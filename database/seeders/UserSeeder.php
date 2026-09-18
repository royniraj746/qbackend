<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 🔹 Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('123456'),
            ]
        );

        $admin->assignRole('admin');

        // 🔹 Normal User
        $user = User::firstOrCreate(
            ['email' => 'user@gmail.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('123456'),
            ]
        );

        $user->assignRole(roles: 'user');
    }
}
