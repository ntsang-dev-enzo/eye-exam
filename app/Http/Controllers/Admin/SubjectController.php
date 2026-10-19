<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSubjectRequest;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Subject::with(['department.faculty', 'teachers'])->withCount('teachers');

        if ($request->filled('faculty_id')) {
            $query->whereHas('department', function ($q) use ($request) {
                $q->where('faculty_id', $request->faculty_id);
            });
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $subjects = $query->latest()->paginate(10)->withQueryString();
        $faculties = Faculty::where('status', 'active')->orderBy('name')->get();
        $departments = collect();

        if ($request->filled('faculty_id')) {
            $departments = Department::where('faculty_id', $request->faculty_id)->where('status', 'active')->get();
        }

        return view('admin.subjects.index', compact('subjects', 'faculties', 'departments'));
    }

    public function create()
    {
        $faculties = Faculty::with(['departments' => function ($q) {
            $q->where('status', 'active');
        }])->where('status', 'active')->get();

        $teachers = User::where('role', 'teacher')->where('status', 'active')->orderBy('name')->get();

        return view('admin.subjects.create', compact('faculties', 'teachers'));
    }

    public function store(StoreSubjectRequest $request)
    {
        DB::transaction(function () use ($request) {
            $subject = Subject::create([
                'department_id' => $request->department_id,
                'code' => strtoupper($request->code),
                'name' => $request->name,
                'credits' => $request->credits,
                'description' => $request->description,
                'status' => $request->boolean('status', true),
            ]);

            if ($request->filled('teachers')) {
                // Sync vào cả 2 bảng subject_teacher (chuẩn mới) và subject_user (để tương thích ngược)
                $subject->teachers()->sync($request->teachers);
                DB::table('subject_user')->where('subject_id', $subject->id)->delete();
                $rows = array_map(fn($tId) => [
                    'subject_id' => $subject->id,
                    'user_id' => $tId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ], $request->teachers);
                if (!empty($rows)) {
                    DB::table('subject_user')->insert($rows);
                }
            }
        });

        return redirect()->route('admin.subjects.index')->with('success', 'Thêm mới Môn học thành công.');
    }

    public function edit(Subject $subject)
    {
        $faculties = Faculty::with(['departments' => function ($q) {
            $q->where('status', 'active');
        }])->where('status', 'active')->get();

        $teachers = User::where('role', 'teacher')->where('status', 'active')->orderBy('name')->get();
        $selectedTeacherIds = $subject->teachers->pluck('id')->toArray();

        return view('admin.subjects.edit', compact('subject', 'faculties', 'teachers', 'selectedTeacherIds'));
    }

    public function update(Request $request, Subject $subject)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'code' => 'required|string|max:50|unique:subjects,code,' . $subject->id,
            'name' => 'required|string|max:255',
            'credits' => 'required|integer|min:1|max:15',
            'description' => 'nullable|string',
            'status' => 'nullable|boolean',
            'teachers' => 'nullable|array',
            'teachers.*' => 'exists:users,id',
        ]);

        DB::transaction(function () use ($request, $subject) {
            $subject->update([
                'department_id' => $request->department_id,
                'code' => strtoupper($request->code),
                'name' => $request->name,
                'credits' => $request->credits,
                'description' => $request->description,
                'status' => $request->boolean('status', true),
            ]);

            $teacherIds = $request->input('teachers', []);
            $subject->teachers()->sync($teacherIds);

            // Đồng bộ subject_user để tương thích ngược
            DB::table('subject_user')->where('subject_id', $subject->id)->delete();
            $rows = array_map(fn($tId) => [
                'subject_id' => $subject->id,
                'user_id' => $tId,
                'created_at' => now(),
                'updated_at' => now(),
            ], $teacherIds);
            if (!empty($rows)) {
                DB::table('subject_user')->insert($rows);
            }
        });

        return redirect()->route('admin.subjects.index')->with('success', 'Cập nhật Môn học thành công.');
    }

    public function destroy(Subject $subject)
    {
        if ($subject->classes()->exists()) {
            return back()->with('error', 'Không thể xóa Môn học vì đã có Lớp học phần đang mở cho môn này.');
        }

        DB::transaction(function () use ($subject) {
            $subject->teachers()->detach();
            DB::table('subject_user')->where('subject_id', $subject->id)->delete();
            $subject->delete();
        });

        return redirect()->route('admin.subjects.index')->with('success', 'Đã xóa Môn học thành công.');
    }

    /**
     * API Lấy danh sách giảng viên đủ điều kiện dạy môn học này
     */
    public function getQualifiedTeachers(Subject $subject)
    {
        $teachers = $subject->teachers()
            ->where('users.status', 'active')
            ->select('users.id', 'users.name', 'users.code', 'users.email')
            ->get();

        return response()->json($teachers);
    }
}
