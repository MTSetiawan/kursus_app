<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\Plan;
use App\Models\UserPlan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function wa(Plan $plan)
    {
        $adminPhone = config('app.admin_wa'); // .env: ADMIN_WA=62812xxxxxxx
        $msg = "Halo Admin, saya ingin upgrade ke paket {$plan->name} (Rp "
            . number_format($plan->price, 0, ',', '.')
            . "). Akun: " . (auth()->guard()->check() ? auth()->user()->name : 'Guest') . " (" . (auth()->guard()->check() ? auth()->user()->email : 'N/A') . ").";

        $to  = preg_replace('/\D+/', '', $adminPhone ?: '');
        $url = $to
            ? "https://wa.me/{$to}?text=" . urlencode($msg)
            : "https://wa.me/?text=" . urlencode($msg);

        return redirect()->away($url);
    }
}
