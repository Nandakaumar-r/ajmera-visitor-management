<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorBankDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'bank_name',
        'account_holder_name',
        'account_number',
        'ifsc_code',
        'upi_id',
        'is_primary',
    ];

    /**
     * Get the vendor that owns the bank detail.
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
