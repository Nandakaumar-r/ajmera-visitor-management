<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Employee;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Manager user first
        $managerUser = User::create([
            'name' => 'Sarah Manager',
            'email' => 'manager@fidelis.com',
            'password' => Hash::make('manager123'),
        ]);
        $managerUser->assignRole('Manager');

        // Create manager employee record
        $managerEmployee = Employee::create([
            'employee_id' => 'EMP001',
            'employee_name' => $managerUser->name,
            'employee_email' => $managerUser->email,
            'employee_designation' => 'Engineering Manager',
            'employee_department' => 'Engineering',
            'employee_date_of_joining' => now(),
            'manager_id' => null  // Manager reports to no one
        ]);

        // Create other users
        $users = [
            [
                'name' => 'John Employee',
                'email' => 'employee@fidelis.com',
                'password' => Hash::make('employee123'),
                'role' => 'Employee',
                'designation' => 'Software Engineer',
                'department' => 'Engineering',
                'employee_id' => 'EMP002'
            ],
            [
                'name' => 'Mike HR',
                'email' => 'hr@fidelis.com',
                'password' => Hash::make('hr123'),
                'role' => 'HR',
                'designation' => 'HR Manager',
                'department' => 'Human Resources',
                'employee_id' => 'EMP003'
            ],
            [
                'name' => 'Admin User',
                'email' => 'admin@fidelis.com',
                'password' => Hash::make('admin123'),
                'role' => 'Admin',
                'designation' => 'System Administrator',
                'department' => 'IT',
                'employee_id' => 'EMP004'
            ]
        ];

        foreach ($users as $userData) {
            // Create user
            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => $userData['password']
            ]);
            $user->assignRole($userData['role']);

            // Create employee record
            Employee::create([
                'employee_id' => $userData['employee_id'],
                'employee_name' => $user->name,
                'employee_email' => $user->email,
                'employee_designation' => $userData['designation'],
                'employee_department' => $userData['department'],
                'employee_date_of_joining' => now(),
                'manager_id' => $managerEmployee->employee_id  // All employees report to the manager
            ]);
        }
    }
}
