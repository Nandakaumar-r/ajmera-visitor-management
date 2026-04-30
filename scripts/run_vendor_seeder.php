<?php

// Bootstrap the Laravel application
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Run the seeder directly
echo "Starting VendorSeeder script...\n";

try {
    $seeder = new Database\Seeders\VendorSeeder();
    $seeder->run();
    echo "VendorSeeder script completed.\n";
} catch (Exception $e) {
    echo "Error running VendorSeeder: " . $e->getMessage() . "\n";
}
