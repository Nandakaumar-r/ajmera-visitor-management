<?php

// Bootstrap the Laravel application
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Check the database schema for the vendors table
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Get database connection info
$connection = config('database.default');
$database = config("database.connections.{$connection}.database");

echo "Database: {$database}\n";
echo "Connection: {$connection}\n\n";

// Get column information directly from information_schema
$columns = DB::select("
    SELECT COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE, COLUMN_DEFAULT, COLUMN_COMMENT
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'vendors'
    ORDER BY ORDINAL_POSITION
", [$database]);

echo "Vendors Table Schema:\n";
echo str_repeat('-', 80) . "\n";
echo sprintf("%-20s | %-30s | %-10s | %-20s\n", 'Column', 'Type', 'Nullable', 'Default');
echo str_repeat('-', 80) . "\n";

foreach ($columns as $column) {
    echo sprintf(
        "%-20s | %-30s | %-10s | %-20s\n",
        $column->COLUMN_NAME,
        $column->COLUMN_TYPE,
        $column->IS_NULLABLE,
        $column->COLUMN_DEFAULT ?? 'NULL'
    );
    
    // If it's an enum, extract and display the values
    if (strpos($column->COLUMN_TYPE, 'enum') === 0) {
        preg_match("/^enum\(\'(.*)\'\)$/", $column->COLUMN_TYPE, $matches);
        if (isset($matches[1])) {
            $enumValues = explode("','", $matches[1]);
            echo "  Allowed values for {$column->COLUMN_NAME}: " . implode(", ", $enumValues) . "\n";
        }
    }
}

// Get a sample vendor record
$sampleVendor = DB::table('vendors')->first();
if ($sampleVendor) {
    echo "\nSample vendor record:\n";
    echo str_repeat('-', 80) . "\n";
    foreach ((array)$sampleVendor as $field => $value) {
        echo sprintf("%-20s: %s\n", $field, $value ?? 'NULL');
    }
}

// Try to get distinct values for the problematic columns
$columns = ['type', 'status', 'onboarding_status'];
echo "\nDistinct values in the database:\n";
echo str_repeat('-', 80) . "\n";

foreach ($columns as $column) {
    $values = DB::table('vendors')->select($column)->distinct()->whereNotNull($column)->get();
    echo "Distinct values for {$column}:\n";
    foreach ($values as $value) {
        echo "- " . $value->$column . "\n";
    }
    echo "\n";
}
