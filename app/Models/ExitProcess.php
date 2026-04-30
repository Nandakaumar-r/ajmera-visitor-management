<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExitProcess extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'resignation_mail',
        'manager_acknowledged',
        'hr_exit_interview',
        'internal_movement_rejection',
        'last_working_day',
        'notice_period',
        'assets_collected',
        'payroll_clearance',
        'id_card_submitted',
        'farewell_mail_sent',
        'final_settlement_completed',
        'relieving_letter_issued',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
