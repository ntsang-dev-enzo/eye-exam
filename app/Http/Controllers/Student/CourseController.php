<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * Display active courses for students (Read-Only Card layout).
     */
    public function index(Request $request)
    {
        $query = Course::with('subjects')->where('status', 'active');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('semester')) {
            $query->where('semester', $request->semester);
        }

        $courses = $query->orderBy('created_at', 'desc')->get();
        $totalCredits = $courses->flatMap->subjects->unique('id')->sum('credits');

        // Tính số tín chỉ đã hoàn thành của sinh viên
        $studentId = auth()->id();
        $passedSubjectIds = \App\Models\ExamAttempt::where('exam_attempts.student_id', $studentId)
            ->where('exam_attempts.status', 'submitted')
            ->where('exam_attempts.score_value', '>=', 5.0)
            ->join('exams', 'exam_attempts.exam_id', '=', 'exams.id')
            ->pluck('exams.subject_id')
            ->filter()
            ->unique();

        // Nếu sinh viên chưa có bài thi >= 5.0, tính các bài thi đã hoàn thành nộp bài
        if ($passedSubjectIds->isEmpty()) {
            $passedSubjectIds = \App\Models\ExamAttempt::where('exam_attempts.student_id', $studentId)
                ->where('exam_attempts.status', 'submitted')
                ->join('exams', 'exam_attempts.exam_id', '=', 'exams.id')
                ->pluck('exams.subject_id')
                ->filter()
                ->unique();
        }

        $completedCredits = \App\Models\Subject::whereIn('id', $passedSubjectIds)->sum('credits');
        $requiredCredits = 150; // Tổng tín chỉ tốt nghiệp / hoàn thành chương trình
        $remainingCredits = max(0, $requiredCredits - $completedCredits);
        $progressPercent = min(100, round(($completedCredits / $requiredCredits) * 100, 1));

        return view('student.courses.index', compact(
            'courses', 
            'totalCredits', 
            'completedCredits', 
            'requiredCredits', 
            'remainingCredits', 
            'progressPercent'
        ));
    }
}
