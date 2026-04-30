<?php

namespace App\Console\Commands;

use App\Models\InterviewQuestion;
use Illuminate\Console\Command;

class SeedExitInterviewQuestions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:seed-exit-interview-questions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed HR exit interview questions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $questions = [
            [
                'question' => 'What are the main reasons you decided to leave, and was there anything that could have made you stay?',
                'type' => 'text',
                'description' => null,
                'options' => null,
                'is_required' => true,
                'order' => 1,
                'is_active' => true
            ],
            [
                'question' => 'Did you feel supported by your manager and team, and was communication effective?',
                'type' => 'text',
                'description' => null,
                'options' => null,
                'is_required' => true,
                'order' => 2,
                'is_active' => true
            ],
            [
                'question' => 'Were there enough opportunities for you to grow and develop professionally during your time here?',
                'type' => 'text',
                'description' => null,
                'options' => null,
                'is_required' => true,
                'order' => 3,
                'is_active' => true
            ],
            [
                'question' => 'How would you describe the company\'s culture, and what aspects of it worked or didn\'t work for you?',
                'type' => 'text',
                'description' => null,
                'options' => null,
                'is_required' => true,
                'order' => 4,
                'is_active' => true
            ],
            [
                'question' => 'Is there anything we can do to improve, for future employees or the organization as a whole?',
                'type' => 'text',
                'description' => null,
                'options' => null,
                'is_required' => true,
                'order' => 5,
                'is_active' => true
            ]
        ];

        foreach ($questions as $questionData) {
            InterviewQuestion::updateOrCreate(
                ['question' => $questionData['question']],
                $questionData
            );
        }

        $this->info('Exit interview questions have been seeded successfully.');
    }
}
