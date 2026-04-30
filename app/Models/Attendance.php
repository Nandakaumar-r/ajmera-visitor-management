<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Employee;
use Carbon\Carbon;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'date',
        'first_in',
        'last_out',
        'late_in',
        'early_out',
        'total_work_hours',
        'actual_work_hours',
        'break_hours',
        'status',
        'shift',
        'work_mode',
        'latitude',
        'longitude',
        'start_time',
        'end_time',
        'total_hours',
        'location_address',
        'is_reminder_sent',
        'last_reminder_sent_at',
        'remarks',
        'work_type',
        'approved_by',
        'approved_at'
    ];

    protected $casts = [
        'date' => 'date',
        'first_in' => 'datetime',
        'last_out' => 'datetime',
        'late_in' => 'boolean',
        'early_out' => 'boolean',
        'total_work_hours' => 'float',
        'actual_work_hours' => 'float',
        'break_hours' => 'float',
        'total_hours' => 'float',
        'is_reminder_sent' => 'boolean',
        'last_reminder_sent_at' => 'datetime',
        'latitude' => 'float',
        'longitude' => 'float',
        'approved_at' => 'datetime'
    ];

    // Define the relationship to the Employee model
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'employee_id', 'id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // Helper methods for work mode
    public function isWFH()
    {
        return $this->work_mode === 'WFH';
    }

    public function isWFO()
    {
        return $this->work_mode === 'WFO';
    }

    // Calculate total hours worked
    public function calculateTotalHours()
    {
        if ($this->start_time && $this->end_time) {
            $start = Carbon::parse($this->start_time);
            $end = Carbon::parse($this->end_time);
            return round($end->diffInMinutes($start) / 60, 2);
        }
        return 0;
    }

    // Check if reminder should be sent
    public function shouldSendReminder()
    {
        if ($this->is_reminder_sent) {
            $lastReminder = Carbon::parse($this->last_reminder_sent_at);
            return $lastReminder->diffInHours(now()) >= 24;
        }
        return true;
    }

    // Update location details
    public function updateLocation($latitude, $longitude, $address = null)
    {
        $this->update([
            'latitude' => $latitude,
            'longitude' => $longitude,
            'location_address' => $address
        ]);
    }
}
