<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Employee;
use App\Models\Manager;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class CreateEmployeeManagerSeeder extends Seeder
{
    public function run(): void
    {
        // Create manager user
        $managerUser = User::create([
            'name' => 'John Manager',
            'email' => 'manager@example.com',
            'password' => Hash::make('password123')
        ]);

        // Create manager record
        $manager = Manager::create([
            'manager_name' => 'John Manager',
            'manager_email' => 'manager@example.com'
        ]);

        // Assign manager role
        $managerRole = Role::firstOrCreate(['name' => 'manager']);
        $managerUser->assignRole($managerRole);

        // Create employee user
        $employeeUser = User::create([
            'name' => 'Jane Employee',
            'email' => 'employee@example.com',
            'password' => Hash::make('password123')
        ]);

        // Create employee record
        $employee = Employee::create([
            'employee_id' => 1002,
            'employee_name' => 'Jane Employee',
            'employee_email' => 'employee@example.com',
            'employee_designation' => 'Software Engineer',
            'employee_department' => 'Engineering',
            'employee_date_of_joining' => now(),
            'manager_id' => $manager->id
        ]);

        // Assign employee role
        $employeeRole = Role::firstOrCreate(['name' => 'employee']);
        $employeeUser->assignRole($employeeRole);
    }
}
