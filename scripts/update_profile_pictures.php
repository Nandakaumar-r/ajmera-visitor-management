<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Path to the photos directory
$photosDir = __DIR__ . '/../Photos/';

if (!is_dir($photosDir)) {
    Log::error('Photos directory not found.');
    exit('Photos directory not found.');
}

// Get all photos from the directory
$photos = scandir($photosDir);
$photos = array_diff($photos, ['.', '..']); // Remove . and .. entries

// Get all users from database
$users = User::all(['id', 'name'])->keyBy(function($user) {
    return strtolower(str_replace(' ', '', $user->name));
});

$updatedCount = 0;
$notFoundCount = 0;

foreach ($photos as $photo) {
    $photoPath = $photosDir . $photo;
    if (!is_file($photoPath)) continue;

    // Get filename without extension
    $filename = pathinfo($photo, PATHINFO_FILENAME);
    $extension = strtolower(pathinfo($photo, PATHINFO_EXTENSION));
    
    // Skip if not an image
    if (!in_array($extension, ['jpg', 'jpeg', 'png'])) continue;

    // Normalize filename for comparison
    $normalizedFilename = strtolower(str_replace([' ', '.', '_'], '', $filename));

    $user = $users[$normalizedFilename] ?? null;

    if ($user) {
        try {
            // Read the image file
            $imageContent = file_get_contents($photoPath);
            
            // Create storage path if it doesn't exist
            $storagePath = storage_path('app/public/profile_pictures');
            if (!file_exists($storagePath)) {
                mkdir($storagePath, 0755, true);
            }

            // Save the image to storage
            $newFilename = $user->id . '.' . $extension;
            $success = file_put_contents($storagePath . '/' . $newFilename, $imageContent);

            if ($success !== false) {
                // Update user's profile_picture in database
                $user->profile_picture = 'profile_pictures/' . $newFilename;
                $user->save();
                
                echo "Updated profile picture for: {$user->name} (ID: {$user->id})\n";
                $updatedCount++;
            } else {
                echo "Failed to save image for: {$user->name} (ID: {$user->id})\n";
            }
        } catch (Exception $e) {
            echo "Error processing image for {$user->name}: " . $e->getMessage() . "\n";
        }
    } else {
        echo "No matching user found for photo: $photo\n";
        $notFoundCount++;
    }
}

echo "\nProcess completed!\n";
echo "Total photos processed: " . count($photos) . "\n";
echo "Successfully updated: $updatedCount\n";
echo "No matches found: $notFoundCount\n";
