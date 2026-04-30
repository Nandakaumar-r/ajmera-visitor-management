<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CabinBooking extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'cabin_id',
        'user_id',
        'start_time',
        'end_time',
        'purpose',
        'status',
        'notes',
        'meeting_minutes',
        'teams_meeting_link',
        'teams_meeting_id'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function cabin(): BelongsTo
    {
        return $this->belongsTo(Cabin::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function attendees(): HasMany
    {
        return $this->hasMany(BookingAttendee::class, 'booking_id');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(BookingNote::class, 'booking_id');
    }

    public function getTeamsMeetingUrlAttribute()
    {
        return $this->teams_meeting_link ?? "https://teams.microsoft.com/l/meetup-join/" . $this->teams_meeting_id;
    }
}
