<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestLeave extends Model
{
    protected $fillable = [
        'employee_id',
        'type',
        'start_date',
        'end_date',
        'reason',
        'status',
    ];

    protected $dates = [
        'start_date',
        'end_date',
    ];

    public function employee()
    {
        return $this->belongsTo(TestEmployee::class, 'employee_id', 'employee_id');
    }
}
