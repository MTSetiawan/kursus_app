<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use Illuminate\Http\Request;

class ListingAdminController extends Controller
{
    public function index(Request $request)
    {
        $listing = Listing::with(['user', 'region'])
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->when($request->region_id, fn($q, $id) => $q->where('region_id', $id))
            ->latest()->paginate(20);

        return view('admin.listing', compact('listing'));
    }

    public function updateStatus(Request $r, Listing $listing)
    {
        $data = $r->validate(['status' => ['required', 'in:active,inactive']]);
        $listing->update(['status' => $data['status']]);
        return back()->with('success', 'Status listing diperbarui.');
    }

    public function destroy(Listing $listing)
    {
        $listing->delete();
        return back()->with('success', 'Listing dihapus.');
    }
}
