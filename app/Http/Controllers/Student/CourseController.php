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

        return view('student.courses.index', compact('courses'));
    }
}
