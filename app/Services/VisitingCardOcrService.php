<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Services\GroqService;

class VisitingCardOcrService
{
    protected $groqService;

    public function __construct(GroqService $groqService)
    {
        $this->groqService = $groqService;
    }

    public function processVisitingCard($imageData)
    {
        try {
            Log::info('Starting visiting card OCR processing');

            // Validate image data
            if (empty($imageData)) {
                Log::error('No image data provided');
                return [
                    'success' => false,
                    'error' => 'No image data provided'
                ];
            }

            // Remove data URL prefix if present
            $imageData = preg_replace('/^data:image\/\w+;base64,/', '', $imageData);

            // Create a temporary file for the image
            $tempImage = tempnam(sys_get_temp_dir(), 'card_');
            file_put_contents($tempImage, base64_decode($imageData));

            // Save a copy to public storage
            $publicPath = 'visiting_cards/' . Str::random(40) . '.png';
            Storage::disk('public')->put($publicPath, base64_decode($imageData));

            // Use Tesseract to extract text
            $output = [];
            $returnCode = 0;
            
            exec("tesseract " . escapeshellarg($tempImage) . " stdout", $output, $returnCode);

            // Clean up temporary file
            unlink($tempImage);

            if ($returnCode !== 0) {
                Log::error('Tesseract OCR failed', [
                    'return_code' => $returnCode
                ]);
                return [
                    'success' => false,
                    'error' => 'OCR processing failed'
                ];
            }

            // Combine output lines into a single string
            $ocrText = implode("\n", $output);

            Log::info('OCR text extracted successfully', [
                'text_length' => strlen($ocrText)
            ]);

            // Process with Groq
            $groqResult = $this->groqService->analyzeText($ocrText);

            return [
                'success' => true,
                'data' => [
                    'text' => $groqResult ?: [],
                    'raw_text' => $ocrText,
                    'image_path' => $publicPath
                ]
            ];

        } catch (\Exception $e) {
            Log::error('Error in visiting card OCR processing', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'error' => 'An error occurred while processing the visiting card'
            ];
        }
    }

    private function isTesseractInstalled()
    {
        exec('which tesseract', $output, $returnCode);
        return $returnCode === 0;
    }
}
