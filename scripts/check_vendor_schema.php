<?php

// Bootstrap the Laravel application
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Vendor;

echo "Checking vendor table schema...\n\n";

// Get column information directly from the database
$columns = DB::select("SHOW COLUMNS FROM vendors");
echo "Vendor table columns:\n";
foreach ($columns as $column) {
    echo "Column: {$column->Field}, Type: {$column->Type}, Null: {$column->Null}, Default: {$column->Default}\n";
}

// Get existing vendors and their status/type values
$vendors = Vendor::limit(5)->get();
echo "\nSample existing vendors:\n";
foreach ($vendors as $vendor) {
    echo "ID: {$vendor->id}, Name: {$vendor->name}, Type: " . ($vendor->type ?? 'NULL') . 
         ", Status: " . ($vendor->status ?? 'NULL') . 
         ", Onboarding Status: " . ($vendor->onboarding_status ?? 'NULL') . "\n";
}

// Get distinct status values
$statuses = DB::table('vendors')->select('status')->distinct()->get();
echo "\nDistinct status values in vendors table:\n";
foreach ($statuses as $status) {
    echo "- " . ($status->status ?? 'NULL') . "\n";
}

// Get distinct type values
$types = DB::table('vendors')->select('type')->distinct()->get();
echo "\nDistinct type values in vendors table:\n";
foreach ($types as $type) {
    echo "- " . ($type->type ?? 'NULL') . "\n";
}
