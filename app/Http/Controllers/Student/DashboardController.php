<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\ExamAssignment;
use App\Models\ExamAttempt;
use App\Models\SchoolClass;
use App\Models\Subject;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Show the student dashboard.
     */
    public function index(Request $request)
    {
        $studentId = auth()->id();
        $student = auth()->user()->load(['schoolClasses.course']);
        $now = now();

        $studentClassIds = $student->schoolClasses->pluck('id')->toArray();

        // 1. Fetch all assigned exams (by student_id or student's class_id)
        $assignments = ExamAssignment::with(['exam.subject.courses', 'exam.category', 'schoolClass.course'])
            ->where(function ($q) use ($studentId, $studentClassIds) {
                $q->where('student_id', $studentId);
                if (!empty($studentClassIds)) {
                    $q->orWhereIn('class_id', $studentClassIds);
                }
            })
            ->latest('id')
            ->get();

        $assignedExams = $assignments->map(function ($assignment) use ($studentId, $now) {
            $exam = $assignment->exam;
            if (!$exam) {
                return null;
            }

            // Gán thông tin kỳ học cho bài thi
            $examSemester = $assignment->schoolClass?->course?->semester 
                ?? ($exam->subject?->courses?->first()?->semester ?? 'Học kỳ 1');
            $exam->semester = $examSemester;
            
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
        })->filter()->unique('id')->values();

        // 2. Fetch attempts for recent history
        $attempts = ExamAttempt::with(['exam.subject', 'exam.category'])
            ->where('student_id', $studentId)
            ->latest('id')
            ->get();

        // 3. Stats Calculation (Bỏ điểm trung bình theo yêu cầu)
        $submittedAttempts = $attempts->where('status', 'submitted');
        $totalSubmitted = $submittedAttempts->count();
        $inProgressCount = $attempts->where('status', 'in_progress')->count();
        $totalAssigned = $assignedExams->count();

        $stats = [
            'total_assigned' => $totalAssigned,
            'total_submitted' => $totalSubmitted,
            'in_progress_count' => $inProgressCount,
        ];

        // 4. Danh sách các học kỳ (từ Course hoặc mặc định)
        $semesters = Course::distinct()->pluck('semester')->filter()->values();
        if ($semesters->isEmpty()) {
            $semesters = collect(['Học kỳ 1', 'Học kỳ 2', 'Học kỳ 3']);
        } else {
            $semesters = $semesters->sort()->values();
        }

        // 5. Danh sách các môn học kèm kỳ học
        $subjectsWithSemester = Subject::with('courses')
            ->where('status', true)
            ->orderBy('name')
            ->get()
            ->map(function ($subj) {
                $sem = $subj->courses->first()?->semester ?? 'Học kỳ 1';
                return [
                    'id' => $subj->id,
                    'name' => $subj->name,
                    'code' => $subj->code,
                    'semester' => $sem,
                ];
            });

        // 6. Xử lý bộ lọc nếu có request
        $selectedSemester = $request->input('semester');
        $selectedSubjectId = $request->input('subject_id');

        $filteredExams = $assignedExams;
        if (!empty($selectedSemester)) {
            $filteredExams = $filteredExams->filter(fn($e) => $e->semester === $selectedSemester)->values();
        }
        if (!empty($selectedSubjectId)) {
            $filteredExams = $filteredExams->filter(fn($e) => (int) $e->subject_id === (int) $selectedSubjectId)->values();
        }

        return view('student.dashboard', compact(
            'assignedExams',
            'filteredExams',
            'attempts',
            'stats',
            'semesters',
            'subjectsWithSemester',
            'selectedSemester',
            'selectedSubjectId'
        ));
    }
}

