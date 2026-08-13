<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    public function index(Request $request)
    {
        $query = SchoolClass::with('teacher')->withCount('students');

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $classes = $query->latest()->paginate(15);
        return view('admin.classes.index', compact('classes'));
    }

    public function create()
    {
        $teachers = User::where('role', 'teacher')->where('status', 'active')->get();
        return view('admin.classes.create', compact('teachers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:classes',
            'name' => 'required|string|max:255',
            'teacher_id' => 'required|exists:users,id',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        SchoolClass::create([
            'code' => strtoupper($request->code),
            'name' => $request->name,
            'teacher_id' => $request->teacher_id,
            'description' => $request->description,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.classes.index')->with('success', 'Thêm lớp học thành công!');
    }

    public function edit(SchoolClass $class)
    {
        $teachers = User::where('role', 'teacher')->where('status', 'active')->get();
        return view('admin.classes.edit', compact('class', 'teachers'));
    }

    public function update(Request $request, SchoolClass $class)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:classes,code,' . $class->id,
            'name' => 'required|string|max:255',
            'teacher_id' => 'required|exists:users,id',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $class->update([
            'code' => strtoupper($request->code),
            'name' => $request->name,
            'teacher_id' => $request->teacher_id,
            'description' => $request->description,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.classes.index')->with('success', 'Cập nhật lớp học thành công!');
    }
    public function show(SchoolClass $class)
    {
        $assignments = \Illuminate\Support\Facades\DB::table('class_subject_teacher')
            ->join('subjects', 'class_subject_teacher.subject_id', '=', 'subjects.id')
            ->join('users', 'class_subject_teacher.teacher_id', '=', 'users.id')
            ->where('class_subject_teacher.class_id', $class->id)
            ->select('class_subject_teacher.*', 'subjects.name as subject_name', 'users.name as teacher_name')
            ->get();
            
        $subjects = \App\Models\Subject::where('status', 'active')->orWhere('status', 1)->get(); // Adjusting status according to your subject logic
        $teachers = User::where('role', 'teacher')->where('status', 'active')->get();

        return view('admin.classes.show', compact('class', 'assignments', 'subjects', 'teachers'));
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
}
