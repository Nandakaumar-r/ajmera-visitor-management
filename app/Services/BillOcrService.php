<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BillOcrService
{
    protected $groqApiKey;
    protected $groqEndpoint = 'https://api.groq.com/openai/v1/chat/completions';

    public function __construct()
    {
        $this->groqApiKey = config('services.groq.api_key');
    }

    public function extractBillData($file)
    {
        try {
            // 1. Extract text using OCR (Tesseract or other service)
            $text = $this->extractText($file);
            
            // 2. Use GROQ AI to extract structured data
            return $this->extractWithGroq($text);
        } catch (\Exception $e) {
            Log::error('OCR Processing Error: ' . $e->getMessage());
            return null;
        }
    }

    protected function extractText($file)
    {
        // For PDFs, we'll use a PDF text extractor
        if ($file->getClientOriginalExtension() === 'pdf') {
            $tempPath = $file->getRealPath();
            $text = shell_exec("pdftotext -layout " . escapeshellarg($tempPath) . " - 2>&1");
            return $text ?: '';
        }
        
        // For images, we'll use Tesseract OCR
        $tempPath = $file->store('temp', 'local');
        $tempPath = storage_path('app/' . $tempPath);
        
        try {
            $text = shell_exec("tesseract " . escapeshellarg($tempPath) . " stdout 2>&1");
            return $text ?: '';
        } finally {
            @unlink($tempPath);
        }
    }

    protected function extractWithGroq($text)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->groqApiKey,
            'Content-Type' => 'application/json',
        ])->post($this->groqEndpoint, [
            'model' => 'llama3-70b-8192',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are an AI assistant that extracts structured data from bill/invoice text. Extract the following fields: bill_number, bill_date, due_date, amount, tax_amount, total_amount, gst_type, gst_percentage, vendor_name, vendor_address, description. Return only valid JSON.'
                ],
                [
                    'role' => 'user',
                    'content' => $text
                ]
            ],
            'temperature' => 0.2,
            'max_tokens' => 2000
        ]);

        if ($response->successful()) {
            $content = $response->json('choices.0.message.content');
            // Clean up the response to ensure it's valid JSON
            $content = preg_replace('/```json\n|```/s', '', $content);
            return json_decode(trim($content), true) ?? [];
        }

        Log::error('GROQ API Error: ' . $response->body());
        return [];
    }
}
