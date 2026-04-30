<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GroqService
{
    protected $apiKey;
    protected $baseUrl = 'https://api.groq.com/openai/v1/chat/completions';

    public function __construct()
    {
        $this->apiKey = env('GROQ_API_KEY');
        if (empty($this->apiKey)) {
            Log::error('Groq API key is not set in .env file');
        }
    }

    public function analyzeText($text)
    {
        try {
            $prompt = "You are an expert at analyzing business cards and extracting information from noisy OCR text. 
                      The text may contain errors, extra spaces, and special characters that should be ignored.
                      
                      Please analyze this OCR text and extract the following information:
                      1. Full Name: Clean and format the name properly, removing any noise
                      2. Company Name: Extract company name if present
                      3. Email Address: Extract any valid email addresses
                      4. Phone Numbers: Extract and format all phone numbers, preserving country codes
                      
                      Here's the OCR text from the business card:
                      ###
                      {$text}
                      ###
                      
                      Please provide the information in this exact JSON format:
                      {
                          'name': 'cleaned full name',
                          'company': 'company name or empty string',
                          'email': 'email or empty string',
                          'phone': 'all phone numbers, comma separated if multiple'
                      }
                      
                      Rules:
                      1. Clean up obvious OCR errors in names (e.g., 'l' that should be 'i')
                      2. Format phone numbers consistently, preserving country codes
                      3. If multiple phone numbers exist, combine them with commas
                      4. Use empty string for any field not found
                      5. Return only the JSON object, no other text";

            Log::info('Sending request to Groq API', [
                'api_key_set' => !empty($this->apiKey),
                'text_length' => strlen($text)
            ]);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl, [
                'model' => 'mixtral-8x7b-32768',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are an expert at cleaning and extracting structured information from noisy OCR text. Always return data in valid JSON format.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => 0.1,
                'max_tokens' => 500
            ]);

            Log::info('Groq API response received', [
                'status' => $response->status(),
                'body' => $response->json()
            ]);

            if ($response->successful()) {
                $result = $response->json();
                if (isset($result['choices'][0]['message']['content'])) {
                    $content = $result['choices'][0]['message']['content'];
                    // Ensure the content is valid JSON
                    $decoded = json_decode($content, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        return $decoded;
                    } else {
                        Log::error('Failed to decode Groq API JSON response', [
                            'content' => $content,
                            'json_error' => json_last_error_msg()
                        ]);
                    }
                }
            } else {
                Log::error('Groq API error response', [
                    'status' => $response->status(),
                    'body' => $response->json(),
                    'headers' => $response->headers()
                ]);
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Groq service error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }
}
