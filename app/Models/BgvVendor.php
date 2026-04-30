<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BgvVendor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'contact_person', 'email', 'phone', 
        'escalation_person', 'escalation_email', 'escalation_phone',
        'tat_days', 'cost_structure', 'is_active'
    ];

    protected $casts = [
        'cost_structure' => 'array',
        'is_active' => 'boolean'
    ];

    public function projects()
    {
        return $this->hasMany(BgvProject::class, 'vendor_id');
    }

    public function bgvRequests()
    {
        return $this->hasMany(BgvRequest::class, 'vendor_id');
    }

    public function invoices()
    {
        return $this->hasMany(BgvInvoice::class, 'vendor_id');
    }
}
