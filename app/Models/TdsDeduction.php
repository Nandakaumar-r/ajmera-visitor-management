<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TdsDeduction extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'certificate_id',
        'bill_id',
        'deduction_percentage',
        'deduction_amount',
        'after_tds',
        'paid_amount',
        'applied_from',
        'applied_to',
    ];

    protected $casts = [
        'deduction_percentage' => 'decimal:2',
        'deduction_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'applied_from' => 'date',
        'applied_to' => 'date',
    ];

    // Relationship to the vendor
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    // Relationship to the Lower TDS Certificate
    public function certificate()
    {
        return $this->belongsTo(LowerTdsCertificate::class, 'certificate_id');
    }

    // Relationship to the bill
    public function bill()
    {
        return $this->belongsTo(VendorBill::class, 'bill_id');
    }
}
