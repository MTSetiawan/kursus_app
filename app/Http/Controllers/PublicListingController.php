<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PublicListingController extends Controller
{
    public function index()
    {
        $listing = Listing::with('region')->where('status', 'active')->latest()->take(6)->get();
        return response()->json([
            'status' => 'success',
            'data' => $listing
        ]);
    }

    public function show(Region $region, string $slug)
    {
        $listing = Listing::with([
            'region:id,slug,name',
            'user:id,name',
            'user.teacherProfile:id,user_id,whatsapp_number'
        ])
            ->where('slug', $slug)
            ->where('region_id', $region->id)
            ->where('status', 'active')
            ->first();

        if (!$listing) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Listing not found',
            ], 404);
        }

        // normalisasi WA (08… -> 62…)
        $rawWa = optional(optional($listing->user)->teacherProfile)->whatsapp_number;
        $wa    = $rawWa ? preg_replace('/\D+/', '', $rawWa) : null;
        if ($wa && str_starts_with($wa, '0')) {
            $wa = '62' . substr($wa, 1);
        }

        $waLink = $wa
            ? 'https://wa.me/' . $wa . '?text=' . urlencode("Halo, saya tertarik dengan listing: {$listing->title}")
            : null;

        return response()->json([
            'status' => 'success',
            'data'   => [
                'id'          => $listing->id,
                'title'       => $listing->title,
                'slug'        => $listing->slug,
                'category'    => $listing->category,
                'price'       => $listing->price,
                'description' => $listing->description,
                'status'      => $listing->status,
                'created_at'  => $listing->created_at,
                'updated_at'  => $listing->updated_at,

                'region' => [
                    'slug' => $listing->region->slug,
                    'name' => $listing->region->name,
                ],

                // 👇 tambahkan data user (guru)
                'teacher' => [
                    'id'       => $listing->user->id ?? null,
                    'name'     => $listing->user->name ?? null,
                    'whatsapp' => $wa,       // sudah dinormalisasi
                    'wa_link'  => $waLink,   // siap pakai di frontend
                ],
            ],
        ]);
    }

    public function search(Request $request)
    {
        $request->validate([
            'title'       => ['sometimes', 'string', 'max:200'],
            'region'      => ['sometimes', 'integer', 'exists:regions,id'],
            'region_slug' => ['sometimes', 'string', 'max:100'],
            'page'        => ['sometimes', 'integer', 'min:1'],
            'per_page'    => ['sometimes', 'integer', 'min:1', 'max:50'],
        ]);

        $perPage = max(1, min((int)$request->input('per_page', 12), 50));

        // 👉 LOG untuk memastikan server menerima query dari FE
        logger()->info('public-listings.search', $request->only('title', 'region', 'region_slug', 'page', 'per_page'));

        $query = Listing::with('region')
            ->where('status', 'active')
            ->when(
                $request->filled('title'),
                fn($q) =>
                $q->where('title', 'like', '%' . $request->input('title') . '%')
            )
            ->when(
                $request->filled('region'),
                fn($q) =>
                $q->where('region_id', (int)$request->input('region'))
            )
            ->when(
                $request->filled('region_slug'),
                fn($q) =>
                $q->whereHas('region', fn($r) => $r->where('slug', $request->input('region_slug')))
            )
            ->latest();

        $paginator = $query->paginate($perPage)->appends($request->query());

        return response()->json([
            'status'  => 'success',
            'meta'    => [
                'page'      => $paginator->currentPage(),
                'per_page'  => $paginator->perPage(),
                'total'     => $paginator->total(),
                'last_page' => $paginator->lastPage(),
                'has_more'  => $paginator->hasMorePages(),
            ],
            'filters' => $request->only('title', 'region', 'region_slug'),
            'data'    => $paginator->items(),
        ]);
    }
}
