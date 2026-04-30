<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TestEmployeeSeeder extends Seeder
{
    public function run()
    {
        // Create a test employee with concerning patterns
        DB::table('test_employees')->insert([
            'employee_id' => 'EMP001',
            'employee_name' => 'John Test',
            'employee_email' => 'john.test@example.com',
            'employee_department' => 'Engineering',
            'employee_designation' => 'Senior Developer',
            'joining_date' => '2023-01-01',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create attendance records for the last month
        for ($i = 30; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            
            // Create some concerning patterns (late arrivals, early departures)
            $isLate = $i % 3 == 0; // Every third day is late
            $isEarlyDeparture = $i % 4 == 0; // Every fourth day leaves early
            
            DB::table('test_attendances')->insert([
                'employee_id' => 'EMP001',
                'date' => $date,
                'first_in' => $isLate ? '10:30:00' : '09:00:00',
                'last_out' => $isEarlyDeparture ? '16:00:00' : '18:00:00',
                'status' => $isLate ? 'late' : 'present',
                'work_type' => 'office',
                'actual_work_hours' => $isEarlyDeparture ? 5.5 : ($isLate ? 7.5 : 9),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Create some recent leave requests
        DB::table('test_leaves')->insert([
            [
                'employee_id' => 'EMP001',
                'type' => 'sick',
                'start_date' => Carbon::now()->subDays(15),
                'end_date' => Carbon::now()->subDays(13),
                'reason' => 'Not feeling well',
                'status' => 'approved',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'employee_id' => 'EMP001',
                'type' => 'casual',
                'start_date' => Carbon::now()->subDays(7),
                'end_date' => Carbon::now()->subDays(7),
                'reason' => 'Personal work',
                'status' => 'approved',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        // Create a test employee with normal patterns
        DB::table('test_employees')->insert([
            'employee_id' => 'EMP002',
            'employee_name' => 'Jane Regular',
            'employee_email' => 'jane.regular@example.com',
            'employee_department' => 'Engineering',
            'employee_designation' => 'Developer',
            'joining_date' => '2023-01-01',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create regular attendance records
        for ($i = 30; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            
            DB::table('test_attendances')->insert([
                'employee_id' => 'EMP002',
                'date' => $date,
                'first_in' => '09:00:00',
                'last_out' => '18:00:00',
                'status' => 'present',
                'work_type' => 'office',
                'actual_work_hours' => 9,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Create one planned leave
        DB::table('test_leaves')->insert([
            'employee_id' => 'EMP002',
            'type' => 'planned',
            'start_date' => Carbon::now()->addDays(10),
            'end_date' => Carbon::now()->addDays(12),
            'reason' => 'Family vacation',
            'status' => 'approved',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
