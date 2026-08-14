<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    public function index(Request $request)
    {
        $query = SchoolClass::with(['teacher', 'course'])->withCount(['students', 'assignedSubjects']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        $classes = $query->latest()->paginate(15)->withQueryString();
        $courses = Course::orderBy('created_at', 'desc')->get();

        return view('admin.classes.index', compact('classes', 'courses'));
    }

    public function create()
    {
        $teachers = User::where('role', 'teacher')->where('status', 'active')->get();
        $courses = Course::orderBy('created_at', 'desc')->get();
        return view('admin.classes.create', compact('teachers', 'courses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:classes',
            'name' => 'required|string|max:255',
            'teacher_id' => 'required|exists:users,id',
            'course_id' => 'nullable|exists:courses,id',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        SchoolClass::create([
            'code' => strtoupper($request->code),
            'name' => $request->name,
            'teacher_id' => $request->teacher_id,
            'course_id' => $request->course_id,
            'description' => $request->description,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.classes.index')->with('success', 'Thêm lớp học thành công!');
    }

    public function edit(SchoolClass $class)
    {
        $teachers = User::where('role', 'teacher')->where('status', 'active')->get();
        $courses = Course::orderBy('created_at', 'desc')->get();
        return view('admin.classes.edit', compact('class', 'teachers', 'courses'));
    }

    public function update(Request $request, SchoolClass $class)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:classes,code,' . $class->id,
            'name' => 'required|string|max:255',
            'teacher_id' => 'required|exists:users,id',
            'course_id' => 'nullable|exists:courses,id',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $class->update([
            'code' => strtoupper($request->code),
            'name' => $request->name,
            'teacher_id' => $request->teacher_id,
            'course_id' => $request->course_id,
            'description' => $request->description,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.classes.index')->with('success', 'Cập nhật lớp học thành công!');
    }

    public function show(SchoolClass $class)
    {
        $class->load(['teacher', 'course', 'students']);

        $assignments = \Illuminate\Support\Facades\DB::table('class_subject_teacher')
            ->join('subjects', 'class_subject_teacher.subject_id', '=', 'subjects.id')
            ->join('users', 'class_subject_teacher.teacher_id', '=', 'users.id')
            ->where('class_subject_teacher.class_id', $class->id)
            ->select('class_subject_teacher.*', 'subjects.name as subject_name', 'users.name as teacher_name')
            ->get();
            
        $subjects = \App\Models\Subject::where('status', 'active')->orWhere('status', 1)->get();
        $teachers = User::where('role', 'teacher')->where('status', 'active')->get();

        // Get available students not yet in this class
        $existingStudentIds = $class->students->pluck('id')->toArray();
        $availableStudents = User::where('role', 'student')
            ->whereNotIn('id', $existingStudentIds)
            ->orderBy('name')
            ->get();

        return view('admin.classes.show', compact('class', 'assignments', 'subjects', 'teachers', 'availableStudents'));
    }

    public function assign(Request $request, SchoolClass $class)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:users,id',
        ]);

        \Illuminate\Support\Facades\DB::table('class_subject_teacher')->updateOrInsert(
            ['class_id' => $class->id, 'subject_id' => $request->subject_id],
            ['teacher_id' => $request->teacher_id, 'created_at' => now(), 'updated_at' => now()]
        );

        return back()->with('success', 'Đã phân công giảng viên thành công.');
    }

    public function removeAssign(SchoolClass $class, $subjectId)
    {
        \Illuminate\Support\Facades\DB::table('class_subject_teacher')
            ->where('class_id', $class->id)
            ->where('subject_id', $subjectId)
            ->delete();

        return back()->with('success', 'Đã xóa phân công giảng dạy.');
    }

    public function addStudent(Request $request, SchoolClass $class)
    {
        $request->validate([
            'student_id' => 'required|exists:users,id',
        ]);

        $class->students()->syncWithoutDetaching([$request->student_id]);

        return back()->with('success', 'Đã thêm sinh viên vào lớp thành công.');
    }

    public function removeStudent(SchoolClass $class, $studentId)
    {
        $class->students()->detach($studentId);

        return back()->with('success', 'Đã xóa sinh viên khỏi lớp thành công.');
    }
}
