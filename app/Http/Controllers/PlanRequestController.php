<?php

namespace App\Http\Controllers;

use App\Models\PlanRequest;
use Illuminate\Http\Request;

class PlanRequestController extends Controller
{

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'plan_id' => ['required', 'exists:plans,id'],
            'note' => ['nullable', 'string', 'max:500']
        ]);

        PlanRequest::create([
            'user_id' => $request->user()->id,
            'plan_id' => $data['plan_id'],
            'note' => $data['note'] ?? null,
            'status' => 'requested'
        ]);

        return back()->with('succes', "Permintaan upgrade terkirim. Silahkan kirim bukti TF via WA ke Admin");
    }
}
