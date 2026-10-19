<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreFacultyRequest;
use App\Models\Faculty;
use Illuminate\Http\Request;

class FacultyController extends Controller
{
    public function index(Request $request)
    {
        $query = Faculty::withCount(['departments', 'subjects']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $faculties = $query->latest()->paginate(10)->withQueryString();
        return view('admin.faculties.index', compact('faculties'));
    }

    public function create()
    {
        return view('admin.faculties.create');
    }

    public function store(StoreFacultyRequest $request)
    {
        Faculty::create([
            'code' => strtoupper($request->code),
            'name' => $request->name,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.faculties.index')->with('success', 'Thêm mới Khoa thành công.');
    }

    public function edit(Faculty $faculty)
    {
        return view('admin.faculties.edit', compact('faculty'));
    }

    public function update(Request $request, Faculty $faculty)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:faculties,code,' . $faculty->id,
            'name' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ], [
            'code.required' => 'Vui lòng nhập mã Khoa.',
            'code.unique' => 'Mã Khoa đã tồn tại.',
            'name.required' => 'Vui lòng nhập tên Khoa.',
        ]);

        $faculty->update([
            'code' => strtoupper($request->code),
            'name' => $request->name,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.faculties.index')->with('success', 'Cập nhật thông tin Khoa thành công.');
    }

    public function destroy(Faculty $faculty)
    {
        // Chặn xóa nếu còn bộ môn trực thuộc
        if ($faculty->departments()->exists()) {
            return back()->with('error', 'Không thể xóa Khoa này vì vẫn còn Bộ môn trực thuộc. Vui lòng chuyển hoặc xóa các Bộ môn trước.');
        }

        $faculty->delete();
        return redirect()->route('admin.faculties.index')->with('success', 'Đã xóa Khoa thành công.');
    }
}
