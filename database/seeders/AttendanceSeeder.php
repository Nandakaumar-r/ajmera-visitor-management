<?php

namespace Database\Seeders;

use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            for ($j = 0; $j < 10; $j++) {
                Attendance::create([
                    'employee_id' => $i,
                    'date' => Carbon::now()->subDays($j),
                    'first_in' => '09:30:00',
                    'last_out' => '18:30:00',
                    'late_in' => rand(0, 1),
                    'early_out' => rand(0, 1),
                    'total_work_hours' => 9.0,
                    'break_hours' => 0.5,
                    'status' => 'Approved',
                    'remarks' => null,
                    'shift' => 'General Shift',
                    'work_type' => 'Office',
                ]);
            }
        }
    }
}
