<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin
        User::create([
            'name' => 'System Admin',
            'code' => 'AD001',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        // Teacher 1
        User::create([
            'name' => 'Nguyễn Văn Giảng Viên',
            'code' => 'GV001',
            'email' => 'teacher@example.com',
            'password' => Hash::make('password'),
            'role' => 'teacher',
            'department' => 'Khoa CNTT',
            'status' => 'active',
        ]);

        // Teacher 2
        User::create([
            'name' => 'Trần Thị Giảng Viên 2',
            'code' => 'GV002',
            'email' => 'teacher2@example.com',
            'password' => Hash::make('password'),
            'role' => 'teacher',
            'department' => 'Khoa Kinh tế',
            'status' => 'active',
        ]);

        // Student
        User::create([
            'name' => 'Student One',
            'code' => 'SV001',
            'email' => 'student@example.com',
            'password' => Hash::make('password'),
            'role' => 'student',
            'status' => 'active',
        ]);
    }
}
