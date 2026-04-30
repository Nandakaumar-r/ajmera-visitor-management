<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class AssignReceptionRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'role:assign-reception {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign reception role to a user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User with email {$email} not found!");
            return 1;
        }

        $user->assignRole('reception');
        $this->info("Reception role assigned to user {$email} successfully!");
        
        return 0;
    }
}
