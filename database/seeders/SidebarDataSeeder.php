<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\Announcement;
use App\Models\QuickLink;
use App\Models\User;
use Carbon\Carbon;

class SidebarDataSeeder extends Seeder
{
    public function run()
    {
        // Create a test user if none exists
        $user = User::first() ?? User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        // Seed Events
        $events = [
            [
                'title' => 'Team Building Workshop',
                'description' => 'Annual team building event',
                'date' => Carbon::now()->addDays(2)->setHour(14)->setMinute(0),
                'location' => 'Conference Room A',
                'created_by' => $user->id,
            ],
            [
                'title' => 'Product Launch Meeting',
                'description' => 'New product features discussion',
                'date' => Carbon::now()->addDays(3)->setHour(10)->setMinute(30),
                'location' => 'Virtual Meeting Room',
                'created_by' => $user->id,
            ],
            [
                'title' => 'Monthly All Hands',
                'description' => 'Company updates and announcements',
                'date' => Carbon::now()->addDays(5)->setHour(11)->setMinute(0),
                'location' => 'Main Hall',
                'created_by' => $user->id,
            ],
            [
                'title' => 'Training Session',
                'description' => 'New technology training',
                'date' => Carbon::now()->addDays(7)->setHour(15)->setMinute(0),
                'location' => 'Training Room B',
                'created_by' => $user->id,
            ],
        ];

        foreach ($events as $event) {
            Event::create($event);
        }

        // Seed Announcements
        $announcements = [
            [
                'title' => '🎉 New Office Opening!',
                'content' => 'We are excited to announce the opening of our new office in downtown. This expansion will help us better serve our growing team.',
                'created_by' => $user->id,
                'is_pinned' => true,
                'published_at' => Carbon::now()->subHours(2),
            ],
            [
                'title' => '📱 Mobile App Update',
                'content' => 'The latest version of our mobile app is now available. Please update to access new features and improvements.',
                'created_by' => $user->id,
                'is_pinned' => false,
                'published_at' => Carbon::now()->subHours(5),
            ],
            [
                'title' => '🏆 Achievement Unlocked',
                'content' => 'Our team has been recognized as the top performer in the industry. Thank you all for your hard work!',
                'created_by' => $user->id,
                'is_pinned' => false,
                'published_at' => Carbon::now()->subHours(8),
            ],
        ];

        foreach ($announcements as $announcement) {
            Announcement::create($announcement);
        }

        // Seed Quick Links
        $quickLinks = [
            [
                'title' => 'HR Portal',
                'url' => '/hr',
                'icon' => '👤',
                'order' => 1,
            ],
            [
                'title' => 'IT Support',
                'url' => '/support',
                'icon' => '💻',
                'order' => 2,
            ],
            [
                'title' => 'Training',
                'url' => '/training',
                'icon' => '📚',
                'order' => 3,
            ],
            [
                'title' => 'Benefits',
                'url' => '/benefits',
                'icon' => '🎁',
                'order' => 4,
            ],
            [
                'title' => 'Directory',
                'url' => '/directory',
                'icon' => '📞',
                'order' => 5,
            ],
            [
                'title' => 'Policies',
                'url' => '/policies',
                'icon' => '📋',
                'order' => 6,
            ],
        ];

        foreach ($quickLinks as $link) {
            QuickLink::create($link);
        }
    }
}
