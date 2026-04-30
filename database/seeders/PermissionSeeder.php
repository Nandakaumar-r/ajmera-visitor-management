<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Permission::truncate();
        DB::table('model_has_permissions')->truncate();
        DB::table('role_has_permissions')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Leave Management Permissions
        Permission::create(['name' => 'view-leave-requests']);
        Permission::create(['name' => 'create-leave-requests']);
        Permission::create(['name' => 'approve-leave-requests']);
        Permission::create(['name' => 'reject-leave-requests']);
        
        // Attendance Management Permissions
        Permission::create(['name' => 'view-attendance']);
        Permission::create(['name' => 'create-attendance']);
        Permission::create(['name' => 'approve-attendance']);
        Permission::create(['name' => 'reject-attendance']);
        Permission::create(['name' => 'modify-attendance']);

        // Resignation Permissions
        Permission::create(['name' => 'create-resignation']);
        Permission::create(['name' => 'view-resignation']);
        Permission::create(['name' => 'approve-resignation']);
        Permission::create(['name' => 'reject-resignation']);

        // Get the roles
        $employeeRole = Role::where('name', 'Employee')->first();
        $managerRole = Role::where('name', 'Manager')->first();
        $hrRole = Role::where('name', 'HR')->first();

        // Assign permissions to employee role
        $employeeRole->givePermissionTo([
            'create-leave-requests',
            'view-leave-requests',
            'create-attendance',
            'view-attendance',
            'create-resignation',
            'view-resignation'
        ]);

        // Assign permissions to manager role
        $managerRole->givePermissionTo([
            'view-leave-requests',
            'approve-leave-requests',
            'reject-leave-requests',
            'view-attendance',
            'approve-attendance',
            'reject-attendance',
            'modify-attendance',
            'view-resignation',
            'approve-resignation',
            'reject-resignation'
        ]);

        // Assign permissions to HR role
        $hrRole->givePermissionTo([
            'view-leave-requests',
            'approve-leave-requests',
            'reject-leave-requests',
            'view-attendance',
            'approve-attendance',
            'reject-attendance',
            'modify-attendance',
            'view-resignation',
            'approve-resignation',
            'reject-resignation'
        ]);
    }
}
