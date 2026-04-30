<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestAttendance extends Model
{
    protected $fillable = [
        'employee_id',
        'date',
        'first_in',
        'last_out',
        'status',
        'work_type',
        'actual_work_hours',
    ];

    protected $dates = [
        'date',
    ];

    public function employee()
    {
        return $this->belongsTo(TestEmployee::class, 'employee_id', 'employee_id');
    }
}
