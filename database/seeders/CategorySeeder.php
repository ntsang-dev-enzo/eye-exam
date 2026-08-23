<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Subject;
use App\Models\Category;
use App\Models\Question;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $teacher = User::where('role', 'teacher')->first();
        $subjects = Subject::all();

        if ($teacher && $subjects->count() > 0) {
            foreach ($subjects as $subject) {
                $cat1 = Category::firstOrCreate([
                    'name' => 'Chương 1: Khái niệm & Cú pháp cơ bản',
                    'subject_id' => $subject->id,
                ], [
                    'created_by' => $teacher->id,
                    'description' => 'Các câu hỏi kiến thức nền tảng',
                ]);

                $cat2 = Category::firstOrCreate([
                    'name' => 'Chương 2: Cấu trúc điều khiển & Vòng lặp',
                    'subject_id' => $subject->id,
                ], [
                    'created_by' => $teacher->id,
                    'description' => 'Các câu hỏi về câu lệnh rẽ nhánh và vòng lặp',
                ]);

                $cat3 = Category::firstOrCreate([
                    'name' => 'Chương 3: Hàm & Mảng dữ liệu',
                    'subject_id' => $subject->id,
                ], [
                    'created_by' => $teacher->id,
                    'description' => 'Các câu hỏi nâng cao',
                ]);

                // Update existing questions for this subject
                $questions = Question::where('subject_id', $subject->id)->get();
                foreach ($questions as $index => $q) {
                    if ($index % 3 === 0) {
                        $q->update(['category_id' => $cat1->id]);
                    } elseif ($index % 3 === 1) {
                        $q->update(['category_id' => $cat2->id]);
                    } else {
                        $q->update(['category_id' => $cat3->id]);
                    }
                }
            }
            $this->command->info('CategorySeeder completed successfully!');
        }
    }
}
