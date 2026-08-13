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

    public function show(Request $request, SchoolClass $class)
    {
        if ($class->teacher_id !== auth()->id()) {
            abort(403, 'Bạn không có quyền xem lớp này.');
        }

        $query = $class->students();
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        $students = $query->orderBy('name')->get();
        
        return view('teacher.classes.show', compact('class', 'students'));
    }
}
