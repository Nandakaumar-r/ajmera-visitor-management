<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use App\Models\LeaveBalance;
use App\Models\LeaveType;
use App\Models\Employee;
use App\Models\Leave;
use App\Notifications\LeaveSubmitted;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\DB;

class LeaveService
{
    const MONTHLY_LEAVE_CREDIT = 1.5;

    public function calculateLeaveBalance(User $employee)
    {
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;
        $today = Carbon::now();

        // Get the employee's join date or start of year, whichever is later
        $employeeJoinDate = $employee->join_date ? Carbon::parse($employee->join_date) : null;
        $startOfYear = Carbon::create($currentYear, 1, 1)->startOfDay();
        $effectiveStartDate = $employeeJoinDate && $employeeJoinDate > $startOfYear
            ? $employeeJoinDate
            : $startOfYear;

        // Calculate completed months (only count months that have started)
        $completedMonths = $effectiveStartDate->copy()->startOfMonth()
            ->diffInMonths($today->copy()->startOfMonth()) + 1;

        // Calculate total leaves credited this year
        $totalLeavesCredited = $completedMonths * self::MONTHLY_LEAVE_CREDIT;

        // Get detailed leave breakdown
        $leaveDetails = $this->getDetailedLeaveBreakdown($employee);
        $leavesTaken = $leaveDetails->sum('total_days');

        // Calculate remaining balance
        $balance = $totalLeavesCredited - $leavesTaken;

        // Calculate next credit date
        $nextCreditDate = $today->copy()->addMonthNoOverflow()->startOfMonth();

        return [
            'total_credited' => round($totalLeavesCredited, 1),
            'leaves_taken' => round($leavesTaken, 1),
            'balance' => round($balance, 1),
            'monthly_credit' => self::MONTHLY_LEAVE_CREDIT,
            'completed_months' => $completedMonths,
            'leave_details' => $leaveDetails,
            'next_credit_date' => $nextCreditDate->format('Y-m-d'),
            'days_until_next_credit' => (int) $today->diffInDays($nextCreditDate)
        ];
    }

    public function getLeaveBreakdown(User $employee)
    {
        $balance = $this->calculateLeaveBalance($employee);
        $currentMonth = Carbon::now()->format('F');
        $upcomingLeaves = $this->getUpcomingLeaves($employee);
        $nextCreditDate = Carbon::parse($balance['next_credit_date']);

        return [
            'current_month' => $currentMonth,
            'monthly_accrual' => self::MONTHLY_LEAVE_CREDIT,
            'year_to_date_balance' => $balance['balance'],
            'leaves_taken' => $balance['leaves_taken'],
            'total_credited' => $balance['total_credited'],
            'upcoming_credit' => self::MONTHLY_LEAVE_CREDIT,
            'next_credit_date' => $nextCreditDate->format('d M Y'),
            'days_until_next_credit' => $balance['days_until_next_credit'],
            'year_end_projection' => $this->calculateYearEndProjection($balance),
            'leave_details_by_type' => $balance['leave_details'],
            'upcoming_leaves' => $upcomingLeaves
        ];
    }

    private function calculateYearEndProjection($balance)
    {
        $currentMonth = Carbon::now()->month;
        $remainingMonths = 12 - $currentMonth;
        $projectedCredits = $remainingMonths * self::MONTHLY_LEAVE_CREDIT;

        return round($balance['balance'] + $projectedCredits, 1);
    }

    private function getDetailedLeaveBreakdown(User $employee)
    {
        $currentYear = Carbon::now()->year;

        return $employee->leaves()
            ->whereYear('from_date', $currentYear)
            ->select([
                'id',
                'from_date',
                'to_date',
                'session_1',
                'session_2',
                'leave_type',
                DB::raw('
                    CASE 
                        WHEN from_date = to_date THEN
                            CASE 
                                WHEN session_1 IS NOT NULL AND session_2 IS NOT NULL THEN 1
                                WHEN session_1 IS NOT NULL OR session_2 IS NOT NULL THEN 0.5
                                ELSE 1
                            END
                        ELSE 
                            CASE 
                                WHEN session_1 IS NOT NULL AND session_2 IS NULL THEN DATEDIFF(to_date, from_date) + 0.5
                                WHEN session_1 IS NULL AND session_2 IS NOT NULL THEN DATEDIFF(to_date, from_date) + 0.5
                                ELSE DATEDIFF(to_date, from_date) + 1
                            END
                    END AS days_count
                ')
            ])
            ->get()
            ->groupBy('leave_type')
            ->map(function ($leaves) {
                return [
                    'total_days' => $leaves->sum('days_count'),
                    'leaves' => $leaves
                ];
            });
    }

    private function getUpcomingLeaves(User $employee)
    {
        return $employee->leaves()
            ->where('status', 'approved')
            ->where('from_date', '>', Carbon::now())
            ->select([
                'id',
                'from_date',
                'to_date',
                'session_1',
                'session_2',
                'leave_type',
                DB::raw('
                    CASE 
                        WHEN from_date = to_date THEN
                            CASE 
                                WHEN session_1 IS NOT NULL AND session_2 IS NOT NULL THEN 1
                                WHEN session_1 IS NOT NULL OR session_2 IS NOT NULL THEN 0.5
                                ELSE 1
                            END
                        ELSE 
                            CASE 
                                WHEN session_1 IS NOT NULL AND session_2 IS NULL THEN DATEDIFF(to_date, from_date) + 0.5
                                WHEN session_1 IS NULL AND session_2 IS NOT NULL THEN DATEDIFF(to_date, from_date) + 0.5
                                ELSE DATEDIFF(to_date, from_date) + 1
                            END
                    END AS days_count
                ')
            ])
            ->orderBy('from_date')
            ->get()
            ->map(function ($leave) {
                return [
                    'id' => $leave->id,
                    'from_date' => $leave->from_date,
                    'to_date' => $leave->to_date,
                    'days_count' => $leave->days_count,
                    'leave_type' => $leave->leave_type,
                    'is_half_day' => ($leave->session_1 !== null || $leave->session_2 !== null)
                ];
            });
    }

    // public function updateLeaveBalance($leave)
    // {
    //     // Update leave balance
    //     $leaveType = LeaveType::where('code', $leave->leave_type)->first();
    //     $leaveBalance = LeaveBalance::where('leave_type_id', $leaveType->id)->where('user_id', $leave->user_id)->where('year', Carbon::now()->year)->first();
    //     // Get total difference between from_date and to_date
    //     $totalDays = $leave->to_date->diffInDays($leave->from_date) + 1;
    //     $leaveBalance->consumed =  $totalDays;
    //     $leaveBalance->balance = $leaveBalance->granted - $leaveBalance->consumed;
    //     $leaveBalance->save();
    // }

    public function updateLeaveBalance($leave)
    {
        $leaveType = LeaveType::where('code', $leave->leave_type)->first();
        dd($leaveType);die;
        if (!$leaveType) {
            throw new \Exception('Invalid leave type code: ' . $leave->leave_type);
        }

        $year = Carbon::now()->year;
        $leaveBalance = LeaveBalance::firstOrNew([
            'leave_type_id' => $leaveType->id,
            'user_id' => $leave->user_id,
            'year' => $year,
        ]);

        $totalDays = $leave->to_date->diffInDays($leave->from_date) + 1;
        $leaveBalance->consumed = $totalDays;
        $leaveBalance->balance = ($leaveBalance->granted ?? 0) - $leaveBalance->consumed;
        $leaveBalance->save();
    }
}
