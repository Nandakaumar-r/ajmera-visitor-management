<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkTypeRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'current_work_type',
        'requested_work_type',
        'effective_from',
        'reason',
        'status',
        'rejection_reason',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'effective_from' => 'date',
        'approved_at' => 'datetime',
    ];

    public const WORK_TYPES = [
        'remote' => 'Remote',
        'hybrid' => 'Hybrid',
        'on-site' => 'On-Site',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'approved' => 'bg-green-100 text-green-800',
            'rejected' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getCurrentWorkTypeDisplayAttribute()
    {
        return self::WORK_TYPES[$this->current_work_type] ?? $this->current_work_type;
    }

    public function getRequestedWorkTypeDisplayAttribute()
    {
        return self::WORK_TYPES[$this->requested_work_type] ?? $this->requested_work_type;
    }
}
