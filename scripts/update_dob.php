<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Carbon\Carbon;

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Path to the CSV file
$csvFile = __DIR__ . '/../CSV/Employee DOB - FTSPL.csv';

if (!file_exists($csvFile)) {
    Log::error('CSV file not found.');
    exit('CSV file not found.');
}

// Open the CSV file
if (($handle = fopen($csvFile, 'r')) !== false) {
    // Skip the header row
    fgetcsv($handle, 1000, ',');

    // Process each row
    while (($data = fgetcsv($handle, 1000, ',')) !== false) {
        $name = trim($data[2]); // Correct index for Name
        $dob = trim($data[3]); // Correct index for DOB

        Log::info("Processing name: $name");

        try {
            // Convert DOB to YYYY-MM-DD format
            $dobFormatted = Carbon::createFromFormat('d/m/Y', $dob)->format('Y-m-d');

            // Attempt to update the user's date_of_birth in the database
            $updated = User::where('name', $name)->update(['date_of_birth' => $dobFormatted]);

            if ($updated) {
                Log::info("Updated DOB for: $name");
                echo "Updated DOB for: $name\n";
            } else {
                Log::info("No match found for: $name");
                echo "No match found for: $name\n";
            }
        } catch (Exception $e) {
            Log::error("Error processing DOB for $name: " . $e->getMessage());
            echo "Error processing DOB for $name: " . $e->getMessage() . "\n";
        }
    }

    fclose($handle);
    echo "DOBs update process completed.\n";
} else {
    Log::error('Unable to open CSV file.');
    exit('Unable to open CSV file.');
}
