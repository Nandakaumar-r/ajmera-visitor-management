<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class ReceptionistSeeder extends Seeder
{
    public function run()
    {
        // Create reception role if it doesn't exist
        $receptionRole = Role::firstOrCreate(['name' => 'reception']);

        // Create receptionist user
        $user = User::create([
            'name' => 'Reception Staff',
            'email' => 'reception@example.com',
            'password' => Hash::make('Reception@123'),
            'is_first_login' => false,
        ]);

        // Assign role to user
        $user->assignRole($receptionRole);
    }
}
