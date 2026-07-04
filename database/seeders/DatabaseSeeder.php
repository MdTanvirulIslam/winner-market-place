<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Initial super admin — change the password after first login.
        User::firstOrCreate(
            ['email' => 'admin@winnerdevs.com'],
            [
                'name' => 'Winner Devs Admin',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
            ]
        );
    }
}
