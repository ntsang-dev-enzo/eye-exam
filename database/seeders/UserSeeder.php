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
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'System Admin',
                'code' => 'AD001',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'status' => 'active',
            ]
        );

        // Teacher 1
        User::firstOrCreate(
            ['email' => 'teacher@example.com'],
            [
                'name' => 'Nguyễn Văn Giảng Viên',
                'code' => 'GV001',
                'password' => Hash::make('password'),
                'role' => 'teacher',
                'department' => 'Khoa CNTT',
                'status' => 'active',
            ]
        );

        // Teacher 2
        User::firstOrCreate(
            ['email' => 'teacher2@example.com'],
            [
                'name' => 'Trần Thị Giảng Viên 2',
                'code' => 'GV002',
                'password' => Hash::make('password'),
                'role' => 'teacher',
                'department' => 'Khoa Kinh tế',
                'status' => 'active',
            ]
        );

        // Student
        User::firstOrCreate(
            ['email' => 'student@example.com'],
            [
                'name' => 'Student One',
                'code' => 'SV001',
                'password' => Hash::make('password'),
                'role' => 'student',
                'status' => 'active',
            ]
        );
    }
}
