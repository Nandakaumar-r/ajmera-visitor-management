<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\LeaveBalance;
use App\Models\LeaveType;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateMonthlyLeaveBalance extends Command
{
    protected $signature = 'leaves:update-monthly-balance';
    protected $description = 'Update leave balances for all employees at the start of month';

    const MONTHLY_LEAVE_CREDIT = 1.5;

    public function handle()
    {
        $this->info('Starting monthly leave balance update...');
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;

        try {
            // Get all users with employee records
            $users = User::whereHas('employee')->get();
            
            // Get all leave types
            $leaveTypes = LeaveType::all();
            
            if ($leaveTypes->isEmpty()) {
                $this->error('No leave types found!');
                return 1;
            }

            $updatedCount = 0;
            foreach ($users as $user) {
                // Skip if user doesn't have an employee record
                if (!$user->employee) {
                    continue;
                }

                foreach ($leaveTypes as $leaveType) {
                    // Check if user already has a leave balance record for this year and type
                    $leaveBalance = LeaveBalance::firstOrCreate(
                        [
                            'user_id' => $user->id,
                            'leave_type_id' => $leaveType->id,
                            'year' => $currentYear,
                        ],
                        [
                            'granted' => 0,
                            'used' => 0,
                        ]
                    );

                    // Only add monthly credit for Earned Leave (EL)
                    if ($leaveType->code === 'EL') {
                        $leaveBalance->granted += self::MONTHLY_LEAVE_CREDIT;
                        $leaveBalance->save();
                    }
                    
                    // Log the update
                    Log::info("Leave balance updated for user {$user->name}", [
                        'user_id' => $user->id,
                        'employee_id' => $user->employee->employee_id,
                        'leave_type' => $leaveType->code,
                        'month' => $currentMonth,
                        'year' => $currentYear,
                        'credit_amount' => $leaveType->code === 'EL' ? self::MONTHLY_LEAVE_CREDIT : 0,
                        'new_balance' => $leaveBalance->granted - $leaveBalance->used
                    ]);
                }

                $updatedCount++;
            }

            $this->info("Successfully updated leave balance for {$updatedCount} employees across all leave types.");
            
            return 0;
        } catch (\Exception $e) {
            $this->error("Error updating leave balances: {$e->getMessage()}");
            Log::error("Error in monthly leave balance update", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return 1;
        }
    }
}
