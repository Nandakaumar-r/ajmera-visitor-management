<?php

// Bootstrap the Laravel application
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Get an existing vendor to see its type
use App\Models\Vendor;

$existingVendor = Vendor::first();
if ($existingVendor) {
    echo "Found existing vendor:\n";
    echo "ID: " . $existingVendor->id . "\n";
    echo "Name: " . $existingVendor->name . "\n";
    echo "Type: " . $existingVendor->type . "\n";
    echo "Status: " . $existingVendor->status . "\n";
    echo "Onboarding Status: " . $existingVendor->onboarding_status . "\n";
    
    // Get all distinct type values
    $types = Vendor::distinct()->pluck('type')->toArray();
    echo "\nAll distinct vendor types in the database:\n";
    foreach ($types as $type) {
        echo "- " . ($type ?? 'NULL') . "\n";
    }
    
    // Get all distinct status values
    $statuses = Vendor::distinct()->pluck('status')->toArray();
    echo "\nAll distinct vendor statuses in the database:\n";
    foreach ($statuses as $status) {
        echo "- " . ($status ?? 'NULL') . "\n";
    }
} else {
    echo "No vendors found in the database.\n";
}
