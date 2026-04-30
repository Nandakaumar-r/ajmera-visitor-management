<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillApprovalHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'bill_id',
        'approver_id',
        'approver_role',
        'status',
        'comments',
        'action_date',
    ];
    protected $casts = [
        'action_date' => 'datetime',
    ];
    // Optional: Define relationship to VendorBill
    public function bill()
    {
        return $this->belongsTo(VendorBill::class, 'bill_id');
    }

    // Optional: Define relationship to approver (User)
    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }
}
