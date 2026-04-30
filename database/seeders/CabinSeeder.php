<?php

namespace Database\Seeders;

use App\Models\Cabin;
use Illuminate\Database\Seeder;

class CabinSeeder extends Seeder
{
    public function run()
    {
        $cabins = [
            [
                'name' => 'Meeting Room A',
                'description' => 'Large conference room with projector',
                'capacity' => 12,
                'is_active' => true,
                'qr_code' => 'ROOM_A_' . uniqid(),
            ],
            [
                'name' => 'Meeting Room B',
                'description' => 'Medium-sized room with whiteboard',
                'capacity' => 8,
                'is_active' => true,
                'qr_code' => 'ROOM_B_' . uniqid(),
            ],
            [
                'name' => 'Meeting Room C',
                'description' => 'Small discussion room',
                'capacity' => 4,
                'is_active' => true,
                'qr_code' => 'ROOM_C_' . uniqid(),
            ],
            [
                'name' => 'Board Room',
                'description' => 'Executive meeting room with video conferencing',
                'capacity' => 16,
                'is_active' => true,
                'qr_code' => 'BOARD_' . uniqid(),
            ],
        ];

        foreach ($cabins as $cabin) {
            Cabin::create($cabin);
        }
    }
}
