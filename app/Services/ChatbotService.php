<?php

namespace App\Services;

use App\Models\ChatbotConversation;
use App\Models\User;
use App\Models\LeaveBalance;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Google\Cloud\Language\LanguageClient;

class ChatbotService
{
    protected $languageClient;
    protected $groqApiKey;
    protected $companyPolicies;
    protected $privacyGuidelines;

    public function __construct()
    {
        $this->groqApiKey = config('services.groq.api_key');
        $this->loadCompanyPolicies();
        $this->initializePrivacyGuidelines();

        if (config('services.google.credentials')) {
            $this->languageClient = new LanguageClient([
                'credentials' => config('services.google.credentials')
            ]);
        }
    }

    protected function initializePrivacyGuidelines()
    {
        $this->privacyGuidelines = [
            'data_access' => [
                'personal_info' => 'Only provide information about the requesting employee',
                'salary_info' => 'Never disclose salary information',
                'manager_info' => 'Only show direct manager name and department head',
                'team_info' => 'Only provide team size and department statistics',
                'prohibited_fields' => [
                    'salary',
                    'compensation',
                    'bank_details',
                    'personal_address',
                    'phone_number',
                    'emergency_contacts',
                    'performance_ratings',
                    'disciplinary_records',
                    'medical_history'
                ],
            ],
            'response_guidelines' => [
                'sensitive_topics' => [
                    'performance_reviews',
                    'disciplinary_actions',
                    'compensation_changes',
                    'medical_leaves',
                    'workplace_conflicts'
                ],
                'escalation_required' => [
                    'harassment_complaints',
                    'discrimination_reports',
                    'ethical_violations',
                    'legal_matters',
                    'confidentiality_breaches'
                ]
            ]
        ];
    }

    protected function loadCompanyPolicies()
    {
        $this->companyPolicies = Cache::remember('company_policies', 86400, function () {
            return [
                'leave_policy' => [
                    'annual_leave' => [
                        'days' => '18 days per year',
                        'carry_forward' => '0 days',
                        'application_process' => 'Submit through HR portal at least 1 week in advance',
                        'cancellation_policy' => '48 hours notice required'
                    ],
                    'sick_leave' => [
                        'days' => '14 days per year',
                        'documentation' => 'Medical certificate required for 3+ consecutive days',
                        'notification' => 'Inform manager before shift start'
                    ],
                    'maternity_leave' => [
                        'duration' => '26 weeks',
                        'eligibility' => 'After 80 days of employment',
                        'benefits' => 'Full pay continuation'
                    ],
                    'paternity_leave' => [
                        'duration' => '2 weeks',
                        'eligibility' => 'All permanent male employees'
                    ],
                    'bereavement_leave' => [
                        'duration' => '3 days',
                        'immediate_family' => 'Parents, spouse, children, siblings'
                    ]
                ],
                'work_policy' => [
                    'work_hours' => [
                        'standard' => '9 AM to 6 PM',
                        'flexible_hours' => 'Core hours 11 AM to 4 PM',
                        'break_duration' => '1 hour lunch break'
                    ],
                    'work_days' => 'Monday to Friday',
                    'overtime_policy' => [
                        'approval' => 'Required from manager',
                        'compensation' => 'Time off in lieu or overtime pay as per policy'
                    ],
                    'remote_work' => [
                        'policy' => 'Hybrid model available',
                        'equipment' => 'Company provides necessary hardware',
                        'connectivity' => 'Internet allowance provided'
                    ]
                ],
                'resignation_policy' => [
                    'notice_period' => [
                        'duration' => '60 days',
                        'buyout_option' => 'Available with management approval',
                        'exceptions' => 'May be adjusted for critical roles'
                    ],
                    'exit_process' => [
                        'interview' => 'Mandatory with HR',
                        'documentation' => 'Required from all departments',
                        'asset_return' => 'All company assets to be returned',
                        'knowledge_transfer' => 'Minimum 2 weeks required'
                    ],
                    'fnf_settlement' => [
                        'timeline' => 'Within 45 days of last working day',
                        'requirements' => 'All clearances must be completed',
                        'documents' => ['Experience letter', 'Relieving letter', 'Form 16']
                    ]
                ],
                'benefits' => [
                    'health_insurance' => [
                        'coverage' => 'Employee and dependents',
                        'limit' => 'As per grade',
                        'inclusions' => ['Hospitalization', 'Maternity', 'Pre-existing conditions']
                    ],
                    'life_insurance' => [
                        'coverage' => '3x annual salary',
                        'additional_riders' => 'Optional at employee cost'
                    ],
                    'wellness_program' => [
                        'allowance' => 'Annual wellness budget',
                        'activities' => ['Gym membership', 'Health checkups', 'Mental health support']
                    ],
                    'learning_development' => [
                        'budget' => 'Annual training allowance',
                        'eligibility' => 'After 6 months of employment',
                        'areas' => ['Technical skills', 'Soft skills', 'Certifications']
                    ]
                ]
            ];
        });
    }

