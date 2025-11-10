<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\PlanRequest;
use App\Models\PlanTransaction;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $users = User::where('role', 'guru');
        $totalGuru = User::count();
        $listingAktif = Listing::where('status', 'active')->count();
        $reqPending = PlanRequest::where('status', 'requested')->count();

        $todayRevenue  = PlanTransaction::whereDate('approved_at', now())->sum('price');
        $monthRevenue  = PlanTransaction::whereBetween('approved_at', [now()->startOfMonth(), now()->endOfMonth()])->sum('price');

        $byPlanThisMonth = PlanTransaction::with('plan')
            ->whereBetween('approved_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->selectRaw('plan_id, SUM(price) as total')
            ->groupBy('plan_id')
            ->get();

        return view('admin.dashboard', compact(
            'totalGuru',
            'listingAktif',
            'reqPending',
            'todayRevenue',
            'monthRevenue',
            'byPlanThisMonth',
            'users'
        ));
    }
}
