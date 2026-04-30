<?php

namespace App\Services;

use App\Models\TestEmployee;
use App\Models\TestAttendance;
use App\Models\TestLeave;
use App\Models\Resignation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class ResignationPredictionService
{
    protected $groqApiKey;

    public function __construct()
    {
        $this->groqApiKey = config('services.groq.api_key');
    }

    public function analyzeEmployee($employeeId)
    {
        $employee = TestEmployee::where('employee_id', $employeeId)->firstOrFail();
        
        // Gather employee data
        $data = $this->gatherEmployeeData($employee);
        
        // Generate prediction using Groq API
        $prediction = $this->getPrediction($data);
        
        // Update or create resignation record
        $this->updateResignationRecord($employee, $prediction);
        
        return $prediction;
    }

    protected function gatherEmployeeData($employee)
    {
        $now = Carbon::now();
        $threeMonthsAgo = $now->copy()->subMonths(3);
        
        // Get attendance data
        $attendances = $employee->attendances()
            ->where('date', '>=', $threeMonthsAgo)
            ->get();
        
        // Calculate attendance metrics
        $lateArrivals = $attendances->where('status', 'late')->count();
        $earlyDepartures = $attendances->where('status', 'early_departure')->count();
        $absences = $attendances->where('status', 'absent')->count();
        
        // Get leave data
        $leaves = $employee->leaves()
            ->where('start_date', '>=', $threeMonthsAgo)
            ->get();
        
        // Calculate leave metrics
        $sickLeaves = $leaves->where('type', 'sick')->count();
        $personalLeaves = $leaves->where('type', 'personal')->count();
        
        // Calculate tenure
        $tenureInYears = $now->diffInYears($employee->joining_date);
        
        return [
            'employee_id' => $employee->employee_id,
            'department' => $employee->employee_department,
            'designation' => $employee->employee_designation,
            'tenure_years' => $tenureInYears,
            'metrics' => [
                'late_arrivals' => $lateArrivals,
                'early_departures' => $earlyDepartures,
                'absences' => $absences,
                'sick_leaves' => $sickLeaves,
                'personal_leaves' => $personalLeaves,
            ]
        ];
    }

    protected function getPrediction($data)
    {
        if (empty($this->groqApiKey)) {
            throw new \Exception('Groq API key not configured. Please add GROQ_API_KEY to your .env file.');
        }

        $prompt = $this->formatPrompt($data);
        
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->groqApiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => 'mixtral-8x7b-32768',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are an HR analytics expert specializing in predicting employee resignation risk. Analyze the provided data and return a JSON response with risk_level (low, medium, high), risk_factors (array of factors), and recommendations (array of actionable items).'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => 0.7,
                'max_tokens' => 500
            ]);
            
            if ($response->successful()) {
                $result = $response->json();
                $content = $result['choices'][0]['message']['content'];
                return json_decode($content, true);
            }
            
            throw new \Exception('Failed to get prediction from Groq API: ' . $response->body());
        } catch (\Exception $e) {
            throw new \Exception('Failed to get prediction from Groq API: ' . $e->getMessage());
        }
    }

    protected function formatPrompt($data)
    {
        return "Please analyze the following employee data for resignation risk:
                Employee ID: {$data['employee_id']}
                Department: {$data['department']}
                Designation: {$data['designation']}
                Tenure: {$data['tenure_years']} years
                
                Recent 3-month metrics:
                - Late arrivals: {$data['metrics']['late_arrivals']}
                - Early departures: {$data['metrics']['early_departures']}
                - Absences: {$data['metrics']['absences']}
                - Sick leaves: {$data['metrics']['sick_leaves']}
                - Personal leaves: {$data['metrics']['personal_leaves']}
                
                Based on these metrics, provide a JSON response with:
                1. Risk level (low, medium, high)
                2. Risk factors identified
                3. Recommendations for retention";
    }

    protected function updateResignationRecord($employee, $prediction)
    {
        Resignation::updateOrCreate(
            ['employee_id' => $employee->employee_id],
            [
                'risk_level' => $prediction['risk_level'],
                'risk_factors' => json_encode($prediction['risk_factors']),
                'recommendations' => json_encode($prediction['recommendations']),
                'last_prediction_at' => now(),
            ]
        );
    }

    public function analyzeAllEmployees()
    {
        $employees = TestEmployee::all();
        $results = [];
        
        foreach ($employees as $employee) {
            try {
                $results[$employee->employee_id] = $this->analyzeEmployee($employee->employee_id);
            } catch (\Exception $e) {
                $results[$employee->employee_id] = [
                    'error' => $e->getMessage()
                ];
            }
        }
        
        return $results;
    }
}
