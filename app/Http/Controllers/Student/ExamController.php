<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\ExamAnswer;
use App\Models\ExamAssignment;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function index()
    {
        $assignments = ExamAssignment::with(['exam.subject'])
            ->where('student_id', auth()->id())
            ->latest()
            ->get();
            
        // Calculate status for each assigned exam
        $exams = $assignments->map(function ($assignment) {
            $exam = $assignment->exam;
            $now = now();
            
            $attempt = ExamAttempt::where('exam_id', $exam->id)
                ->where('student_id', auth()->id())
                ->latest()
                ->first();
                
            $submittedCount = ExamAttempt::where('exam_id', $exam->id)
                ->where('student_id', auth()->id())
                ->where('status', 'submitted')
                ->count();
                
            $exam->attempt_status = $attempt ? $attempt->status : null;
            $exam->score = $attempt ? $attempt->score_value : null;
            
            if ($exam->start_at && $now->lt($exam->start_at)) {
                $exam->calculated_status = 'Sắp thi';
                $exam->status_color = 'amber';
            } elseif ($exam->end_at && $now->gt($exam->end_at)) {
                $exam->calculated_status = 'Đã hết hạn';
                $exam->status_color = 'rose';
            } elseif ($exam->status !== 'published') {
                $exam->calculated_status = 'Đang khóa';
                $exam->status_color = 'gray';
            } elseif ($attempt && $attempt->status === 'in_progress') {
                $exam->calculated_status = 'Đang thi';
                $exam->status_color = 'blue';
            } elseif ($submittedCount >= ($exam->max_attempts ?? 1)) {
                $exam->calculated_status = 'Đã nộp bài';
                $exam->status_color = 'emerald';
            } else {
                $exam->calculated_status = $submittedCount > 0 ? 'Thi lại' : 'Đang thi';
                $exam->status_color = 'blue';
            }
            
            return $exam;
        });
        
        return view('student.exams.index', compact('exams'));
    }

    public function join(Request $request)
    {
        $request->validate(['code' => 'required|string']);
        
        $exam = Exam::where('code', strtoupper($request->code))->first();
        
        if (!$exam) {
            return back()->withErrors(['code' => 'Mã đề thi không tồn tại.']);
        }
        
        if ($exam->status !== 'published' && $exam->status !== 'ongoing') {
            return back()->withErrors(['code' => 'Kỳ thi này hiện không mở.']);
        }
        
        if ($exam->start_at && now()->lt($exam->start_at)) {
            return back()->withErrors(['code' => 'Kỳ thi chưa bắt đầu.']);
        }
        
        if ($exam->end_at && now()->gt($exam->end_at)) {
            return back()->withErrors(['code' => 'Kỳ thi đã kết thúc.']);
        }

        // Check if there is an in_progress attempt
        $inProgress = ExamAttempt::where('exam_id', $exam->id)
            ->where('student_id', auth()->id())
            ->where('status', 'in_progress')
            ->first();

        if ($inProgress) {
            return redirect()->route('student.exams.take', $exam);
        }

        // Check max attempts
        $submittedCount = ExamAttempt::where('exam_id', $exam->id)
            ->where('student_id', auth()->id())
            ->where('status', 'submitted')
            ->count();

        if ($submittedCount >= ($exam->max_attempts ?? 1)) {
            return back()->withErrors(['code' => 'Bạn đã vượt quá số lần làm bài cho phép.']);
        }

        // Create new attempt
        ExamAttempt::create([
            'exam_id' => $exam->id,
            'student_id' => auth()->id(),
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        return redirect()->route('student.exams.take', $exam);
    }

    public function take(Exam $exam)
    {
        $attempt = ExamAttempt::where('exam_id', $exam->id)
            ->where('student_id', auth()->id())
            ->where('status', 'in_progress')
            ->latest()
            ->first();

        if (!$attempt) {
            return redirect()->route('student.dashboard')->with('error', 'Bạn không thể truy cập bài thi này.');
        }
        
        // Time check
        if ($exam->end_at && now()->gt($exam->end_at)) {
            return redirect()->route('student.exams.submit', $exam); // auto submit if late
        }

        $timeLimit = $exam->duration_minutes * 60;
        $elapsed = now()->diffInSeconds($attempt->started_at);
        $timeRemaining = max(0, $timeLimit - $elapsed);
        
        if ($timeRemaining <= 0) {
            return redirect()->route('student.exams.submit', $exam); // time's up
        }

        $exam->load(['questions.answers', 'subject']);

        return view('student.exams.take', compact('exam', 'attempt', 'timeRemaining'));
    }

    public function submit(Request $request, Exam $exam)
    {
        $attempt = ExamAttempt::where('exam_id', $exam->id)
            ->where('student_id', auth()->id())
            ->where('status', 'in_progress')
            ->first();

        if (!$attempt) {
            return redirect()->route('student.dashboard');
        }

        $answers = $request->input('answers', []);
        $totalScore = 0;
        $correct = 0;
        $wrong = 0;
        $unanswered = 0;

        foreach ($exam->questions as $question) {
            $isCorrect = null;
            $pointsEarned = 0;
            $studentAnswerId = $answers[$question->id] ?? null;

            if ($question->type === 'multiple_choice') {
                if ($studentAnswerId) {
                    $correctAnswer = $question->answers()->where('is_correct', true)->first();
                    if ($correctAnswer && $correctAnswer->id == $studentAnswerId) {
                        $isCorrect = true;
                        $pointsEarned = $question->pivot->points ?? 1;
                        $correct++;
                    } else {
                        $isCorrect = false;
                        $wrong++;
                    }
                } else {
                    $unanswered++;
                }
            }

            $totalScore += $pointsEarned;

            ExamAnswer::create([
                'attempt_id' => $attempt->id,
                'question_id' => $question->id,
                'answer_id' => $studentAnswerId,
                'is_correct' => $isCorrect,
                'points_earned' => $pointsEarned,
                'answered_at' => now(),
            ]);
        }

        $attempt->update([
            'status' => 'submitted',
            'submitted_at' => now(),
            'score_value' => $totalScore,
            'correct_answers' => $correct,
            'wrong_answers' => $wrong,
            'unanswered' => $unanswered,
        ]);

        return redirect()->route('student.dashboard')->with('success', 'Nộp bài thi thành công!');
    }

    public function cheat(Request $request, $attemptId)
    {
        $attempt = ExamAttempt::where('id', $attemptId)
            ->where('student_id', auth()->id())
            ->first();
            
        if ($attempt) {
            $attempt->increment('cheat_warnings');
            
            // If they pass out_time, add it
            if ($request->has('out_time')) {
                $attempt->increment('out_of_screen_time', (int) $request->out_time);
            }
        }
        
        return response()->json(['success' => true]);
    }

    public function review(Exam $exam)
    {
        if (!$exam->allow_review) {
            return redirect()->route('student.exams.index')->with('error', 'Kỳ thi này không cho phép xem lại bài.');
        }

        $attempt = ExamAttempt::with('answers')->where('exam_id', $exam->id)
            ->where('student_id', auth()->id())
            ->where('status', 'submitted')
            ->latest()
            ->first();

        if (!$attempt) {
            return redirect()->route('student.exams.index')->with('error', 'Không tìm thấy bài thi.');
        }

        $exam->load(['questions.answers']);
        $studentAnswers = $attempt->answers->keyBy('question_id');

        return view('student.exams.review', compact('exam', 'attempt', 'studentAnswers'));
    }
}
