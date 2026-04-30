<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VendorBill extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'vendor_bills';

    protected $fillable = [
        'vendor_id',
        'bill_number',
        'bill_date',
        'due_date',
        'payable_date',
        'amount',
        'tax_amount',
        'total_amount',
        'billing_period_start',
        'billing_period_end',
        'description',
        'file_path',
        'document_path',
        'document_name',
        'document_mime_type',
        'document_size',
        'status',
        'payment_status',
        'payment_notes',
        'payment_date',
        'rejection_reason',
        'approved_by',
        'approved_at',
        'gst_type',
        'is_credit_note',
        'original_bill_id',
        'credit_note_number',
        'credit_note_amount',
        'credit_note_date',
        'credit_note_reason',
        'credit_note_file_path',
        'credit_note',
        'po_number',
        'company',
        'invoice_type',
    ];

    protected $casts = [
        'bill_date' => 'date',
        'due_date' => 'date',
        'payable_date' => 'date',
        'billing_period_start' => 'date',
        'billing_period_end' => 'date',
        'amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'payment_date' => 'datetime',
        'credit_note_date' => 'date',
        'is_credit_note' => 'boolean',
    ];

    /**
     * Get the vendor that owns the bill.
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Get the original bill that this credit note is for.
     */
    public function originalBill()
    {
        return $this->belongsTo(VendorBill::class, 'original_bill_id');
    }

    /**
     * Get all credit notes for this bill.
     */
    public function creditNotes()
    {
        return $this->hasMany(VendorBill::class, 'original_bill_id');
    }

    /**
     * Get the status history for the bill.
     */
    public function statusHistory()
    {
        return $this->hasMany(BillStatusHistory::class, 'bill_id');
    }
    
    /**
     * Get the approval history for the bill.
     */
    public function approvalHistory()
    {
        return $this->hasMany(BillApprovalHistory::class, 'bill_id');
    }
}
