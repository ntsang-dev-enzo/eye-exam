<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\ExamAnswer;
use Illuminate\Http\Request;

class ExamController extends Controller
{
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

        // Check if already attempted
        $attempt = ExamAttempt::where('exam_id', $exam->id)
            ->where('student_id', auth()->id())
            ->first();

        if ($attempt) {
            if ($attempt->status === 'submitted') {
                return back()->withErrors(['code' => 'Bạn đã hoàn thành bài thi này rồi.']);
            }
            return redirect()->route('student.exams.take', $exam);
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
            ->first();

        if (!$attempt || $attempt->status !== 'in_progress') {
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
}
