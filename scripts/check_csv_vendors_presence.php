<?php
// Bootstrap the Laravel application
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$csvPath = __DIR__ . '/../Creditors.csv';
$logPath = __DIR__ . '/../storage/logs/check_csv_vendors_presence.log';

function out($text, $logPath) {
    echo $text;
    if ($logPath) {
        file_put_contents($logPath, $text, FILE_APPEND);
    }
}
if (!file_exists($csvPath)) {
    out("CSV not found at {$csvPath}\n", $logPath);
    exit(1);
}

$fh = fopen($csvPath, 'r');
if (!$fh) {
    out("Unable to open CSV: {$csvPath}\n", $logPath);
    exit(1);
}

// Skip header
fgetcsv($fh);

$names = [];
while (($row = fgetcsv($fh)) !== false) {
    $name = isset($row[1]) ? trim($row[1]) : '';
    if ($name !== '') {
        $names[$name] = true; // de-dupe
    }
}
fclose($fh);

$vendorNames = array_keys($names);
$totalExpected = count($vendorNames);

$present = [];
$missing = [];

if ($totalExpected === 0) {
    out("No vendor names found in CSV.\n", $logPath);
    exit(0);
}

// Batch query to reduce round-trips
$existing = DB::table('vendors')->whereIn('name', $vendorNames)->pluck('name')->all();
$existingSet = array_fill_keys($existing, true);

foreach ($vendorNames as $vn) {
    if (isset($existingSet[$vn])) {
        $present[] = $vn;
    } else {
        $missing[] = $vn;
    }
}

$presentCount = count($present);
$missingCount = count($missing);

// Clear previous log
file_put_contents($logPath, "", LOCK_EX);

out("CSV vendor names (unique, non-empty): {$totalExpected}\n", $logPath);
out("Present in DB: {$presentCount}\n", $logPath);
out("Missing in DB: {$missingCount}\n", $logPath);

if ($missingCount > 0) {
    out("\nMissing vendor names:\n", $logPath);
    foreach ($missing as $m) {
        out("- {$m}\n", $logPath);
    }
}

// Also show extra vendors in DB that are not in CSV (sanity check)
$dbOnly = DB::table('vendors')->whereNotIn('name', $vendorNames)->pluck('name')->all();
if (!empty($dbOnly)) {
    out("\nVendors present in DB but not in CSV (first 20 shown):\n", $logPath);
    $i = 0;
    foreach ($dbOnly as $extra) {
        out("- {$extra}\n", $logPath);
        if (++$i >= 20) { break; }
    }
}
