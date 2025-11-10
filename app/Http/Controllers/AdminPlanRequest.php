<?php

namespace App\Http\Controllers;

use App\Models\PlanRequest;
use App\Models\PlanTransaction;
use App\Models\UserPlan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminPlanRequest extends Controller
{
    public function index(Request $request)
    {
        $reqs = PlanRequest::with(['user', 'plan'])
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(20);

        return view('admin.request', compact('reqs'));
    }

    public function approve(PlanRequest $req)
    {
        abort_if($req->status !== 'requested', 400, 'Request sudah di proses');

        DB::transaction(function () use ($req) {
            $now = Carbon::now();
            $plan = $req->plan;

            // cek apakah user masih punya plan aktif
            $active = UserPlan::where('user_id', $req->user_id)
                ->where('plan_id', $req->plan_id)
                ->where('status', 'active')
                ->whereDate('end_date', '>=', $now->toDateString())
                ->latest()
                ->first();

            if ($active) {
                $currentEnd = Carbon::parse($active->end_date);
                $active->update([
                    'end_date' => $currentEnd->addDays($plan->duration_days)->toDateString(),
                ]);
            } else {
                UserPlan::create([
                    'user_id'    => $req->user_id,
                    'plan_id'    => $req->plan_id,
                    'status'     => 'active',
                    'start_date' => $now->toDateString(),
                    'end_date'   => $now->copy()->addDays($plan->duration_days)->toDateString(),
                ]);
            }

            // 🧾 Tambahkan catatan transaksi di sini
            PlanTransaction::create([
                'user_id'       => $req->user_id,
                'plan_id'       => $req->plan_id,
                'price'         => (int) $plan->price,
                'duration_days' => (int) $plan->duration_days,
                'approved_at'   => now(),
            ]);

            $req->update(['status' => 'approved']);
        });

        return back()->with('success', 'Request sudah di setujui.');
    }

    public function rejected(PlanRequest $req)
    {
        abort_if($req->status !== 'requested', 400, 'Request sudah di proses');

        $req->update([
            'status' => 'rejected'
        ]);

        return back()->with('success', 'Request sudah di tolak.');
    }
}
