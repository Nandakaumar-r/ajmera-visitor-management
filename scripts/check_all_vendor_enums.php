<?php

// Bootstrap the Laravel application
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Check the database schema for the vendors table
use Illuminate\Support\Facades\DB;

// Columns to check
$columns = ['type', 'status', 'onboarding_status'];

foreach ($columns as $column) {
    // Get the column definition
    $columnInfo = DB::select("SHOW COLUMNS FROM vendors WHERE Field = '$column'");
    
    if (!empty($columnInfo)) {
        $info = $columnInfo[0];
        echo "Column '$column' details:\n";
        echo "Type: " . $info->Type . "\n";
        echo "Null: " . $info->Null . "\n";
        echo "Default: " . $info->Default . "\n";
        
        // If it's an enum, extract the allowed values
        if (strpos($info->Type, 'enum') === 0) {
            preg_match("/^enum\(\'(.*)\'\)$/", $info->Type, $matches);
            if (isset($matches[1])) {
                $enumValues = explode("','", $matches[1]);
                echo "Allowed values: " . implode(", ", $enumValues) . "\n";
            }
        }
        
        // Get a sample of existing values
        $existingValues = DB::table('vendors')->select($column)->distinct()->get();
        echo "\nExisting distinct '$column' values in the database:\n";
        foreach ($existingValues as $value) {
            echo "- " . ($value->$column ?? 'NULL') . "\n";
        }
        
        // Get a count of vendors by this column
        $counts = DB::table('vendors')->select($column, DB::raw('count(*) as count'))->groupBy($column)->get();
        echo "\nVendor counts by $column:\n";
        foreach ($counts as $count) {
            echo "- " . ($count->$column ?? 'NULL') . ": " . $count->count . "\n";
        }
        
        echo "\n" . str_repeat('-', 50) . "\n\n";
    } else {
        echo "Column '$column' not found in vendors table.\n\n";
    }
}

// Get a sample vendor record to see all values together
$sampleVendor = DB::table('vendors')->first();
if ($sampleVendor) {
    echo "Sample vendor record:\n";
    foreach ((array)$sampleVendor as $field => $value) {
        echo "$field: " . ($value ?? 'NULL') . "\n";
    }
}
