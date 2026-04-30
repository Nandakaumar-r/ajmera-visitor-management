<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'asset_code',
        'description',
        'quantity',
        'warranty_expiry',
        'warranty_details'
    ];

    protected $casts = [
        'warranty_expiry' => 'date'
    ];

    public function category()
    {
        return $this->belongsTo(AssetCategory::class);
    }

    public function requests()
    {
        return $this->hasMany(AssetRequest::class);
    }

    public function maintenanceRecords()
    {
        return $this->hasMany(AssetMaintenanceRecord::class);
    }

    public function isDurable()
    {
        return $this->category->type === 'durable';
    }

    public function isConsumable()
    {
        return $this->category->type === 'consumable';
    }
}
