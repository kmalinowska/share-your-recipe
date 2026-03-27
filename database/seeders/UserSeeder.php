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
        // Main test user - known password for logging in during development
        User::firstOrCreate(
            ['email' => 'test@gmail.com'],
            [
                'name'     => 'Test User',
                'password' => Hash::make('password'),
                'avatar'   => null,
            ]
        );

        User::firstOrCreate(
            ['email' => 'test2@gmail.com'],
            [
                'name'     => 'Test User2',
                'password' => Hash::make('password'),
                'avatar'   => null,
            ]
        );

        // Additional random users via Factory (optional)
        // User::factory(10)->create();
    }
}
