<?php

// Bootstrap the Laravel application
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Check existing vendor types
use App\Models\Vendor;
use Illuminate\Support\Facades\DB;

echo "Checking existing vendor types...\n";

// Get distinct types from vendors table
$types = DB::table('vendors')->select('type')->distinct()->get();
echo "Distinct vendor types in database:\n";
foreach ($types as $type) {
    echo "- " . ($type->type ?? 'NULL') . "\n";
}

// Get a sample vendor
$sampleVendor = Vendor::first();
if ($sampleVendor) {
    echo "\nSample vendor details:\n";
    echo "ID: " . $sampleVendor->id . "\n";
    echo "Name: " . $sampleVendor->name . "\n";
    echo "Type: " . $sampleVendor->type . "\n";
    echo "Status: " . $sampleVendor->status . "\n";
    echo "Onboarding Status: " . $sampleVendor->onboarding_status . "\n";
}

// Check table structure
echo "\nVendors table structure:\n";
$columns = DB::select('SHOW COLUMNS FROM vendors');
foreach ($columns as $column) {
    echo $column->Field . " - " . $column->Type . " - " . ($column->Null === "YES" ? "NULL" : "NOT NULL") . " - Default: " . $column->Default . "\n";
}
