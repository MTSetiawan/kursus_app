<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\Plan;
use App\Models\Region;
use App\Models\UserPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

use Illuminate\Routing\Controller as BaseController;

class ListingController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware(['auth', 'teacher.profile']);
    // }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $listing = Listing::with('region')->where('user_id', auth()->id())->latest()->paginate(10);
        $listings = Listing::with('region')
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        $activePlan = UserPlan::with('plan')
            ->where('user_id', auth()->id())
            ->where('status', 'active')
            ->whereDate('end_date', '>=', now()->toDateString())
            ->latest()
            ->first();

        $planName = $activePlan?->plan?->name ?? 'bronze';
        $limit    = $activePlan?->plan?->quota_region ?? 3;
        $endDate    = $activePlan?->end_date;
        $used     = Listing::where('user_id', auth()->id())
            ->where('status', 'active')
            ->count();

        $plans = Plan::orderBy('price')->get();

        return view('teacher.dashboard', compact(
            'listings',
            'planName',
            'limit',
            'used',
            'plans',
            'activePlan',
            'endDate'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $regions = Region::where('type', 'kecamatan')
            ->orderBy('name')
            ->get();

        $listing = new Listing();

        // Kalau view juga butuh dropdown kota (opsional)
        // $cities = Region::where('type','kota')->orderBy('name')->get();

        return view('listings.create', compact('regions', 'listing')); // , 'cities'
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:150',
            'category'    => 'required|string|max:50',
            'price'       => 'nullable|integer|min:0',
            'region_id'   => 'required|exists:regions,id',
            'description' => 'nullable|string|max:5000',
            'status'      => 'sometimes|in:active,inactive',
        ]);

        $region = Region::where('id', $data['region_id'])
            ->where('type', 'kecamatan')
            ->firstOrFail();

        $cityId = (int) $region->parent_id;

        $plan = $this->currentPlan();
        $planName = $plan?->name ?? 'bronze';
        $quota    = $plan?->quota_region ?? 3;
        $unlimited = is_null($plan?->quota_region) || (int)$plan?->quota_region === 0;

        if (!$unlimited) {
            $activeCountInCity = $this->countActiveUniqueDistrictsInCity(auth()->id(), $cityId);

            $activeCountInCity = Listing::where('user_id', auth()->id())
                ->where('status', 'active')
                ->where('region_id', $region->id)
                ->exists();

            $nextCount = $activeCountInCity + ($activeCountInCity ? 0 : 1);
            abort_if($nextCount > $quota, 403, "Kuota {$planName} habis (maks {$quota} kecamatan dalam kota ini). Upgrade plan untuk menambah.");
        }

        $data['user_id'] = auth()->id();
        $data['slug'] = Str::slug($data['title'] . '-' . Str::random(6));

        Listing::create($data);

        return redirect()->route('teacher.dashboard')->with('succes', 'Listing berhasil dibuat');
    }

    public function edit(Listing $listing)
    {
        abort_unless($listing->user_id === auth()->id(), 403);

        $regions = Region::where('type', 'kecamatan')
            ->orderBy('name')
            ->get();

        return view('listings.create', compact('listing', 'regions'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Listing $listing)
    {
        abort_unless($listing->user_id === auth()->id(), 403);

        $data = $request->validate([
            'title'       => 'required|string|max:150',
            'category'    => 'required|string|max:50',
            'price'       => 'nullable|integer|min:0',
            'region_id'   => 'required|exists:regions,id',
            'description' => 'nullable|string|max:5000',
            'status'      => 'required|in:active,inactive',
        ]);

        $targetRegion = Region::where('id', $data['region_id'])
            ->where('type', 'kecamatan')
            ->firstOrFail();
        $targetCityId = (int) $targetRegion->parent_id;

        if ($data['status'] === 'active') {
            $plan      = $this->currentPlan();
            $planName  = $plan?->name ?? 'Bronze';
            $quota     = $plan?->quota_region ?? 3;
            $unlimited = is_null($plan?->quota_region) || (int)$plan?->quota_region === 0;

            if (!$unlimited) {
                $activeCountInTargetCity = $this->countActiveUniqueDistrictsInCity(
                    auth()->id(),
                    $targetCityId,
                    $listing->id
                );

                $alreadyActiveThisDistrict = Listing::where('user_id', auth()->id())
                    ->where('status', 'active')
                    ->where('id', '!=', $listing->id)
                    ->where('region_id', $targetRegion->id)
                    ->exists();

                $nextCount = $activeCountInTargetCity + ($alreadyActiveThisDistrict ? 0 : 1);

                abort_if(
                    $nextCount > (int)$quota,
                    403,
                    "Kuota {$planName} habis (maks {$quota} kecamatan aktif dalam kota ini). Upgrade plan untuk menambah."
                );
            }
        }

        if ($listing->title !== $data['title']) {
            $data['slug'] = Str::slug($data['title'] . '-' . Str::random(6));
        }

        $listing->update($data);

        return redirect()
            ->route('listings.index')
            ->with('success', 'Listing diperbarui.');
    }

    public function destroy(Listing $listing)
    {
        abort_unless($listing->user_id === auth()->id(), 403);
        $listing->delete();
        return back()->with('success', 'Listing dihapus.');
    }

    private function currentPlan()
    {
        return optional(auth()->user()->activePlan?->plan);
    }

    private function countActiveUniqueDistrictsInCity(int $userId, int $cityId, ?int $exceptListingId = null): int
    {
        return (int) Listing::query()
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->when($exceptListingId, fn($q) => $q->where('id', '!=', $exceptListingId))
            ->whereHas('region', fn($q) => $q->where('type', 'kecamatan')->where('parent_id', $cityId))
            ->distinct('region_id')
            ->count('region_id');
    }
}
