<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSemesterRequest;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SemesterController extends Controller
{
    public function index(Request $request)
    {
        $query = Semester::withCount('classes');

        if ($request->filled('academic_year')) {
            $query->where('academic_year', $request->academic_year);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $semesters = $query->orderBy('academic_year', 'desc')->orderBy('name', 'asc')->paginate(10)->withQueryString();
        $academicYears = Semester::distinct()->pluck('academic_year')->filter()->values();

        return view('admin.semesters.index', compact('semesters', 'academicYears'));
    }

    public function create()
    {
        return view('admin.semesters.create');
    }

    public function store(StoreSemesterRequest $request)
    {
        // Ràng buộc cặp duy nhất (name, academic_year)
        $exists = Semester::where('name', $request->name)
            ->where('academic_year', $request->academic_year)
            ->exists();

        if ($exists) {
            return back()->withErrors([
                'name' => "Học kỳ '{$request->name}' của năm học {$request->academic_year} đã tồn tại.",
            ])->withInput();
        }

        DB::transaction(function () use ($request) {
            $isCurrent = $request->boolean('is_current');
            if ($isCurrent) {
                Semester::where('is_current', true)->update(['is_current' => false]);
            }

            Semester::create([
                'code' => strtoupper($request->code),
                'name' => $request->name,
                'academic_year' => $request->academic_year,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'is_current' => $isCurrent,
            ]);
        });

        return redirect()->route('admin.semesters.index')->with('success', 'Thêm mới Học kỳ thành công.');
    }

    public function edit(Semester $semester)
    {
        return view('admin.semesters.edit', compact('semester'));
    }

    public function update(Request $request, Semester $semester)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:semesters,code,' . $semester->id,
            'name' => 'required|string|max:50',
            'academic_year' => ['required', 'regex:/^\d{4}-\d{4}$/'],
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_current' => 'nullable|boolean',
        ], [
            'code.required' => 'Vui lòng nhập mã học kỳ.',
            'code.unique' => 'Mã học kỳ đã tồn tại.',
            'academic_year.regex' => 'Năm học phải có dạng YYYY-YYYY.',
            'end_date.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu.',
        ]);

        $exists = Semester::where('name', $request->name)
            ->where('academic_year', $request->academic_year)
            ->where('id', '!=', $semester->id)
            ->exists();

        if ($exists) {
            return back()->withErrors([
                'name' => "Học kỳ '{$request->name}' của năm học {$request->academic_year} đã tồn tại.",
            ])->withInput();
        }

        DB::transaction(function () use ($request, $semester) {
            $isCurrent = $request->boolean('is_current');
            if ($isCurrent) {
                Semester::where('id', '!=', $semester->id)->where('is_current', true)->update(['is_current' => false]);
            }

            $semester->update([
                'code' => strtoupper($request->code),
                'name' => $request->name,
                'academic_year' => $request->academic_year,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'is_current' => $isCurrent,
            ]);
        });

        return redirect()->route('admin.semesters.index')->with('success', 'Cập nhật Học kỳ thành công.');
    }

    public function destroy(Semester $semester)
    {
        if ($semester->classes()->exists()) {
            return back()->with('error', 'Không thể xóa Học kỳ này vì đã có Lớp học phần được mở.');
        }

        $semester->delete();
        return redirect()->route('admin.semesters.index')->with('success', 'Đã xóa Học kỳ thành công.');
    }

    public function setCurrent(Semester $semester)
    {
        DB::transaction(function () use ($semester) {
            Semester::where('is_current', true)->update(['is_current' => false]);
            $semester->update(['is_current' => true]);
        });

        return back()->with('success', "Đã chuyển '{$semester->name} ({$semester->academic_year})' thành Học kỳ hiện tại.");
    }
}
