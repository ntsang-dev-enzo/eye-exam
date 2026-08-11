<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Show the student dashboard.
     */
    public function index()
    {
        $attempts = \App\Models\ExamAttempt::with(['exam.subject'])
            ->where('student_id', auth()->id())
            ->latest('updated_at')
            ->get();
            
        return view('student.dashboard', compact('attempts'));
    }
}
