<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreClassRequest;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\SchoolClass;
use App\Models\Semester;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ClassController extends Controller
{
    public function index(Request $request)
    {
        $query = SchoolClass::with(['subject.department.faculty', 'semester', 'teacher'])
            ->withCount('students');

        // Lọc theo Học kỳ
        if ($request->filled('semester_id')) {
            $query->where('semester_id', $request->semester_id);
        } else {
            // Mặc định chọn học kỳ hiện tại nếu có
            $currentSemester = Semester::where('is_current', true)->first();
            if ($currentSemester && !$request->has('all_semesters')) {
                $query->where('semester_id', $currentSemester->id);
            }
        }

        // Lọc theo Môn học
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        // Lọc theo Khoa
        if ($request->filled('faculty_id')) {
            $query->whereHas('subject.department', function ($q) use ($request) {
                $q->where('faculty_id', $request->faculty_id);
            });
        }

        // Tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $classes = $query->latest()->paginate(15)->withQueryString();
        $semesters = Semester::orderBy('academic_year', 'desc')->orderBy('name', 'asc')->get();
        $faculties = Faculty::where('status', 'active')->orderBy('name')->get();
        $subjects = Subject::where('status', true)->orderBy('name')->get();

        return view('admin.classes.index', compact('classes', 'semesters', 'faculties', 'subjects'));
    }

    public function create()
    {
        $semesters = Semester::orderBy('academic_year', 'desc')->orderBy('name', 'asc')->get();
        $subjects = Subject::with('teachers')->where('status', true)->orderBy('name')->get();

        return view('admin.classes.create', compact('semesters', 'subjects'));
    }

    public function store(StoreClassRequest $request)
    {
        DB::transaction(function () use ($request) {
            $class = SchoolClass::create([
                'code' => strtoupper($request->code),
                'name' => $request->name,
                'subject_id' => $request->subject_id,
                'semester_id' => $request->semester_id,
                'teacher_id' => $request->teacher_id,
                'room' => $request->room,
                'max_students' => $request->max_students,
                'description' => $request->description,
                'status' => $request->status ?? 'open',
            ]);

            // Đồng bộ sang bảng class_subject_teacher để giữ tương thích các module khác
            DB::table('class_subject_teacher')->updateOrInsert(
                ['class_id' => $class->id, 'subject_id' => $class->subject_id],
                ['teacher_id' => $class->teacher_id, 'created_at' => now(), 'updated_at' => now()]
            );
        });

        return redirect()->route('admin.classes.index')->with('success', 'Mở Lớp học phần mới thành công!');
    }

    public function show(SchoolClass $class)
    {
        $class->load(['subject.department.faculty', 'semester', 'teacher', 'students']);

        // Danh sách sinh viên chưa thuộc lớp để ghi danh
        $existingStudentIds = $class->students->pluck('id')->toArray();
        $availableStudents = User::where('role', 'student')
            ->where('status', 'active')
            ->whereNotIn('id', $existingStudentIds)
            ->orderBy('name')
            ->get();

        return view('admin.classes.show', compact('class', 'availableStudents'));
    }

    public function edit(SchoolClass $class)
    {
        $semesters = Semester::orderBy('academic_year', 'desc')->orderBy('name', 'asc')->get();
        $subjects = Subject::with('teachers')->where('status', true)->orderBy('name')->get();

        // Lấy giảng viên đủ điều kiện dạy môn của lớp này
        $qualifiedTeachers = $class->subject ? $class->subject->teachers : collect();

        return view('admin.classes.edit', compact('class', 'semesters', 'subjects', 'qualifiedTeachers'));
    }

    public function update(Request $request, SchoolClass $class)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:classes,code,' . $class->id,
            'name' => 'required|string|max:255',
            'subject_id' => 'required|exists:subjects,id',
            'semester_id' => 'required|exists:semesters,id',
            'teacher_id' => [
                'required',
                'exists:users,id',
                Rule::exists('subject_teacher', 'teacher_id')->where(function ($query) use ($request) {
                    $query->where('subject_id', $request->subject_id);
                }),
            ],
            'room' => 'nullable|string|max:100',
            'max_students' => 'required|integer|min:1|max:300',
            'description' => 'nullable|string',
            'status' => 'required|in:open,ongoing,closed',
        ], [
            'teacher_id.exists' => 'Giảng viên được chọn chưa đủ điều kiện / chưa được phân công môn học này.',
        ]);

        DB::transaction(function () use ($request, $class) {
            $class->update([
                'code' => strtoupper($request->code),
                'name' => $request->name,
                'subject_id' => $request->subject_id,
                'semester_id' => $request->semester_id,
                'teacher_id' => $request->teacher_id,
                'room' => $request->room,
                'max_students' => $request->max_students,
                'description' => $request->description,
                'status' => $request->status,
            ]);

            DB::table('class_subject_teacher')->updateOrInsert(
                ['class_id' => $class->id, 'subject_id' => $class->subject_id],
                ['teacher_id' => $class->teacher_id, 'updated_at' => now()]
            );
        });

        return redirect()->route('admin.classes.index')->with('success', 'Cập nhật Lớp học phần thành công!');
    }

    public function destroy(SchoolClass $class)
    {
        if ($class->students()->exists()) {
            return back()->with('error', 'Không thể xóa Lớp học phần vì đã có sinh viên ghi danh.');
        }

        DB::transaction(function () use ($class) {
            DB::table('class_subject_teacher')->where('class_id', $class->id)->delete();
            $class->delete();
        });

        return redirect()->route('admin.classes.index')->with('success', 'Đã xóa Lớp học phần.');
    }
}
