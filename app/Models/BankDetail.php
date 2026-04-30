<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankDetail extends Model
{
    use HasFactory;

    protected $table = 'bank_details';

    protected $fillable = [
        'user_id',
        'i_or_n',
        'amount_to_be_paid',
        'sheet_generated_at',
        'emp_name',
        'emp_account_number',
        'email',
        'company_account_number',
        'bank_code',
        'emp_ifsc_code',
        'code',
        'remarks',
        'emp_contact_number',
    ];

    protected $casts = [
        'sheet_generated_at' => 'date',
        'amount_to_be_paid' => 'decimal:2',
    ];
}
