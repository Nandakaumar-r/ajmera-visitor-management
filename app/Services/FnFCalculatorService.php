<?php

namespace App\Services;

class FnFCalculatorService
{
    public function calculateSettlement($data)
    {
        // Basic Salary Components
        $basicSalary = $data['basic_salary'];
        $daysWorked = $data['days_worked'];
        $proportionateSalary = ($basicSalary / 30) * $daysWorked;
        
        // Leave Encashment
        $unusedLeaves = $data['unused_leaves'];
        $leaveEncashment = ($basicSalary / 30) * $unusedLeaves;
        
        // Gratuity (if applicable)
        $yearsOfService = $data['years_of_service'];
        $gratuity = 0;
        if ($yearsOfService >= 5) {
            $gratuity = ($basicSalary * $yearsOfService * 15) / 26;
        }
        
        // Bonus/Incentives
        $bonus = $data['bonus'] ?? 0;
        $incentives = $data['incentives'] ?? 0;
        
        // Deductions
        $taxDeduction = $data['tax_deduction'] ?? 0;
        $loanBalance = $data['loan_balance'] ?? 0;
        $otherDeductions = $data['other_deductions'] ?? 0;
        
        // Notice Period Adjustment
        $noticeRecovery = 0;
        if ($data['notice_period_served'] === false) {
            $noticeRecovery = $basicSalary; // Assuming 1 month notice period
        }
        
        // Calculate Total
        $totalEarnings = $proportionateSalary + $leaveEncashment + $gratuity + $bonus + $incentives;
        $totalDeductions = $taxDeduction + $loanBalance + $otherDeductions + $noticeRecovery;
        $netPayable = $totalEarnings - $totalDeductions;
        
        return [
            'proportionate_salary' => round($proportionateSalary, 2),
            'leave_encashment' => round($leaveEncashment, 2),
            'gratuity' => round($gratuity, 2),
            'bonus' => round($bonus, 2),
            'incentives' => round($incentives, 2),
            'total_earnings' => round($totalEarnings, 2),
            'tax_deduction' => round($taxDeduction, 2),
            'loan_balance' => round($loanBalance, 2),
            'other_deductions' => round($otherDeductions, 2),
            'notice_recovery' => round($noticeRecovery, 2),
            'total_deductions' => round($totalDeductions, 2),
            'net_payable' => round($netPayable, 2)
        ];
    }
}

