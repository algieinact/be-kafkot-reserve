<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@kafkot.com',
            'password' => Hash::make('password'),
            'username' => 'admin',
            'full_name' => 'Administrator',
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Staff User',
            'email' => 'staff@kafkot.com',
            'password' => Hash::make('password'),
            'username' => 'staff',
            'full_name' => 'Staff Member',
            'role' => 'staff',
        ]);
    }
}
