<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Exam;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Show the admin dashboard with real statistics.
     */
    public function index()
    {
        $totalUsers = User::count();
        $studentCount = User::where('role', 'student')->count();
        $teacherCount = User::where('role', 'teacher')->count();
        
        $totalSubjects = Subject::count();
        $activeSubjects = Subject::where('status', true)->count();
        
        $totalCourses = Course::count();
        $totalClasses = SchoolClass::count();
        
        $totalExams = Exam::count();
        $publishedExams = Exam::where('status', 'published')->count();

        // Recent exams with relations
        $recentExams = Exam::with(['subject', 'creator'])
            ->latest()
            ->take(5)
            ->get();

        // Recent courses with subjects
        $recentCourses = Course::with('subjects')
            ->withCount('subjects')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'studentCount',
            'teacherCount',
            'totalSubjects',
            'activeSubjects',
            'totalCourses',
            'totalClasses',
            'totalExams',
            'publishedExams',
            'recentExams',
            'recentCourses'
        ));
    }
}
