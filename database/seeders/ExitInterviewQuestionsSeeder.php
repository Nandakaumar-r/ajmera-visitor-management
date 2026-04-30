<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExitInterviewQuestionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('exit_interview_questions')->insert([
            [
                'question' => 'What is the primary reason for your resignation?',
                'field_type' => 'radio',
                'options' => json_encode(['Better opportunity', 'Work-life balance', 'Compensation', 'Other']),
            ],
            [
                'question' => 'What did you like most about working here?',
                'field_type' => 'textarea',
                'options' => null,
            ],
            [
                'question' => 'Were you satisfied with your manager\'s support?',
                'field_type' => 'radio',
                'options' => json_encode(['Yes', 'No', 'Somewhat']),
            ],
            [
                'question' => 'Please provide any suggestions for improvement.',
                'field_type' => 'text',
                'options' => null,
            ],
            [
                'question' => 'Would you recommend this company to a friend?',
                'field_type' => 'radio',
                'options' => json_encode(['Yes', 'No']),
            ],
             [
                'question' => 'Primary reasons for leaving?',
                'field_type' => 'checkbox',
                'options' => json_encode(['Career growth', 'Work environment', 'Compensation', 'Management issues', 'Other']),
            ],
            [
                'question' => 'How would you rate your overall experience with the company?',
                'field_type' => 'select',
                'options' => json_encode(['Excellent', 'Good', 'Average', 'Poor']),
            ],
            [
                'question' => 'Elaborate your above selection or specify if theres any other reasons for leaving Fidelis.?',
                'field_type' => 'textarea',
                'options' => null,
            ],
            [
                'question' => 'Is there anything the company could have done to prevent you from leaving this position?',
                'field_type' => 'textarea',
                'options' => null,
            ],
            [
                'question' => 'Would you consider returning to the company in the future?',
                'field_type' => 'radio',
                'options' => json_encode(['Yes', 'No', 'Maybe']),
            ],
            [
                'question' => 'New organization name?',
                'field_type' => 'text',
                'options' => null,
            ],
            [
                'question' => 'Position offered in new organization?',
                'field_type' => 'text',
                'options' => null,
            ],
            [
                'question' => 'Advantages you see in the new position?',
                'field_type' => 'radio',
                'options' => json_encode(['Pay', 'Career Progression', 'Personal Reasons', 'Workload', 'Work Environment', 'Nature Of Work',  'HR Policies','Other']), 
            ],
            [
                'question' => 'Elaborate your above selection or specify if theres any other reasons for joining new organization?',
                'field_type' => 'text',
                'options' => null,
            ],
            [
                'question' => 'CTC (INR per month) offered in new organization?',
                'field_type' => 'text',
                'options' => null,
            ],
            [
                'question' => 'Your Signature',
                'field_type' => 'file',
                'options' => null,
            ],
            [
                'question' => 'Google & LinkedIn review with screenshot upload',
                'field_type' => 'file',
                'options' => null,
            ],
        ]); 

    }
}
