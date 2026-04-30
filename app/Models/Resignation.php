<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resignation extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'manager_id',
        'resignation_date',
        'reason',
        'additional_details',
        'status',
        'manager_last_working_day',
        'resignation_reason',
    ];

    // Resignation.php

    public function exitProcess()
    {
        return $this->hasOne(ExitProcess::class, 'employee_id', 'employee_id');
    }


    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }
}
