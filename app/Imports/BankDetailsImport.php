<?php

namespace App\Imports;

use App\Models\BankDetail;
use App\Models\ExternalEmpBankDetail;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BankDetailsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new ExternalEmpBankDetail([
            'i_or_n'                 => $row['i_or_n'],
            'amount_to_be_paid'     => $row['amount_to_be_paid'],
            'sheet_generated_at'    => $row['date_of_sheet_generation'],
            'emp_id'                => $row['emp_id'],
            'emp_name'              => $row['emp_name'],
            'emp_account_number'    => $row['emp_account_number'],
            'email'                 => $row['defult_email'],
            'company_account_number'=> $row['company_account_no'],
            'bank_code'             => $row['bank_code_column_should_be_blank'],
            'emp_ifsc_code'         => $row['emp_ifsc_code'],
            'code'                  => $row['code_11_to_be_mentioned_defult_mode'],
            'remarks'               => $row['remarks_column_to_be_blank'],
            'emp_contact_number'    => $row['emp_contact_number'],
        ]);
    }

    private function formatDate($value)
    {
        try {
            return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
        } catch (\Exception $e) {
            return Carbon::parse($value);
        }
    }
}

