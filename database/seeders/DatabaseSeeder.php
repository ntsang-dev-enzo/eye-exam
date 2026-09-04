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
            ['code' => 'CS101', 'name' => 'Lập trình C++', 'credits' => 4, 'description' => 'Cơ bản C++', 'status' => true],
            ['code' => 'CS102', 'name' => 'Toán Rời Rạc', 'credits' => 3, 'description' => 'Toán học máy tính', 'status' => true],
            ['code' => 'CS103', 'name' => 'Cơ sở Dữ liệu', 'credits' => 3, 'description' => 'SQL & NoSQL', 'status' => true],
            ['code' => 'CS104', 'name' => 'Phát triển Web', 'credits' => 3, 'description' => 'HTML, CSS, JS, PHP', 'status' => true],
        ];

        foreach ($subjects as $subject) {
            \App\Models\Subject::updateOrCreate(['code' => $subject['code']], $subject);
        }

        $this->call([
            TestDataSeeder::class,
        ]);
    }
}
