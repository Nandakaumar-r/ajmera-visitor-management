<?php

// Bootstrap the Laravel application
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Check the database schema for the vendors table
use Illuminate\Support\Facades\DB;

// Get the column definition for the 'type' column
$typeColumnInfo = DB::select("SHOW COLUMNS FROM vendors WHERE Field = 'type'");

if (!empty($typeColumnInfo)) {
    $typeInfo = $typeColumnInfo[0];
    echo "Column 'type' details:\n";
    echo "Type: " . $typeInfo->Type . "\n";
    echo "Null: " . $typeInfo->Null . "\n";
    echo "Default: " . $typeInfo->Default . "\n";
    
    // If it's an enum, extract the allowed values
    if (strpos($typeInfo->Type, 'enum') === 0) {
        preg_match("/^enum\(\'(.*)\'\)$/", $typeInfo->Type, $matches);
        if (isset($matches[1])) {
            $enumValues = explode("','", $matches[1]);
            echo "Allowed values: " . implode(", ", $enumValues) . "\n";
        }
    }
    
    // Get a sample of existing values
    $existingTypes = DB::table('vendors')->select('type')->distinct()->get();
    echo "\nExisting distinct 'type' values in the database:\n";
    foreach ($existingTypes as $type) {
        echo "- " . ($type->type ?? 'NULL') . "\n";
    }
} else {
    echo "Column 'type' not found in vendors table.\n";
}

// Get a count of vendors by type
$vendorCounts = DB::table('vendors')->select('type', DB::raw('count(*) as count'))->groupBy('type')->get();
echo "\nVendor counts by type:\n";
foreach ($vendorCounts as $count) {
    echo "- " . ($count->type ?? 'NULL') . ": " . $count->count . "\n";
}
