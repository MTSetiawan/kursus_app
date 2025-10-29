<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserPlan;
use App\Models\Plan;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminTeacherController extends Controller
{
    public function index()
    {
        $teachers = User::with(['teacherProfile', 'userPlans.plan'])
            ->where('role', 'guru')
            ->get()
            ->map(function ($user) {
                $activePlan = $user->userPlans
                    ->where('status', 'active')
                    ->sortByDesc('end_date')
                    ->first();

                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'plan' => $activePlan?->plan?->name ?? '-',
                    'plan_id' => $activePlan?->plan_id,
                    'start_date' => $activePlan?->start_date,
                    'end_date' => $activePlan?->end_date,
                    'status' => $activePlan?->status ?? '-',
                ];
            });

        $plans = Plan::all();

        return view('admin.teacher-plans', compact('teachers', 'plans'));
    }

    public function removePlan($userId)
    {
        $plan = UserPlan::where('user_id', $userId)
            ->where('status', 'active')
            ->latest()
            ->first();

        if (!$plan) {
            return back()->with('error', 'Guru ini tidak memiliki plan aktif.');
        }

        $plan->update(['status' => 'expired']);
        return back()->with('success', 'Plan guru telah dihapus.');
    }

    public function updatePlan(Request $request, $userId)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
        ]);

        $plan = Plan::find($request->plan_id);
        if (!$plan) {
            return back()->with('error', 'Plan tidak ditemukan.');
        }

        DB::transaction(function () use ($plan, $userId) {
            // expire all existing plans
            UserPlan::where('user_id', $userId)
                ->where('status', 'active')
                ->update(['status' => 'expired']);

            // assign new plan
            UserPlan::create([
                'user_id' => $userId,
                'plan_id' => $plan->id,
                'status' => 'active',
                'start_date' => now(),
                'end_date' => now()->addDays($plan->duration_days),
            ]);
        });

        return back()->with('success', 'Plan guru berhasil diganti ke ' . $plan->name);
    }

    public function extendPlan($userId)
    {
        $plan = UserPlan::where('user_id', $userId)
            ->where('status', 'active')
            ->latest()
            ->first();

        if (!$plan) {
            return back()->with('error', 'Guru ini tidak memiliki plan aktif.');
        }

        $plan->update([
            'end_date' => Carbon::parse($plan->end_date)->addDays(7)
        ]);

        return back()->with('success', 'Plan guru diperpanjang 7 hari.');
    }
}
