<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form16 extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'financial_year',
        'gross_salary',
        'taxable_income',
        'tax_deducted',
        'pan_number',
        'file_path',
        'status'
    ];

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }
}
