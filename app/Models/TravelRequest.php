<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TravelRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'transport_mode',
        'number_of_travelers',
        'destination',
        'travel_reason',
        'accommodation_details',
        'status',
        'manager_id',
        'manager_comments',
        'cfo_id',
        'cfo_comments',
        'booking_details',
        'estimated_cost',
        'actual_cost',
        'is_international',
        'is_group_travel',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'accommodation_details' => 'array',
        'booking_details' => 'array',
        'manager_approved_at' => 'datetime',
        'cfo_approved_at' => 'datetime',
        'is_international' => 'boolean',
        'is_group_travel' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function cfo()
    {
        return $this->belongsTo(User::class, 'cfo_id');
    }

    public function travelers()
    {
        return $this->hasMany(Traveler::class);
    }

    public function reimbursements()
    {
        return $this->hasMany(TravelReimbursement::class);
    }

    public function isPending()
    {
        return in_array($this->status, ['pending_manager', 'pending_cfo']);
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
