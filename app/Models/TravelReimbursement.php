<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TravelReimbursement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'travel_request_id',
        'user_id',
        'company',
        'amount',
        'status',
        'previous_status',
        'description',
        'receipt_files',
        'approved_by',
        'rejection_reason',
        'reimbursement_date',
        'details',
        'manager_email',
        'manager_id',
        'hr_id',
        'cfo_id',
        'finance_id',
       
    ];

    protected $casts = [
        'receipt_files' => 'array',
        'approved_at' => 'datetime',
    ];

    public function travelRequest()
    {
        return $this->belongsTo(TravelRequest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function employee()
    {
        return $this->hasOne(Employee::class, 'employee_email', 'email');
    }

    public function designation()
    {
        return $this->belongsTo(Designations::class, 'employee_designation', 'id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }
}
