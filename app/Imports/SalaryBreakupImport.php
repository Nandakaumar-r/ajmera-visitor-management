<?php

namespace App\Imports;

use App\Models\InternalSalaryBreakup as SalaryBreakup;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class SalaryBreakupImport implements ToCollection, WithCalculatedFormulas
{

    protected $candidateId;

    public function __construct($candidateId)
    {
        $this->candidateId = $candidateId;
    }
    public function collection(Collection $rows)
    {
        $mapped = [];

        foreach ($rows as $row) {
            $key = trim($row[0]); // Details column
            $monthVal = isset($row[1]) ? floatval(str_replace(',', '', $row[1])) : 0;
            $annualVal = isset($row[2]) ? floatval(str_replace(',', '', $row[2])) : 0;

            switch (strtolower($key)) {
                case 'basic':
                    $mapped['basic_month'] = $monthVal;
                    $mapped['basic_annual'] = $annualVal;
                    break;
                case 'hra':
                    $mapped['hra_month'] = $monthVal;
                    $mapped['hra_annual'] = $annualVal;
                    break;
                case 'statutory bonus':
                    $mapped['statutory_bonus_month'] = $monthVal;
                    $mapped['statutory_bonus_annual'] = $annualVal;
                    break;
                case 'shift allowance':
                    $mapped['shift_allowance_month'] = $monthVal;
                    $mapped['shift_allowance_annual'] = $annualVal;
                    break;
                case 'internet allowance':
                    $mapped['internet_allowance_month'] = $monthVal;
                    $mapped['internet_allowance_annual'] = $annualVal;
                    break;
                case 'special allowance':
                    $mapped['special_allowance_month'] = $monthVal;
                    $mapped['special_allowance_annual'] = $annualVal;
                    break;
                case 'lta':
                    $mapped['lta_month'] = $monthVal;
                    $mapped['lta_annual'] = $annualVal;
                    break;
                case 'gross pay':
                    $mapped['gross_pay_month'] = $monthVal;
                    $mapped['gross_pay_annual'] = $annualVal;
                    break;
                case 'empl pf':
                    $mapped['empl_pf_month'] = $monthVal;
                    $mapped['empl_pf_annual'] = $annualVal;
                    break;
                case 'empl esi':
                    $mapped['empl_esi_month'] = $monthVal;
                    $mapped['empl_esi_annual'] = $annualVal;
                    break;
                case 'pt':
                    $mapped['pt_month'] = $monthVal;
                    $mapped['pt_annual'] = $annualVal;
                    break;
                case 'lwf':
                    // LWF is used twice — assume 1st is employee, 2nd is employer
                    if (!isset($mapped['lwf_month'])) {
                        $mapped['lwf_month'] = $monthVal;
                        $mapped['lwf_annual'] = $annualVal;
                    } else {
                        $mapped['empr_lwf_month'] = $monthVal;
                        $mapped['empr_lwf_annual'] = $annualVal;
                    }
                    break;
                case 'take home':
                    $mapped['take_home_month'] = $monthVal;
                    $mapped['take_home_annual'] = $annualVal;
                    break;
                case 'empr pf':
                    $mapped['empr_pf_month'] = $monthVal;
                    $mapped['empr_pf_annual'] = $annualVal;
                    break;
                case 'empr esi':
                    $mapped['empr_esi_month'] = $monthVal;
                    $mapped['empr_esi_annual'] = $annualVal;
                    break;
                case 'medical insurance':
                    $mapped['medical_insurance_month'] = $monthVal;
                    $mapped['medical_insurance_annual'] = $annualVal;
                    break;
                case 'gratuity':
                    $mapped['gratuity_month'] = $monthVal;
                    $mapped['gratuity_annual'] = $annualVal;
                    break;
                case 'retaintion bonus/joining bonus':
                    $mapped['joining_bonus'] = $annualVal; // assuming only annual provided
                    break;
                case 'cost to company':
                    $mapped['ctc_month'] = $monthVal;
                    $mapped['ctc_annual'] = $annualVal;
                    break;
            }
        }
        $mapped['candidate_id'] = $this->candidateId;

        SalaryBreakup::updateOrCreate(
            ['candidate_id' => $this->candidateId],
            $mapped
        );
    }
}
