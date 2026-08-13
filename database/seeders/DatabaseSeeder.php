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
        $this->call([
            UserSeeder::class,
        ]);

        $subjects = [
            ['code' => 'CS101', 'name' => 'Lập trình C++', 'description' => 'Cơ bản C++', 'status' => true],
            ['code' => 'CS102', 'name' => 'Toán Rời Rạc', 'description' => 'Toán học máy tính', 'status' => true],
            ['code' => 'CS103', 'name' => 'Cơ sở Dữ liệu', 'description' => 'SQL & NoSQL', 'status' => true],
            ['code' => 'CS104', 'name' => 'Phát triển Web', 'description' => 'HTML, CSS, JS, PHP', 'status' => true],
        ];

        foreach ($subjects as $subject) {
            \App\Models\Subject::firstOrCreate(['code' => $subject['code']], $subject);
        }

        $this->call([
            TestDataSeeder::class,
        ]);
    }
}
