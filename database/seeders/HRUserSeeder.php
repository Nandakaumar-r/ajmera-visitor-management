<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class HRUserSeeder extends Seeder
{
    public function run()
    {
        // Create HR role if it doesn't exist
        $hrRole = Role::firstOrCreate(['name' => 'HR']);

        // Create HR user
        $user = User::create([
            'name' => 'HR Admin',
            'email' => 'hr@example.com',
            'password' => Hash::make('hr@12345'),
        ]);

        // Assign HR role to user
        $user->assignRole($hrRole);
    }
}
