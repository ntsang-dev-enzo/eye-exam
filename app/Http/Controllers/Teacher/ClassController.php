<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    public function index()
    {
        $classes = SchoolClass::withCount('students')
            ->where('teacher_id', auth()->id())
            ->latest()
            ->paginate(15);
            
        return view('teacher.classes.index', compact('classes'));
    }

    public function show(SchoolClass $class)
    {
        if ($class->teacher_id !== auth()->id()) {
            abort(403, 'Bạn không có quyền xem lớp này.');
        }

        $students = $class->students()->orderBy('name')->get();
        
        return view('teacher.classes.show', compact('class', 'students'));
    }
}
