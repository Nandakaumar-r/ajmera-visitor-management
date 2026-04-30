<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\Announcement;
use App\Models\QuickLink;
use App\Models\User;
use App\Models\Employee;
use App\Models\Manager;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class TestDataSeeder extends Seeder
{
    public function run()
    {
        // Create roles if they don't exist
        $roles = ['admin', 'employee', 'manager', 'hr', 'tech'];
        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        // Create test users
        $users = [
            [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => Hash::make('password'),
                'date_of_birth' => now(),
                'joining_date' => now()->subYears(2),
                'department' => 'Engineering',
                'role' => 'admin'
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'password' => Hash::make('password'),
                'date_of_birth' => now(),
                'joining_date' => now()->subYears(1),
                'department' => 'Design',
                'role' => 'employee'
            ],
            [
                'name' => 'Mike Johnson',
                'email' => 'mike@example.com',
                'password' => Hash::make('password'),
                'date_of_birth' => now()->subYears(35),
                'joining_date' => now()->subYears(5),
                'department' => 'Engineering',
                'role' => 'manager'
            ],
            [
                'name' => 'Sarah Wilson',
                'email' => 'sarah@example.com',
                'password' => Hash::make('password'),
                'date_of_birth' => now()->subYears(30),
                'joining_date' => now()->subYears(3),
                'department' => 'HR',
                'role' => 'hr'
            ],
        ];

        $createdUsers = [];
        foreach ($users as $userData) {
            $role = $userData['role'];
            unset($userData['role']);
            
            $user = User::create($userData);
            $user->assignRole($role);
            $createdUsers[$role] = $user;
        }

        // Create Manager records
        $manager = Manager::create([
            'manager_name' => $createdUsers['manager']->name,
            'manager_email' => $createdUsers['manager']->email,
        ]);

        // Create Employee records
        Employee::create([
            'employee_id' => 'EMP101',
            'employee_name' => $createdUsers['employee']->name,
            'employee_email' => $createdUsers['employee']->email,
            'employee_designation' => 'Senior Designer',
            'employee_department' => 'Design',
            'employee_date_of_joining' => $createdUsers['employee']->joining_date,
            'manager_id' => 'EMP001',  // Manager from UserSeeder
        ]);

        Employee::create([
            'employee_id' => 'EMP102',
            'employee_name' => $createdUsers['admin']->name,
            'employee_email' => $createdUsers['admin']->email,
            'employee_designation' => 'Tech Lead',
            'employee_department' => 'Engineering',
            'employee_date_of_joining' => $createdUsers['admin']->joining_date,
            'manager_id' => 'EMP001',  // Manager from UserSeeder
        ]);

        // Test Events
        $events = [
            [
                'title' => 'Team Meeting',
                'date' => now()->addDays(1)->setHour(10),
                'location' => 'Conference Room A',
                'created_by' => $createdUsers['admin']->id,
            ],
            [
                'title' => 'Product Launch',
                'date' => now()->addDays(2)->setHour(14),
                'location' => 'Main Hall',
                'created_by' => $createdUsers['admin']->id,
            ],
        ];

        foreach ($events as $event) {
            Event::create($event);
        }

        // Test Announcements
        $announcements = [
            [
                'title' => '🎉 New Feature Release',
                'content' => 'We are excited to announce our latest feature release!',
                'created_by' => $createdUsers['admin']->id,
                'is_pinned' => true,
                'published_at' => now(),
            ],
            [
                'title' => '📢 Office Update',
                'content' => 'Important updates about office policies.',
                'created_by' => $createdUsers['hr']->id,
                'published_at' => now()->subHours(2),
            ],
        ];

        foreach ($announcements as $announcement) {
            Announcement::create($announcement);
        }

        // Test Quick Links
        $quickLinks = [
            [
                'title' => 'HR Portal',
                'url' => '/hr',
                'icon' => '👤',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'IT Help',
                'url' => '/it',
                'icon' => '💻',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Training',
                'url' => '/learn',
                'icon' => '📚',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'title' => 'Directory',
                'url' => '/people',
                'icon' => '📞',
                'order' => 4,
                'is_active' => true,
            ],
        ];

        foreach ($quickLinks as $link) {
            QuickLink::create($link);
        }
    }
}
