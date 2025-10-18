<?php


namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Initialize system administrator account
        User::firstOrCreate(
            ['email' => 'psuteam001@gmail.com'],
            [
                'name' => 'PSU Administrator',
                'email' => 'psuteam001@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('admin001'),
                'role' => User::ROLE_ADMIN,
                'phone' => '+63 945 000 0001',
                'is_verified' => true,
            ]
        );

        // Users register through application interface through the application
    }
}