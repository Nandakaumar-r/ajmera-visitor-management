<?php

$sourceLogo = __DIR__ . '/Logo_Fidelis.png';
$sizes = [192, 512];

foreach ($sizes as $size) {
    $outputFile = __DIR__ . "/images/icons/icon-{$size}x{$size}.png";
    
    // Create image from source
    $source = imagecreatefrompng($sourceLogo);
    
    // Get original dimensions
    $width = imagesx($source);
    $height = imagesy($source);
    
    // Create a new true color image
    $destination = imagecreatetruecolor($size, $size);
    
    // Set the background to transparent
    imagealphablending($destination, false);
    imagesavealpha($destination, true);
    $transparent = imagecolorallocatealpha($destination, 255, 255, 255, 127);
    imagefilledrectangle($destination, 0, 0, $size, $size, $transparent);
    
    // Copy and resize the image
    imagecopyresampled($destination, $source, 0, 0, 0, 0, $size, $size, $width, $height);
    
    // Save the new image
    imagepng($destination, $outputFile);
    
    // Free up memory
    imagedestroy($destination);
    imagedestroy($source);
    
    echo "Generated icon: {$size}x{$size}\n";
}

echo "Icon generation complete!\n";
