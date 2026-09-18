<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 🔹 Admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('123456'),
            ]
        );

        $admin->assignRole('admin');

        // $users = User::firstOrCreate(
        //     ['email' => 'user@gmail.com'],
        //     [
        //         'name' => 'User',
        //         'password' => Hash::make('123456'),
        //     ]
        // );

        // $users->assignRole('user');

        // 🔹 Manager user
        $manager = User::firstOrCreate(
            ['email' => 'manager@gmail.com'],
            [
                'name' => 'Manager',
                'password' => Hash::make('123456'),
            ]
        );

        $manager->assignRole('manager');

        // 🔹 Staff user
        $staff = User::firstOrCreate(
            ['email' => 'staff@gmail.com'],
            [
                'name' => 'Staff User',
                'password' => Hash::make('123456'),
            ]
        );

        $staff->assignRole('staff');
    }
}
