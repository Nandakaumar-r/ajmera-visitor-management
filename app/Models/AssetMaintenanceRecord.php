<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetMaintenanceRecord extends Model
{
    protected $fillable = [
        'asset_id',
        'maintenance_date',
        'description',
        'cost',
        'vendor',
        'next_maintenance_due'
    ];

    protected $casts = [
        'maintenance_date' => 'date',
        'next_maintenance_due' => 'date',
        'cost' => 'decimal:2'
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}
