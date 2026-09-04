<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClassController extends Controller
{
    public function index()
    {
        $studentId = auth()->id();

        $classes = SchoolClass::with(['teacher', 'course'])
            ->withCount('students')
            ->whereHas('students', function ($q) use ($studentId) {
                $q->where('users.id', $studentId);
            })
            ->latest()
            ->paginate(15);

        return view('student.classes.index', compact('classes'));
    }

    public function show(SchoolClass $class)
    {
        $studentId = auth()->id();

        // Check if student belongs to this class
        $belongsToClass = $class->students()->where('users.id', $studentId)->exists();
        if (!$belongsToClass) {
            abort(403, 'Bạn không thuộc lớp học này.');
        }

        $class->load(['teacher', 'course']);

        // Get subjects & teachers assigned to this class
        $subjectTeachers = DB::table('class_subject_teacher')
            ->join('subjects', 'class_subject_teacher.subject_id', '=', 'subjects.id')
            ->join('users', 'class_subject_teacher.teacher_id', '=', 'users.id')
            ->where('class_subject_teacher.class_id', $class->id)
            ->select('subjects.id as subject_id', 'subjects.code as subject_code', 'subjects.name as subject_name', 'users.name as teacher_name', 'users.email as teacher_email')
            ->get();

        return view('student.classes.show', compact('class', 'subjectTeachers'));
    }
}
