<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExternalReimbursement extends Model
{
    use HasFactory;

    protected $table = 'external_reimbursements';

    protected $fillable = [
        'name',
        'manager_name',
        'emp_id',
        'department',
        'designation',
        'business_purpose',
        'from',
        'to',
        'amount',
        'reimbursement_details',
        'status',
        'manager_approval_attachment',
        'approved_by',
        'approved_at',
        'submitted_by',
        'hr_status', 'finance_status', 'cfo_status', 'final_status',
        'remarks',
        'bills_attachment',
        'project',
        'client',
    ];

    protected $casts = [
        'from' => 'date',
        'to' => 'date',
        'approved_at' => 'datetime',
        'reimbursement_details' => 'array',
        'bills_attachment' => 'array',
    ];

    // Optional: relationship to user who approved
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
