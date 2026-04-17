<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name'     => 'Admin',
                'password' => Hash::make('password'),
                'avatar' => null,
                'role'     => 'admin',
            ]
        );

        // Main test user - known password for logging in during development
        User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name'     => 'user',
                'password' => Hash::make('password'),
                'avatar'   => null,
                'role' => 'user'
            ]
        );

        User::firstOrCreate(
            ['email' => 'user2@example.com'],
            [
                'name'     => 'user2',
                'password' => Hash::make('password'),
                'avatar'   => null,
                'role' => 'user'
            ]
        );

        // Additional random users via Factory (optional)
        // User::factory(10)->create();
    }
}
