<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\LeaveType;
use App\Models\LeaveBalance;
use Illuminate\Database\Seeder;

class LeaveBalanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $leaveTypes = LeaveType::all();
        $currentYear = date('Y');

        foreach ($users as $user) {
            foreach ($leaveTypes as $leaveType) {
                // Skip if balance already exists
                if (LeaveBalance::where('user_id', $user->id)
                    ->where('leave_type_id', $leaveType->id)
                    ->where('year', $currentYear)
                    ->exists()) {
                    continue;
                }

                // Set default granted days based on leave type
                $granted = match($leaveType->code) {
                    'EL' => 12,  // 1 per month
                    'RH' => 2,   // 2 per year
                    'PL' => 5,   // 5 days paternity leave
                    'CO' => 5,   // 5 compensatory offs
                    default => 0
                };

                LeaveBalance::create([
                    'user_id' => $user->id,
                    'leave_type_id' => $leaveType->id,
                    'granted' => $granted,
                    'consumed' => 0,
                    'balance' => $granted,
                    'year' => $currentYear
                ]);
            }
        }
    }
}
