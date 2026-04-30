<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vendor extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $casts = [
        'pan_verified' => 'boolean',
        'gst_verified' => 'boolean',
    ];

    protected $fillable = [
        'name',
        'type',
        'contact_person',
        'email',
        'phone',
        'pan_number',
        'pan_verified',
        'gst_number',
        'gst_verified',
        'tan_number',
        'address',
        'city',
        'state',
        'pincode',
        'nature_of_work',
        'status',
        'onboarding_status',
        'website',
        'msme_certificate_path',
        'gst_exemption_certificate_path',
        'vendor_contact_person',
        'vendor_phone',
        'vendor_email',
    ];

    /**
     * Get the bank details for the vendor.
     */
    public function bankDetails()
    {
        return $this->hasMany(VendorBankDetail::class);
    }

    /**
     * Get the documents for the vendor.
     */
    public function documents()
    {
        return $this->hasMany(VendorDocument::class);
    }

    /**
     * Get the bills for the vendor.
     */
    public function bills()
    {
        return $this->hasMany(VendorBill::class);
    }

    /**
     * Get the user for the vendor.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the approval workflow for the vendor.
     */
    public function approvalWorkflow()
    {
        return $this->hasOne(VendorApprovalWorkflow::class);
    }

    /**
     * Get the contacts for the vendor.
     */
    public function contacts()
    {
        return $this->hasMany(VendorContact::class);
    }
}
