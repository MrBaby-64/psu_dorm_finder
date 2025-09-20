<?php
// database/seeders/UserSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin (PSU)
        User::create([
            'name' => 'PSU Administrator',
            'email' => 'admin@psu.edu.ph',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'role' => User::ROLE_ADMIN,
            'phone' => '+63 945 123 4567',
            'is_verified' => true,
        ]);

        // Landlords
        $landlords = [
            [
                'name' => 'Maria Santos',
                'email' => 'maria.santos@gmail.com',
                'phone' => '+63 917 234 5678',
                'is_verified' => true,
            ],
            [
                'name' => 'Roberto Cruz',
                'email' => 'roberto.cruz@yahoo.com',
                'phone' => '+63 928 345 6789',
                'is_verified' => true,
            ],
            [
                'name' => 'Elena Rodriguez',
                'email' => 'elena.rodriguez@gmail.com',
                'phone' => '+63 939 456 7890',
                'is_verified' => false, // Some unverified landlords
            ]
        ];

        foreach ($landlords as $landlord) {
            User::create([
                'name' => $landlord['name'],
                'email' => $landlord['email'],
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role' => User::ROLE_LANDLORD,
                'phone' => $landlord['phone'],
                'is_verified' => $landlord['is_verified'],
            ]);
        }

        // Students/Tenants
        $tenants = [
            ['name' => 'Juan Dela Cruz', 'email' => 'juan.delacruz@student.psu.edu.ph'],
            ['name' => 'Ana Reyes', 'email' => 'ana.reyes@student.psu.edu.ph'],
            ['name' => 'Miguel Torres', 'email' => 'miguel.torres@student.psu.edu.ph'],
            ['name' => 'Sofia Garcia', 'email' => 'sofia.garcia@student.psu.edu.ph'],
            ['name' => 'Carlos Mendoza', 'email' => 'carlos.mendoza@student.psu.edu.ph'],
            ['name' => 'Isabella Flores', 'email' => 'isabella.flores@student.psu.edu.ph'],
            ['name' => 'Diego Morales', 'email' => 'diego.morales@student.psu.edu.ph'],
            ['name' => 'Camila Herrera', 'email' => 'camila.herrera@student.psu.edu.ph'],
        ];

        foreach ($tenants as $tenant) {
            User::create([
                'name' => $tenant['name'],
                'email' => $tenant['email'],
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role' => User::ROLE_TENANT,
                'phone' => '+63 9' . rand(10, 99) . ' ' . rand(100, 999) . ' ' . rand(1000, 9999),
                'is_verified' => false, // Students don't need PSU verification
            ]);
        }
    }
}