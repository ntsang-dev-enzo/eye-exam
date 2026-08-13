<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Subject;
use App\Models\SchoolClass;
use App\Models\Question;
use App\Models\Answer;
use App\Models\Exam;
use App\Models\ExamAssignment;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TestDataSeeder extends Seeder
{
    public function run()
    {
        // Find Teacher 1
        $teacher = User::where('code', 'GV001')->first();
        if (!$teacher) {
            $this->command->error('Teacher GV001 not found. Run UserSeeder first.');
            return;
        }

        // Find Subject CS101
        $subject = Subject::where('code', 'CS101')->first();
        if (!$subject) {
            $this->command->error('Subject CS101 not found.');
            return;
        }

        // Assign subject to teacher
        $teacher->subjects()->syncWithoutDetaching([$subject->id]);

        // Create Students
        $students = collect();
        $students->push(User::where('code', 'SV001')->first());

        for ($i = 2; $i <= 5; $i++) {
            $students->push(User::firstOrCreate(
                ['code' => 'SV00' . $i],
                [
                    'name' => 'Student ' . $i,
                    'email' => 'student' . $i . '@example.com',
                    'password' => Hash::make('password'),
                    'role' => 'student',
                    'status' => 'active',
                ]
            ));
        }

        // Create Class
        $class = SchoolClass::firstOrCreate(
            ['code' => 'CS101-01'],
            [
                'name' => 'Lớp C++ Sáng T2',
                'teacher_id' => $teacher->id,
                'description' => 'Lớp học phần cơ sở C++',
                'status' => true,
            ]
        );

        // Attach students to class
        $class->students()->syncWithoutDetaching($students->pluck('id'));

        // Create Questions
        $questions = [];
        for ($i = 1; $i <= 10; $i++) {
            $q = Question::create([
                'subject_id' => $subject->id,
                'created_by' => $teacher->id,
                'content' => 'Câu hỏi test số ' . $i . ' về C++',
                'type' => 'multiple_choice',
                'difficulty' => 'medium',
            ]);
            
            Answer::insert([
                ['question_id' => $q->id, 'label' => 'A', 'content' => 'Đáp án A ' . $i, 'is_correct' => true],
                ['question_id' => $q->id, 'label' => 'B', 'content' => 'Đáp án B ' . $i, 'is_correct' => false],
                ['question_id' => $q->id, 'label' => 'C', 'content' => 'Đáp án C ' . $i, 'is_correct' => false],
                ['question_id' => $q->id, 'label' => 'D', 'content' => 'Đáp án D ' . $i, 'is_correct' => false],
            ]);
            
            $questions[] = $q->id;
        }

        // Create Exams
        $exam1 = Exam::create([
            'subject_id' => $subject->id,
            'created_by' => $teacher->id,
            'code' => strtoupper(Str::random(8)),
            'title' => 'Đề thi Giữa Kỳ C++',
            'duration_minutes' => 45,
            'total_questions' => 5,
            'status' => 'published',
            'shuffle_questions' => true,
        ]);

        $exam2 = Exam::create([
            'subject_id' => $subject->id,
            'created_by' => $teacher->id,
            'code' => strtoupper(Str::random(8)),
            'title' => 'Đề thi Cuối Kỳ C++ (Chưa mở)',
            'duration_minutes' => 60,
            'total_questions' => 10,
            'status' => 'closed',
            'shuffle_questions' => true,
        ]);

        // Attach questions to exams
        foreach (array_slice($questions, 0, 5) as $idx => $qId) {
            $exam1->questions()->attach($qId, ['question_order' => $idx + 1, 'points' => 2]);
        }
        foreach ($questions as $idx => $qId) {
            $exam2->questions()->attach($qId, ['question_order' => $idx + 1, 'points' => 1]);
        }

        // Assign exams to students
        foreach ($students as $student) {
            ExamAssignment::create([
                'exam_id' => $exam1->id,
                'class_id' => $class->id,
                'student_id' => $student->id,
                'assigned_by' => $teacher->id,
                'assigned_at' => now(),
            ]);
            
            ExamAssignment::create([
                'exam_id' => $exam2->id,
                'class_id' => $class->id,
                'student_id' => $student->id,
                'assigned_by' => $teacher->id,
                'assigned_at' => now(),
            ]);
        }

        $this->command->info('Test data seeded successfully!');
    }
}
