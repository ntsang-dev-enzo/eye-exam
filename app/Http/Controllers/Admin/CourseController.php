<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CourseController extends Controller
{
    /**
     * Display a listing of courses with search and filters.
     */
    public function index(Request $request)
    {
        $query = Course::with('subjects')->withCount('subjects');

        // Search by name
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter by semester
        if ($request->filled('semester')) {
            $query->where('semester', $request->semester);
        }

        // Filter by academic_year
        if ($request->filled('academic_year')) {
            $query->where('academic_year', $request->academic_year);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $courses = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        // Get list of distinct academic years for filter dropdown
        $academicYears = Course::distinct()->pluck('academic_year')->filter()->values();

        return view('admin.courses.index', compact('courses', 'academicYears'));
    }

    /**
     * Show the form for creating a new course.
     */
    public function create()
    {
        $subjects = Subject::where('status', true)->orWhereNull('status')->orderBy('name')->get();
        return view('admin.courses.create', compact('subjects'));
    }

    /**
     * Store a newly created course in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'semester' => 'required|string|in:Học kỳ 1,Học kỳ 2,Học kỳ 3',
            'academic_year' => ['required', 'regex:/^\d{4}-\d{4}$/'],
            'subjects' => 'required|array|min:1',
            'subjects.*' => 'exists:subjects,id',
            'description' => 'nullable|string',
            'status' => 'nullable|string|in:active,inactive',
        ], [
            'name.required' => 'Vui lòng nhập tên khóa học.',
            'semester.required' => 'Vui lòng chọn học kỳ.',
            'academic_year.required' => 'Vui lòng nhập năm học (Ví dụ: 2025-2026).',
            'academic_year.regex' => 'Năm học phải có định dạng YYYY-YYYY (Ví dụ: 2025-2026).',
            'subjects.required' => 'Vui lòng chọn ít nhất một môn học cho khóa học.',
            'subjects.min' => 'Vui lòng chọn ít nhất một môn học cho khóa học.',
        ]);

        // Duplicate Check: Prevent creating duplicate courses with the same (semester, academic_year)
        $exists = Course::where('semester', $request->semester)
            ->where('academic_year', $request->academic_year)
            ->exists();

        if ($exists) {
            return back()->withErrors([
                'semester' => 'Khóa học này đã tồn tại (Đã có khóa học cho ' . $request->semester . ' - ' . $request->academic_year . ').'
            ])->withInput();
        }

        $course = Course::create([
            'name' => $request->name,
            'semester' => $request->semester,
            'academic_year' => $request->academic_year,
            'description' => $request->description,
            'status' => $request->has('status') && $request->status === 'active' ? 'active' : ($request->has('is_active') ? 'active' : ($request->input('status', 'active'))),
        ]);

        $course->subjects()->sync($request->subjects);

        return redirect()->route('admin.khoa-hoc.index')->with('success', 'Thêm khóa học mới thành công!');
    }

    /**
     * Display the specified course with its subjects.
     */
    public function show(Course $khoaHoc)
    {
        $khoaHoc->load(['subjects.teachers']);
        return view('admin.courses.show', ['course' => $khoaHoc]);
    }

    /**
     * Show the form for editing the specified course.
     */
    public function edit(Course $khoaHoc)
    {
        $subjects = Subject::orderBy('name')->get();
        $selectedSubjectIds = $khoaHoc->subjects->pluck('id')->toArray();
        return view('admin.courses.edit', ['course' => $khoaHoc, 'subjects' => $subjects, 'selectedSubjectIds' => $selectedSubjectIds]);
    }

    /**
     * Update the specified course in storage.
     */
    public function update(Request $request, Course $khoaHoc)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'semester' => 'required|string|in:Học kỳ 1,Học kỳ 2,Học kỳ 3',
            'academic_year' => ['required', 'regex:/^\d{4}-\d{4}$/'],
            'subjects' => 'required|array|min:1',
            'subjects.*' => 'exists:subjects,id',
            'description' => 'nullable|string',
            'status' => 'nullable|string|in:active,inactive',
        ], [
            'name.required' => 'Vui lòng nhập tên khóa học.',
            'semester.required' => 'Vui lòng chọn học kỳ.',
            'academic_year.required' => 'Vui lòng nhập năm học.',
            'academic_year.regex' => 'Năm học phải có định dạng YYYY-YYYY (Ví dụ: 2025-2026).',
            'subjects.required' => 'Vui lòng chọn ít nhất một môn học cho khóa học.',
            'subjects.min' => 'Vui lòng chọn ít nhất một môn học cho khóa học.',
        ]);

        // Duplicate Check excluding current course ID
        $exists = Course::where('semester', $request->semester)
            ->where('academic_year', $request->academic_year)
            ->where('id', '!=', $khoaHoc->id)
            ->exists();

        if ($exists) {
            return back()->withErrors([
                'semester' => 'Khóa học trùng lặp (Đã có khóa học khác cho ' . $request->semester . ' - ' . $request->academic_year . ').'
            ])->withInput();
        }

        $khoaHoc->update([
            'name' => $request->name,
            'semester' => $request->semester,
            'academic_year' => $request->academic_year,
            'description' => $request->description,
            'status' => $request->input('status', 'active'),
        ]);

        $khoaHoc->subjects()->sync($request->subjects);

        return redirect()->route('admin.khoa-hoc.index')->with('success', 'Cập nhật khóa học thành công!');
    }

    /**
     * Remove the specified course from storage.
     */
    public function destroy(Course $khoaHoc)
    {
        $khoaHoc->subjects()->detach();
        $khoaHoc->delete();

        return redirect()->route('admin.khoa-hoc.index')->with('success', 'Xóa khóa học thành công!');
    }
}
