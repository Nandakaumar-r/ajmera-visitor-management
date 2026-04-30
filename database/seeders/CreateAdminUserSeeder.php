<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Employee;
use App\Models\Manager;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;

class CreateAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        $user = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123')
        ]);

        // Create roles if they don't exist
        $roles = ['admin', 'hr', 'employee', 'manager'];
        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        // Assign all roles to admin user
        $user->assignRole($roles);

        // Create manager record first
        $manager = Manager::create([
            'manager_name' => 'Admin User',
            'manager_email' => 'admin@example.com'
        ]);

        // Create employee record
        $employee = Employee::create([
            'employee_id' => 1001,
            'employee_name' => 'Admin User',
            'employee_email' => 'admin@example.com',
            'employee_designation' => 'Administrator',
            'employee_department' => 'Administration',
            'employee_date_of_joining' => now(),
            'manager_id' => $manager->id
        ]);
    }
}
