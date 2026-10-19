<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreDepartmentRequest;
use App\Models\Department;
use App\Models\Faculty;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Department::with('faculty')->withCount('subjects');

        if ($request->filled('faculty_id')) {
            $query->where('faculty_id', $request->faculty_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $departments = $query->latest()->paginate(10)->withQueryString();
        $faculties = Faculty::where('status', 'active')->orderBy('name')->get();

        return view('admin.departments.index', compact('departments', 'faculties'));
    }

    public function create()
    {
        $faculties = Faculty::where('status', 'active')->orderBy('name')->get();
        return view('admin.departments.create', compact('faculties'));
    }

    public function store(StoreDepartmentRequest $request)
    {
        Department::create([
            'faculty_id' => $request->faculty_id,
            'code' => strtoupper($request->code),
            'name' => $request->name,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.departments.index')->with('success', 'Thêm mới Bộ môn thành công.');
    }

    public function edit(Department $department)
    {
        $faculties = Faculty::where('status', 'active')->orderBy('name')->get();
        return view('admin.departments.edit', compact('department', 'faculties'));
    }

    public function update(Request $request, Department $department)
    {
        $request->validate([
            'faculty_id' => 'required|exists:faculties,id',
            'code' => 'required|string|max:50|unique:departments,code,' . $department->id,
            'name' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ], [
            'faculty_id.required' => 'Vui lòng chọn Khoa.',
            'code.required' => 'Vui lòng nhập mã Bộ môn.',
            'code.unique' => 'Mã Bộ môn đã tồn tại.',
            'name.required' => 'Vui lòng nhập tên Bộ môn.',
        ]);

        $department->update([
            'faculty_id' => $request->faculty_id,
            'code' => strtoupper($request->code),
            'name' => $request->name,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.departments.index')->with('success', 'Cập nhật Bộ môn thành công.');
    }

    public function destroy(Department $department)
    {
        if ($department->subjects()->exists()) {
            return back()->with('error', 'Không thể xóa Bộ môn này vì vẫn còn Môn học trực thuộc. Vui lòng chuyển hoặc xóa Môn học trước.');
        }

        $department->delete();
        return redirect()->route('admin.departments.index')->with('success', 'Đã xóa Bộ môn thành công.');
    }

    /**
     * API dynamic dropdown: lấy danh sách bộ môn theo khoa
     */
    public function getByFaculty(Faculty $faculty)
    {
        $departments = $faculty->departments()->where('status', 'active')->select('id', 'code', 'name')->get();
        return response()->json($departments);
    }
}
