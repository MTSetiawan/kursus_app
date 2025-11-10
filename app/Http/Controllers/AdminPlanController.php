<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;

class AdminPlanController extends Controller
{
    public function index()
    {
        $plans = Plan::orderBy('price')->paginate(20);
        return view('admin.plan', compact('plans'));
    }
    public function edit(Plan $plan)
    {
        return view('admin.plan-form', compact('plan'));
    }
    public function update(Request $r, Plan $plan)
    {
        $data = $r->validate([
            'name' => 'required|string|max:50|unique:plans,name,' . $plan->id,
            'price' => 'required|integer|min:0',
            'duration_days' => 'required|integer|min:1',
            'quota_region' => 'nullable|integer|min:0',
        ]);
        $plan->update($data);
        return redirect()->route('admin.plans.index')->with('success', 'Plan diperbarui.');
    }
    public function destroy(Plan $plan)
    {
        $plan->delete();
        return back()->with('success', 'Plan dihapus.');
    }
}
