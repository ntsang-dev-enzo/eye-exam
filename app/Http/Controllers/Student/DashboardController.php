<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ExamAssignment;
use App\Models\ExamAttempt;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Show the student dashboard.
     */
    public function index(Request $request)
    {
        $studentId = auth()->id();
        $now = now();

        // 1. Fetch all assigned exams
        $assignments = ExamAssignment::with(['exam.subject', 'exam.category'])
            ->where('student_id', $studentId)
            ->latest('id')
            ->get();

        $assignedExams = $assignments->map(function ($assignment) use ($studentId, $now) {
            $exam = $assignment->exam;
            
            $attempts = ExamAttempt::where('exam_id', $exam->id)
                ->where('student_id', $studentId)
                ->latest('id')
                ->get();

            $latestAttempt = $attempts->first();
            $inProgressAttempt = $attempts->where('status', 'in_progress')->first();
            $submittedAttempts = $attempts->where('status', 'submitted');
            $submittedCount = $submittedAttempts->count();
            $highestScore = $submittedAttempts->max('score_value');

            $isTimeEnded = $exam->end_at && $now->gt($exam->end_at);
            $isTimeNotStarted = $exam->start_at && $now->lt($exam->start_at);
            $isPublished = in_array($exam->status, ['published', 'ongoing']);
            $isUnlimited = $exam->isUnlimitedAttempts();
            $hasRetakeAvailable = ($isUnlimited || $submittedCount < ($exam->max_attempts ?? 1));

            $exam->attempt_status = $latestAttempt ? $latestAttempt->status : null;
            $exam->submitted_count = $submittedCount;
            $exam->score = $highestScore !== null ? $highestScore : ($latestAttempt ? $latestAttempt->score_value : null);
            $exam->has_in_progress = (bool) $inProgressAttempt;
            $exam->can_review = ($submittedCount > 0 && (bool) $exam->allow_review);
            $exam->can_take = $isPublished && !$isTimeEnded && !$isTimeNotStarted && ($inProgressAttempt || $hasRetakeAvailable);

            if ($isTimeNotStarted) {
                $exam->calculated_status = 'Sắp thi';
                $exam->status_color = 'amber';
            } elseif ($isTimeEnded) {
                $exam->calculated_status = 'Đã hết hạn';
                $exam->status_color = 'rose';
            } elseif (!$isPublished) {
                $exam->calculated_status = 'Đang khóa';
                $exam->status_color = 'gray';
            } elseif ($inProgressAttempt) {
                $exam->calculated_status = 'Đang làm dở';
                $exam->status_color = 'blue';
            } elseif ($submittedCount > 0) {
                if ($hasRetakeAvailable) {
                    $exam->calculated_status = 'Có thể thi lại';
                    $exam->status_color = 'indigo';
                } else {
                    $exam->calculated_status = 'Đã nộp bài';
                    $exam->status_color = 'emerald';
                }
            } else {
                $exam->calculated_status = 'Chưa làm';
                $exam->status_color = 'blue';
            }

            return $exam;
        });

        // 2. Fetch attempts
        $attempts = ExamAttempt::with(['exam.subject', 'exam.category'])
            ->where('student_id', $studentId)
            ->latest('id')
            ->get();

        // 3. Stats Calculation
        $submittedAttempts = $attempts->where('status', 'submitted');
        $totalSubmitted = $submittedAttempts->count();
        $avgScore = $totalSubmitted > 0 ? round($submittedAttempts->avg('score_value'), 1) : null;
        $inProgressCount = $attempts->where('status', 'in_progress')->count();
        $totalAssigned = $assignedExams->count();

        $stats = [
            'total_assigned' => $totalAssigned,
            'total_submitted' => $totalSubmitted,
            'avg_score' => $avgScore,
            'in_progress_count' => $inProgressCount,
        ];

        // 4. Extract unique categories & subjects for filtering
        $categories = $assignedExams->map(function ($exam) {
            return $exam->category ? [
                'id' => 'cat_' . $exam->category->id,
                'name' => $exam->category->name,
                'type' => 'category'
            ] : ($exam->subject ? [
                'id' => 'subj_' . $exam->subject->id,
                'name' => $exam->subject->name,
                'type' => 'subject'
            ] : null);
        })->filter()->unique('id')->values();

        return view('student.dashboard', compact('assignedExams', 'attempts', 'stats', 'categories'));
    }
}
