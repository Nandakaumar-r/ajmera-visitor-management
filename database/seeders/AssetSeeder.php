<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\AssetCategory;
use Illuminate\Database\Seeder;

class AssetSeeder extends Seeder
{
    public function run(): void
    {
        // Create Stationery Category
        $stationeryCategory = AssetCategory::create([
            'name' => 'Stationery',
            'description' => 'Office stationery and supplies'
        ]);

        // Create Electronics Category
        $electronicsCategory = AssetCategory::create([
            'name' => 'Electronics',
            'description' => 'Office electronics and accessories'
        ]);

        // Add Stationery Items
        $stationeryItems = [
            [
                'name' => 'Notebooks (A4)',
                'description' => 'A4 size spiral notebooks, 100 pages',
                'quantity' => 500,
                'unit' => 'pieces'
            ],
            [
                'name' => 'Ballpoint Pens',
                'description' => 'Blue ink ballpoint pens',
                'quantity' => 1000,
                'unit' => 'pieces'
            ],
            [
                'name' => 'Sticky Notes',
                'description' => '3x3 inches, assorted colors',
                'quantity' => 300,
                'unit' => 'packs'
            ],
            [
                'name' => 'File Folders',
                'description' => 'A4 size paper folders',
                'quantity' => 200,
                'unit' => 'pieces'
            ],
            [
                'name' => 'Staplers',
                'description' => 'Standard desktop staplers',
                'quantity' => 50,
                'unit' => 'pieces'
            ],
            [
                'name' => 'Paper Clips',
                'description' => '33mm silver paper clips',
                'quantity' => 100,
                'unit' => 'boxes'
            ],
            [
                'name' => 'Printer Paper',
                'description' => 'A4 size, 80gsm white paper',
                'quantity' => 100,
                'unit' => 'reams'
            ]
        ];

        foreach ($stationeryItems as $item) {
            Asset::create([
                'category_id' => $stationeryCategory->id,
                'name' => $item['name'],
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit' => $item['unit']
            ]);
        }

        // Add Electronics Items
        $electronicsItems = [
            [
                'name' => 'Wireless Mouse',
                'description' => 'Bluetooth wireless mouse',
                'quantity' => 50,
                'unit' => 'pieces'
            ],
            [
                'name' => 'USB Flash Drive',
                'description' => '32GB USB 3.0 flash drive',
                'quantity' => 100,
                'unit' => 'pieces'
            ],
            [
                'name' => 'Wireless Keyboard',
                'description' => 'Bluetooth wireless keyboard',
                'quantity' => 30,
                'unit' => 'pieces'
            ],
            [
                'name' => 'Laptop Stand',
                'description' => 'Adjustable ergonomic laptop stand',
                'quantity' => 40,
                'unit' => 'pieces'
            ],
            [
                'name' => 'Webcam',
                'description' => '1080p HD webcam with microphone',
                'quantity' => 25,
                'unit' => 'pieces'
            ],
            [
                'name' => 'Headphones',
                'description' => 'Over-ear noise-canceling headphones',
                'quantity' => 35,
                'unit' => 'pieces'
            ],
            [
                'name' => 'USB Hub',
                'description' => '4-port USB 3.0 hub',
                'quantity' => 45,
                'unit' => 'pieces'
            ]
        ];

        foreach ($electronicsItems as $item) {
            Asset::create([
                'category_id' => $electronicsCategory->id,
                'name' => $item['name'],
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit' => $item['unit']
            ]);
        }
    }
}
