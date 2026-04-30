<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResignationPrediction extends Model
{
    protected $fillable = [
        'employee_id',
        'risk_level',
        'risk_factors',
        'last_prediction_at',
    ];

    protected $casts = [
        'risk_factors' => 'array',
        'last_prediction_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(TestEmployee::class, 'employee_id', 'employee_id');
    }
}
