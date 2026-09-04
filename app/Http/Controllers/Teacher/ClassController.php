<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClassController extends Controller
{
    public function index()
    {
        $teacherId = auth()->id();

        // Homeroom classes
        $homeroomClasses = SchoolClass::with(['course'])->withCount('students')
            ->where('teacher_id', $teacherId)
            ->latest()
            ->get();

        // Subject teaching classes
        $subjectClassIds = DB::table('class_subject_teacher')
            ->where('teacher_id', $teacherId)
            ->pluck('class_id')
            ->toArray();

        $teachingClasses = SchoolClass::with(['course'])->withCount('students')
            ->whereIn('id', $subjectClassIds)
            ->where('teacher_id', '!=', $teacherId)
            ->latest()
            ->get();

        return view('teacher.classes.index', compact('homeroomClasses', 'teachingClasses'));
    }

    public function show(Request $request, SchoolClass $class)
    {
        $teacherId = auth()->id();
        $isHomeroom = ($class->teacher_id === $teacherId);
        $isSubjectTeacher = DB::table('class_subject_teacher')
            ->where('class_id', $class->id)
            ->where('teacher_id', $teacherId)
            ->exists();

        if (!$isHomeroom && !$isSubjectTeacher) {
            abort(403, 'Bạn không có quyền xem lớp này.');
        }

        $class->load(['course', 'teacher']);

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

        // Get subjects assigned in this class
        $classSubjects = DB::table('class_subject_teacher')
            ->join('subjects', 'class_subject_teacher.subject_id', '=', 'subjects.id')
            ->join('users', 'class_subject_teacher.teacher_id', '=', 'users.id')
            ->where('class_subject_teacher.class_id', $class->id)
            ->select(
                'subjects.id as subject_id',
                'subjects.code as subject_code',
                'subjects.name as subject_name',
                'users.id as teacher_id',
                'users.name as teacher_name'
            )
            ->get();

        return view('teacher.classes.show', compact('class', 'students', 'isHomeroom', 'classSubjects'));
    }
}
