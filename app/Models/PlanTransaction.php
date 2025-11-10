<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanTransaction extends Model
{
    protected $fillable = [
        'user_id',
        'plan_id',
        'price',
        'duration_days',
        'approved_at'
    ];

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
