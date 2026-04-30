<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetRequest extends Model
{
    protected $fillable = [
        'employee_id',
        'asset_id',
        'quantity',
        'justification',
        'status',
        'handover_date',
        'return_date',
        'condition_on_return',
        'remarks',
        'approved_by'
    ];

    protected $casts = [
        'handover_date' => 'date',
        'return_date' => 'date'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function approver()
    {
        return $this->belongsTo(Employee::class, 'approved_by');
    }
}