    protected function getEmployeeContext(User $user): array
    {
        $currentYear = date('Y');
        $leaveBalances = LeaveBalance::with('leaveType')
            ->where('user_id', $user->id)
            ->where('year', $currentYear)
            ->get()
            ->map(function ($balance) {
                $granted = max($balance->granted ?? 0, 0);
                $consumed = max($balance->consumed ?? 0, 0);
                return [
                    'type' => $balance->leaveType->name,
                    'granted' => $granted,
                    'consumed' => $consumed,
                    'balance' => max($balance->balance ?? 0, 0),
                    'code' => $balance->leaveType->code,
                ];
            })->toArray();

        return [
            'basic_info' => [
                'name' => $user->name,
                'employee_id' => $user->employee_id,
                'department' => $user->department,
                'role' => $user->role,
                'joining_date' => $user->joining_date,
            ],
            'reporting_structure' => [
                'manager' => $user->manager ? [
                    'name' => $user->manager->name,
                    'department' => $user->manager->department
                ] : 'Not assigned',
                'department_head' => $user->department_head ? [
                    'name' => $user->department_head->name,
                    'department' => $user->department_head->department
                ] : 'Not assigned',
            ],
            'employment_details' => [
                'employment_type' => $user->employment_type,
                'work_location' => $user->work_location,
                'designation' => $user->designation,
            ],
            'team_context' => [
                'department_size' => $user->department_size ?? 'Not available',
                'team_size' => $user->team_size ?? 'Not available',
            ],
            'access_level' => [
                'is_manager' => $user->is_manager ?? false,
                'is_department_head' => $user->is_department_head ?? false,
            ],
            'leave_balances' => $leaveBalances
        ];
    }

    public function handleMessage(string $message, User $user, string $platform = 'web'): array
    {
        try {
            // Detect language and sentiment
            $language = $this->detectLanguage($message);
            $sentiment = $this->analyzeSentiment($message);

            // Check for sensitive keywords
            $needsEscalation = $this->checkForSensitiveContent($message) || $sentiment < -0.5;

            // Generate response using GROQ
            $response = $this->generateResponse($message, $user, $language);

            // Save conversation
            $conversation = ChatbotConversation::create([
                'user_id' => $user->id,
                'message' => $message,
                'response' => $response,
                'sentiment_score' => $sentiment,
                'language' => $language,
                'platform' => $platform,
                'is_escalated' => $needsEscalation,
            ]);

            return [
                'success' => true,
                'data' => [
                    'response' => $response,
                    'needs_escalation' => $needsEscalation,
                    'id' => $conversation->id,
                ]
            ];
        } catch (\Exception $e) {
            Log::error('Chatbot error: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => 'An error occurred while processing your request.',
                'data' => [
                    'response' => "I apologize, but I'm having trouble processing your request right now. Please try again later or contact HR for assistance.",
                    'needs_escalation' => true,
                ]
            ];
        }
    }

    protected function checkForSensitiveContent(string $message): bool
    {
        $message = strtolower($message);
        $sensitiveTopics = array_merge(
            $this->privacyGuidelines['response_guidelines']['sensitive_topics'],
            $this->privacyGuidelines['response_guidelines']['escalation_required']
        );

        foreach ($sensitiveTopics as $topic) {
            if (str_contains($message, strtolower($topic))) {
                return true;
            }
        }

        return false;
    }

