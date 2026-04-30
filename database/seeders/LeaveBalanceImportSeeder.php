<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\LeaveType;
use App\Models\LeaveBalance;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class LeaveBalanceImportSeeder extends Seeder
{
    public function run(): void
    {
        $csvFile = fopen(base_path("leaveBalanceAsOnDay-1736327557833.csv"), "r");
        
        // Skip first two header rows
        fgetcsv($csvFile);
        fgetcsv($csvFile);
        
        $leaveType = LeaveType::firstOrCreate([
            'name' => 'Previous Balance',
            'code' => 'PB',
            'description' => 'Leave balance carried forward from previous year'
        ]);

        while (($data = fgetcsv($csvFile)) !== FALSE) {
            $employeeNo = trim($data[1]);
            $employeeName = trim($data[2]);
            $balance = floatval($data[3]);

            // Find or create user
            $user = User::firstOrCreate(
                ['employee_no' => $employeeNo],
                [
                    'name' => $employeeName,
                    'email' => strtolower(str_replace(' ', '.', $employeeName)) . '@company.com',
                    'password' => Hash::make('password123'),
                    'is_first_login' => true
                ]
            );
            
            // Create or update leave balance
            LeaveBalance::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'leave_type_id' => $leaveType->id,
                    'year' => 2024
                ],
                [
                    'granted' => $balance,
                    'consumed' => 0,
                    'balance' => $balance
                ]
            );
        }

        fclose($csvFile);
    }
}
