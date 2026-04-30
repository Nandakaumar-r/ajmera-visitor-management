<?php

namespace Database\Seeders;

use App\Models\LeaveType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LeaveTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $leaveTypes = [
            ['name' => 'Loss Of Pay', 'code' => 'LOP'],
            ['name' => 'Earned Leave', 'code' => 'EL'],
            ['name' => 'Compensatory Off', 'code' => 'CO'],
            ['name' => 'Restricted Holiday', 'code' => 'RH'],
            ['name' => 'Client Visit', 'code' => 'CV'],
            ['name' => 'Paternity Leave', 'code' => 'PL'],
        ];

        foreach ($leaveTypes as $type) {
            LeaveType::create($type);
        }
    }
}
