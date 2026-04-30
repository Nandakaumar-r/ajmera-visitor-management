<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternalSalaryBreakup extends Model
{
    use HasFactory;
    
    protected $table = 'internal_salary_breakups';
    protected $fillable = [
        'candidate_id',
        'basic_month',
        'basic_annual',
        'hra_month',
        'hra_annual',
        'statutory_bonus_month',
        'statutory_bonus_annual',
        'shift_allowance_month',
        'shift_allowance_annual',
        'internet_allowance_month',
        'internet_allowance_annual',
        'special_allowance_month',
        'special_allowance_annual',
        'lta_month',
        'lta_annual',
        'gross_pay_month',
        'gross_pay_annual',
        'empl_pf_month',
        'empl_pf_annual',
        'empl_esi_month',
        'empl_esi_annual',
        'pt_month',
        'pt_annual',
        'lwf_month',
        'lwf_annual',
        'take_home_month',
        'take_home_annual',
        'empr_pf_month',
        'empr_pf_annual',
        'empr_esi_month',
        'empr_esi_annual',
        'medical_insurance_month',
        'medical_insurance_annual',
        'gratuity_month',
        'gratuity_annual',
        'empr_lwf_month',
        'empr_lwf_annual',
        'joining_bonus',
        'ctc_month',
        'ctc_annual'
    ];

    
}
