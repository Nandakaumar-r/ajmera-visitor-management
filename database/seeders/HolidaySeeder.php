<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Holiday;
use Carbon\Carbon;

class HolidaySeeder extends Seeder
{
    public function run()
    {
        $holidays = [
            [
                'title' => 'New Year',
                'date' => '2025-01-01',
                'description' => 'First day of the year 2025',
                'type' => 'public'
            ],
            [
                'title' => 'Makara Sankranti / Pongal',
                'date' => '2025-01-14',
                'description' => 'Harvest festival celebrated across India',
                'type' => 'public'
            ],
            [
                'title' => 'Republic Day',
                'date' => '2025-01-26',
                'description' => 'Day when Constitution of India came into effect',
                'type' => 'public'
            ],
            [
                'title' => 'Ramzan/Eid',
                'date' => '2025-03-31',
                'description' => 'Islamic festival marking the end of Ramadan',
                'type' => 'public'
            ],
            [
                'title' => 'Good Friday',
                'date' => '2025-04-18',
                'description' => 'Christian holiday commemorating the crucifixion of Jesus',
                'type' => 'public'
            ],
            [
                'title' => 'May Day / Maharashtra Day',
                'date' => '2025-05-01',
                'description' => 'International Workers\' Day and Maharashtra State Formation Day',
                'type' => 'public'
            ],
            [
                'title' => 'Independence Day',
                'date' => '2025-08-15',
                'description' => 'Celebration of India\'s independence from British rule',
                'type' => 'public'
            ],
            [
                'title' => 'Ganesh Chaturthi',
                'date' => '2025-08-27',
                'description' => 'Festival celebrating Lord Ganesha',
                'type' => 'public'
            ],
            [
                'title' => 'Maha Navami / Ayudha Pooja',
                'date' => '2025-10-01',
                'description' => 'Ninth day of Navaratri festival',
                'type' => 'public'
            ],
            [
                'title' => 'Gandhi Jayanti / Vijayadashami',
                'date' => '2025-10-02',
                'description' => 'Birth anniversary of Mahatma Gandhi and victory of good over evil',
                'type' => 'public'
            ],
            [
                'title' => 'Naraka Chaturdashi',
                'date' => '2025-10-20',
                'description' => 'Day before Diwali',
                'type' => 'public'
            ],
            [
                'title' => 'Deepavali',
                'date' => '2025-10-22',
                'description' => 'Festival of lights',
                'type' => 'public'
            ],
            [
                'title' => 'Kannada Rajyotsava',
                'date' => '2025-11-01',
                'description' => 'Karnataka Formation Day',
                'type' => 'public'
            ],
            [
                'title' => 'Christmas',
                'date' => '2025-12-25',
                'description' => 'Christian festival celebrating the birth of Jesus Christ',
                'type' => 'public'
            ],
        ];

        foreach ($holidays as $holiday) {
            Holiday::create($holiday);
        }
    }
}