    protected function generateResponse(string $message, User $user, string $language): string
    {
        $employeeContext = $this->getEmployeeContext($user);

        $systemPrompt = "You are an HR assistant name NexoHR, helping employees with their queries. " .
            "Please follow these strict privacy and response guidelines:\n\n" .
            "Privacy Rules:\n" .
            json_encode($this->privacyGuidelines, JSON_PRETTY_PRINT) . "\n\n" .
            "Employee Context (CONFIDENTIAL - Only use for this employee):\n" .
            json_encode($employeeContext, JSON_PRETTY_PRINT) . "\n\n" .
            "Company Policies (Public Information):\n" .
            json_encode($this->companyPolicies, JSON_PRETTY_PRINT) . "\n\n" .
            "Guidelines:\n" .
            "1. NEVER share information about other employees\n" .
            "2. Only use the provided employee context for personalization\n" .
            "3. For sensitive topics, recommend contacting HR directly\n" .
            "4. Be professional and empathetic in responses\n" .
            "5. Format responses clearly with headings and bullet points\n" .
            "6. If asked about another employee, politely explain privacy policy\n" .
            "7. Stick to factual policy information for general queries\n" .
            "8. For manager queries, verify access_level before sharing team info";

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->groqApiKey,
            ])->post('https://api.groq.com/openai/v1/chat/completions', [
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $systemPrompt
                    ],
                    [
                        'role' => 'user',
                        'content' => $message
                    ]
                ],
                'model' => 'llama-3.3-70b-versatile',
                'temperature' => 0.09,
                'max_tokens' => 1024,
                'top_p' => 1,
                'stream' => false,
                'stop' => null
            ]);

            if ($response->successful()) {
                return $response->json()['choices'][0]['message']['content'];
            }

            Log::error('GROQ API Error: ' . $response->body());
            throw new \Exception('Failed to get response from GROQ API');
        } catch (\Exception $e) {
            Log::error('GROQ API Error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function extractBillDetails(string $ocrText): array
    {
        $prompt = '
You are an intelligent assistant that extracts structured data from OCR-scanned bill text.

Extract and return the following based on the content:

1. General:
- Date
- Bill Type (e.g., travel, food, other)
- Description
- Total Amount

2. If Bill Type is Travel:
- Transport Mode (bike, car, auto, cab)
- Total KMs
- Description
- Date
- Amount

3. If Bill Type is Food:
- Event name (if mentioned)
- Description
- Date
- Amount

4. If Bill Type is Others:
- Description
- Date
- Amount

Return the result as a valid JSON object like:
{
  "type": "travel",
  "date": "2025-04-01",
  "description": "Uber ride to airport",
  "amount": 450.00,
  "transport_mode": "cab",
  "kms": 25
}

or for food:
{
  "type": "food",
  "date": "2025-04-01",
  "event": "Client Dinner",
  "description": "Dinner at BBQ Nation",
  "amount": 1200.00
}

or for others:
{
  "type": "others",
  "date": "2025-04-01",
  "description": "Printer ink purchase",
  "amount": 300.00
}

return only the json and nothing else
';
        try {
            $response = Http::withOptions([
                'verify' => false
            ])->withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->groqApiKey,            
            ])->post('https://api.groq.com/openai/v1/chat/completions', [
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $prompt
                    ],
                    [
                        'role' => 'user',
                        'content' => $ocrText
                    ]
                ],
                'model' => 'llama-3.3-70b-versatile',
                'temperature' => 0.2,
                'max_tokens' => 1500,
                'top_p' => 1,
                'stream' => false,
                'stop' => null,
                'response_format' => ['type' => 'json_object'],
            ]);

            if ($response->successful()) {
                $json = $response->json()['choices'][0]['message']['content'];

                // Try to parse response as JSON
                return json_decode($json, true) ?? ['error' => 'Invalid JSON returned from LLM'];
            }

            Log::error('GROQ Bill Details Extraction Error: ' . $response->body());
            return ['error' => 'Failed to extract bill details'];
        } catch (\Exception $e) {
            Log::error('Exception in extractBillDetails: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }


    protected function detectLanguage(string $text): string
    {
        if (!$this->languageClient) {
            return 'en';
        }

        try {
            $annotation = $this->languageClient->analyzeSentiment([
                'content' => $text,
                'type' => 'PLAIN_TEXT'
            ]);

            return $annotation->info()['language'] ?? 'en';
        } catch (\Exception $e) {
            Log::error('Language detection error: ' . $e->getMessage());
            return 'en';
        }
    }

    protected function analyzeSentiment(string $text): float
    {
        if (!$this->languageClient) {
            return 0.0;
        }

        try {
            $annotation = $this->languageClient->analyzeSentiment([
                'content' => $text,
                'type' => 'PLAIN_TEXT'
            ]);

            $sentiment = $annotation->sentiment();
            return $sentiment['score'] ?? 0.0;
        } catch (\Exception $e) {
            Log::error('Sentiment analysis error: ' . $e->getMessage());
            return 0.0;
        }
    }

    public function saveFeedback(int $conversationId, int $rating, ?string $comment = null): void
    {
        $conversation = ChatbotConversation::findOrFail($conversationId);
        $conversation->update([
            'feedback_rating' => $rating,
            'feedback_comment' => $comment,
        ]);
    }
}
