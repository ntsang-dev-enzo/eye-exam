<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
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

        // Count attendance stats
        $totalSessions = Attendance::where('class_id', $class->id)
            ->distinct('attendance_date')
            ->count('attendance_date');

        return view('teacher.classes.show', compact('class', 'students', 'isHomeroom', 'totalSessions'));
    }

    public function attendance(Request $request, SchoolClass $class)
    {
        $teacherId = auth()->id();
        $isHomeroom = ($class->teacher_id === $teacherId);
        $isSubjectTeacher = DB::table('class_subject_teacher')
            ->where('class_id', $class->id)
            ->where('teacher_id', $teacherId)
            ->exists();

        if (!$isHomeroom && !$isSubjectTeacher) {
            abort(403, 'Bạn không có quyền điểm danh lớp này.');
        }

        $date = $request->query('date', now()->format('Y-m-d'));
        $students = $class->students()->orderBy('name')->get();

        // Fetch existing attendance for this date
        $existingRecords = Attendance::where('class_id', $class->id)
            ->whereDate('attendance_date', $date)
            ->get()
            ->keyBy('student_id');

        return view('teacher.classes.attendance', compact('class', 'students', 'date', 'existingRecords'));
    }

    public function storeAttendance(Request $request, SchoolClass $class)
    {
        $teacherId = auth()->id();
        $isHomeroom = ($class->teacher_id === $teacherId);
        $isSubjectTeacher = DB::table('class_subject_teacher')
            ->where('class_id', $class->id)
            ->where('teacher_id', $teacherId)
            ->exists();

        if (!$isHomeroom && !$isSubjectTeacher) {
            abort(403, 'Bạn không có quyền điểm danh lớp này.');
        }

        $request->validate([
            'date' => 'required|date',
            'attendances' => 'required|array',
            'notes' => 'nullable|array',
        ]);

        $date = $request->date;
        $attendances = $request->attendances;
        $notes = $request->notes ?? [];

        foreach ($attendances as $studentId => $status) {
            Attendance::updateOrCreate(
                [
                    'class_id' => $class->id,
                    'student_id' => $studentId,
                    'attendance_date' => $date,
                ],
                [
                    'teacher_id' => $teacherId,
                    'status' => in_array($status, ['present', 'absent', 'late', 'excused']) ? $status : 'present',
                    'note' => $notes[$studentId] ?? null,
                ]
            );
        }

        return redirect()->route('teacher.classes.show', $class)->with('success', 'Đã lưu kết quả điểm danh ngày ' . date('d/m/Y', strtotime($date)) . ' thành công!');
    }

    public function attendanceHistory(SchoolClass $class)
    {
        $teacherId = auth()->id();
        $isHomeroom = ($class->teacher_id === $teacherId);
        $isSubjectTeacher = DB::table('class_subject_teacher')
            ->where('class_id', $class->id)
            ->where('teacher_id', $teacherId)
            ->exists();

        if (!$isHomeroom && !$isSubjectTeacher) {
            abort(403, 'Bạn không có quyền xem lịch sử điểm danh lớp này.');
        }

        $history = Attendance::where('class_id', $class->id)
            ->select('attendance_date', 
                DB::raw('count(case when status = "present" then 1 end) as present_count'),
                DB::raw('count(case when status = "absent" then 1 end) as absent_count'),
                DB::raw('count(case when status = "late" then 1 end) as late_count'),
                DB::raw('count(case when status = "excused" then 1 end) as excused_count')
            )
            ->groupBy('attendance_date')
            ->orderBy('attendance_date', 'desc')
            ->get();

        return view('teacher.classes.attendance-history', compact('class', 'history'));
    }
}
