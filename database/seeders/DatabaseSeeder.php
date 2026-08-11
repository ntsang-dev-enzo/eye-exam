<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call([
            UserSeeder::class,
        ]);

        \App\Models\Subject::insert([
            ['code' => 'CS101', 'name' => 'Lập trình C++', 'description' => 'Cơ bản C++', 'status' => true],
            ['code' => 'CS102', 'name' => 'Toán Rời Rạc', 'description' => 'Toán học máy tính', 'status' => true],
            ['code' => 'CS103', 'name' => 'Cơ sở Dữ liệu', 'description' => 'SQL & NoSQL', 'status' => true],
            ['code' => 'CS104', 'name' => 'Phát triển Web', 'description' => 'HTML, CSS, JS, PHP', 'status' => true],
        ]);
    }
}
