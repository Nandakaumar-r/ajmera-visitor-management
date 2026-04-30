<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ProcessUserPhotos extends Command
{
    protected $signature = 'users:process-photos {--force : Force reprocessing of all photos}';
    protected $description = 'Process user photos to generate face embeddings';

    public function handle()
    {
        $users = User::whereNull('face_embedding')
            ->when(!$this->option('force'), function($query) {
                return $query->whereNotNull('profile_picture');
            })
            ->get();

        if ($users->isEmpty()) {
            $this->info('No users found that need photo processing.');
            return;
        }

        $this->info(sprintf('Processing photos for %d users...', $users->count()));
        $bar = $this->output->createProgressBar($users->count());

        foreach ($users as $user) {
            if (!$user->profile_picture) {
                $this->warn(sprintf('User %s has no profile picture uploaded.', $user->name));
                continue;
            }

            $photoPath = Storage::path($user->profile_picture);
            if (!file_exists($photoPath)) {
                $this->warn(sprintf('Profile picture file not found for user %s: %s', $user->name, $user->profile_picture));
                continue;
            }

            try {
                // Process the photo using our Python script
                $pythonScript = base_path('app/Python/face_detection_helper.py');
                $command = sprintf('python "%s" "%s"', $pythonScript, $photoPath);
                $output = shell_exec($command);

                if (!$output) {
                    $this->warn(sprintf('No output from face detection script for user %s', $user->name));
                    continue;
                }

                $result = json_decode($output, true);
                if (isset($result['error'])) {
                    $this->warn(sprintf('Error processing photo for user %s: %s', $user->name, $result['error']));
                    continue;
                }

                // Update user with face embedding
                $user->face_embedding = json_encode($result['features']);
                $user->save();

                $bar->advance();

            } catch (\Exception $e) {
                $this->error(sprintf('Exception processing photo for user %s: %s', $user->name, $e->getMessage()));
            }
        }

        $bar->finish();
        $this->newLine();
        $this->info('Photo processing completed.');
    }
}
