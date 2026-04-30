<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'bill_id',
        'payment_method',
        'transaction_id',
        'amount',
        'payment_date',
        'notes',
        'status',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    /**
     * Get the bill that owns this payment.
     */
    public function bill()
    {
        return $this->belongsTo(VendorBill::class, 'bill_id');
    }
}
