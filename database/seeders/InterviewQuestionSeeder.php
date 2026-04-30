<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InterviewQuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $questions = [
            [
                'question' => 'What is your primary reason for leaving?',
                'description' => 'Please select the main reason for your resignation',
                'type' => 'select',
                'options' => json_encode([
                    'Better opportunity',
                    'Career change',
                    'Work environment',
                    'Relocation',
                    'Personal reasons',
                    'Health issues',
                    'Further education',
                    'Other'
                ]),
                'is_required' => true,
                'order' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'question' => 'Please elaborate on your reason for leaving',
                'description' => 'Provide more details about your decision to leave',
                'type' => 'text',
                'options' => null,
                'is_required' => true,
                'order' => 2,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'question' => 'What aspects of your job did you enjoy the most?',
                'description' => 'Select all that apply',
                'type' => 'checkbox',
                'options' => json_encode([
                    'Work culture',
                    'Team collaboration',
                    'Learning opportunities',
                    'Work-life balance',
                    'Project challenges',
                    'Company benefits',
                    'Career growth',
                    'Management support'
                ]),
                'is_required' => true,
                'order' => 3,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'question' => 'Would you consider returning to the company in the future?',
                'description' => 'Select your response',
                'type' => 'radio',
                'options' => json_encode([
                    'Yes, definitely',
                    'Maybe',
                    'No',
                    'Unsure'
                ]),
                'is_required' => true,
                'order' => 4,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'question' => 'Do you have any suggestions for improving the work environment?',
                'description' => 'Your feedback will help us improve',
                'type' => 'text',
                'options' => null,
                'is_required' => false,
                'order' => 5,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        DB::table('interview_questions')->insert($questions);
    }
}
