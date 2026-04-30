<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Manager;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InsertManagerUsers extends Command
{
    protected $signature = 'insert:manager-users';
    protected $description = 'Insert managers into users table with manager role';

    public function handle()
    {
        $this->info('Starting manager user creation...');
        
        try {
            DB::beginTransaction();
            
            $managers = Manager::all();
            $createdCount = 0;
            $existingCount = 0;
            
            foreach ($managers as $manager) {
                // Check if user already exists
                $existingUser = User::where('email', $manager->manager_email)->first();
                
                if ($existingUser) {
                    // Ensure existing user has manager role
                    if (!$existingUser->hasRole('manager')) {
                        $existingUser->assignRole('manager');
                        $this->line("Assigned manager role to existing user: {$manager->manager_email}");
                    }
                    $existingCount++;
                    continue;
                }
                
                // Create new user for manager
                $user = User::create([
                    'name' => $manager->manager_name,
                    'email' => $manager->manager_email,
                    'password' => Hash::make(Str::random(12)), // Random password for security
                    'is_first_login' => true,
                    'first_login_at' => null
                ]);
                
                // Assign manager role
                $user->assignRole('manager');
                
                $createdCount++;
            }
            
            DB::commit();
            
            $this->info("Successfully created $createdCount new manager users.");
            if ($existingCount > 0) {
                $this->info("Found $existingCount existing manager users.");
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Error creating manager users: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}
