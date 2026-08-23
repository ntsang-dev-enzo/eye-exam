<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\ExamAttempt;

class CheckActiveExam
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->role === 'student') {
            // Check if they are already on the exam taking route or submitting or cheating
            $allowedRoutes = [
                'student.exams.take',
                'student.exams.submit',
                'student.exams.cheat',
                'student.exams.save-answer',
                'student.exams.sync-offline'
            ];
            
            if (!in_array($request->route()->getName(), $allowedRoutes)) {
                // Look for in_progress attempt
                $activeAttempt = ExamAttempt::where('student_id', auth()->id())
                    ->where('status', 'in_progress')
                    ->latest()
                    ->first();
                    
                if ($activeAttempt) {
                    return redirect()->route('student.exams.take', $activeAttempt->exam_id)
                        ->with('error', 'Bạn đang có bài thi dang dở, không thể rời khỏi trang lúc này!');
                }
            }
        }

        return $next($request);
    }
}
