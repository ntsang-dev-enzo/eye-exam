<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * Display courses assigned/permitted to the teacher (Read-Only).
     */
    public function index(Request $request)
    {
        $teacher = auth()->user();
        $teacherSubjectIds = $teacher->subjects()->pluck('subjects.id')->toArray();

        $query = Course::with(['subjects' => function($q) use ($teacherSubjectIds) {
            if (!empty($teacherSubjectIds)) {
                // Return all subjects in course, marking teacher's ones
                $q->with('teachers');
            }
        }])->where('status', 'active');

        // Optional search
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('semester')) {
            $query->where('semester', $request->semester);
        }

        $courses = $query->orderBy('created_at', 'desc')->get();

        return view('teacher.courses.index', compact('courses', 'teacherSubjectIds'));
    }
}
