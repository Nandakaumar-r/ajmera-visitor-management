<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Employee extends Model
{
    use HasFactory, Notifiable;

    protected $primaryKey = 'employee_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'employee_id',
        'employee_name',
        'employee_email',
        'employee_designation',
        'employee_department',
        'employee_date_of_joining',
        'manager_id'
    ];

    protected $casts = [
        'manager_id' => 'string',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'employee_email', 'email');
    }

    public function manager()
    {
        return $this->belongsTo(Manager::class, 'manager_id', 'manager_id');
    }

    public function department()
    {
        return $this->belongsTo(Departments::class, 'employee_department');
    }

    public function designation()
    {
        return $this->belongsTo(Designations::class, 'employee_designation');
    }

    public function subordinates()
    {
        return $this->hasMany(Employee::class, 'manager_id', 'employee_id');
    }

    public function routeNotificationForMail()
    {
        return $this->employee_email;
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'employee_id', 'employee_id');
    }

    public function resignations()
    {
        return $this->hasMany(Resignation::class, 'employee_id', 'employee_id');
    }
    
}
