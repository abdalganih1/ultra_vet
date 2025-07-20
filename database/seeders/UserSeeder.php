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
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Data Entry User',
            'email' => 'dataentry@example.com',
            'password' => Hash::make('password'),
            'role' => 'data_entry',
        ]);

        User::create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'role' => 'regular_user',
        ]);
         User::create([ // لإظهار دور الضيف في لوحة التحكم إذا سجل دخول
            'name' => 'Guest User Example',
            'email' => 'guest@example.com',
            'password' => Hash::make('password'),
            'role' => 'guest_user',
        ]);
    }
}