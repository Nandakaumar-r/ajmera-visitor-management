<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\CabinSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
            ImportEmployeeSeeder::class,
            LeaveTypeSeeder::class,
            LeaveBalanceSeeder::class,
            HolidaySeeder::class,
            AttendanceSeeder::class,
            SidebarDataSeeder::class,
            AssetSeeder::class,
            CabinSeeder::class,
        ]);
    }
}
