<?php

// Bootstrap the Laravel application
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// Get the status column definition
$statusColumn = DB::select("SHOW COLUMNS FROM vendors WHERE Field = 'status'");
if (!empty($statusColumn)) {
    echo "Status column definition: " . json_encode($statusColumn[0]) . "\n";
    
    // If it's an enum, extract the values
    if (strpos($statusColumn[0]->Type, 'enum') === 0) {
        preg_match("/^enum\(\'(.*)\'\)$/", $statusColumn[0]->Type, $matches);
        if (isset($matches[1])) {
            $enumValues = explode("','", $matches[1]);
            echo "Status enum values: " . implode(", ", $enumValues) . "\n";
        }
    }
}

// Get the type column definition
$typeColumn = DB::select("SHOW COLUMNS FROM vendors WHERE Field = 'type'");
if (!empty($typeColumn)) {
    echo "Type column definition: " . json_encode($typeColumn[0]) . "\n";
    
    // If it's an enum, extract the values
    if (strpos($typeColumn[0]->Type, 'enum') === 0) {
        preg_match("/^enum\(\'(.*)\'\)$/", $typeColumn[0]->Type, $matches);
        if (isset($matches[1])) {
            $enumValues = explode("','", $matches[1]);
            echo "Type enum values: " . implode(", ", $enumValues) . "\n";
        }
    }
}

// Get sample vendors with their status and type
$vendors = DB::table('vendors')->select('id', 'name', 'status', 'type')->limit(5)->get();
echo "\nSample vendors:\n";
foreach ($vendors as $vendor) {
    echo "ID: {$vendor->id}, Name: {$vendor->name}, Status: {$vendor->status}, Type: " . ($vendor->type ?? 'NULL') . "\n";
}
