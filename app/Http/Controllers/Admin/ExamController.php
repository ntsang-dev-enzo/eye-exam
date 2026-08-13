<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\ExamAttempt;

class ExamController extends Controller
{
    public function index(Request $request)
    {
        $query = Exam::with(['subject', 'creator'])->latest();
        
        if ($request->has('search') && $request->search != '') {
            $query->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('code', 'like', '%' . $request->search . '%');
        }
        
        $exams = $query->paginate(15);
        return view('admin.exams.index', compact('exams'));
    }

    public function monitor(Exam $exam)
    {
        return view('admin.exams.monitor', compact('exam'));
    }

    public function apiMonitor(Exam $exam)
    {
        $attempts = ExamAttempt::with('student')
            ->where('exam_id', $exam->id)
            ->where('status', 'in_progress')
            ->get()
            ->map(function ($attempt) {
                return [
                    'id' => $attempt->id,
                    'student_name' => $attempt->student->name ?? 'N/A',
                    'student_code' => $attempt->student->code ?? 'N/A',
                    'out_of_screen_time' => $attempt->out_of_screen_time,
                    'cheat_warnings' => $attempt->cheat_warnings,
                    'started_at' => $attempt->started_at->format('H:i:s'),
                ];
            });

        return response()->json(['attempts' => $attempts]);
    }
}
