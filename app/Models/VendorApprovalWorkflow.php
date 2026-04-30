<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VendorApprovalWorkflow extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'vendor_id',
        'initial_approver_id',
        'hr_approver_id',
        'finance_approver_id',
        'cfo_approver_id',
        'payment_processor_id',
    ];

    /**
     * Get the vendor that this workflow belongs to.
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Get the initial approver user.
     */
    public function initialApprover()
    {
        return $this->belongsTo(User::class, 'initial_approver_id');
    }

    /**
     * Get the HR approver user.
     */
    public function hrApprover()
    {
        return $this->belongsTo(User::class, 'hr_approver_id');
    }

    /**
     * Get the finance approver user.
     */
    public function financeApprover()
    {
        return $this->belongsTo(User::class, 'finance_approver_id');
    }

    /**
     * Get the CFO approver user.
     */
    public function cfoApprover()
    {
        return $this->belongsTo(User::class, 'cfo_approver_id');
    }

    /**
     * Get the payment processor user.
     */
    public function paymentProcessor()
    {
        return $this->belongsTo(User::class, 'payment_processor_id');
    }
}
