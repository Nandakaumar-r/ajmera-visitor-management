<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestEmployee extends Model
{
    protected $fillable = [
        'employee_id',
        'employee_name',
        'employee_email',
        'employee_department',
        'employee_designation',
        'joining_date',
    ];

    protected $dates = [
        'joining_date',
    ];

    public function attendances()
    {
        return $this->hasMany(TestAttendance::class, 'employee_id', 'employee_id');
    }

    public function leaves()
    {
        return $this->hasMany(TestLeave::class, 'employee_id', 'employee_id');
    }

    public function resignation()
    {
        return $this->hasOne(ResignationPrediction::class, 'employee_id', 'employee_id');
    }
}
