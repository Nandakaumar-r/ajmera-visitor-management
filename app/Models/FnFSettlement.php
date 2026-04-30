<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FnFSettlement extends Model
{
    use HasFactory;
    protected $fillable = [
        'resignation_id',
        'basic_salary',
        'days_worked',
        'proportionate_salary',
        'unused_leaves',
        'leave_encashment',
        'gratuity',
        'bonus',
        'incentives',
        'tax_deduction',
        'loan_balance',
        'notice_recovery',
        'other_deductions',
        'total_earnings',
        'total_deductions',
        'net_payable',
        'calculation_details',
        'processed_at',
        'processed_by',
        'status',
        'remarks'
    ];

    protected $table = 'fnf_settlements';

    protected $casts = [
        'processed_at' => 'datetime',
        'calculation_details' => 'array'
    ];

    public function resignation()
    {
        return $this->belongsTo(Resignation::class);
    }

    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
