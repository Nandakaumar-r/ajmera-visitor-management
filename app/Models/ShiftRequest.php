<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShiftRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'current_start_time',
        'current_end_time',
        'requested_start_time',
        'requested_end_time',
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
        'current_start_time' => 'datetime',
        'current_end_time' => 'datetime',
        'requested_start_time' => 'datetime',
        'requested_end_time' => 'datetime',
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

    public function getCurrentShiftAttribute()
    {
        return $this->current_start_time->format('H:i') . ' - ' . $this->current_end_time->format('H:i');
    }

    public function getRequestedShiftAttribute()
    {
        return $this->requested_start_time->format('H:i') . ' - ' . $this->requested_end_time->format('H:i');
    }
}
