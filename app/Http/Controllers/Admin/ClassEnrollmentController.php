<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\EnrollStudentsRequest;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClassEnrollmentController extends Controller
{
    /**
     * Ghi danh sinh viên vào Lớp học phần có khóa race-condition và kiểm tra sĩ số
     */
    public function enroll(EnrollStudentsRequest $request, SchoolClass $class)
    {
        return DB::transaction(function () use ($request, $class) {
            // Lock dòng để đảm bảo tính đúng đắn khi nhiều request đồng thời
            $lockedClass = SchoolClass::where('id', $class->id)->lockForUpdate()->first();

            $currentCount = $lockedClass->students()->count();
            $newStudentIds = $request->student_ids;
            $newCount = count($newStudentIds);

            if ($currentCount + $newCount > $lockedClass->max_students) {
                return back()->with('error', "Không thể ghi danh: Tổng số sinh viên sau khi thêm (" . ($currentCount + $newCount) . ") sẽ vượt quá sĩ số tối đa của lớp ({$lockedClass->max_students}).");
            }

            $attachData = [];
            foreach ($newStudentIds as $id) {
                $attachData[$id] = [
                    'enrolled_at' => now(),
                    'status' => 'enrolled',
                ];
            }

            $lockedClass->students()->syncWithoutDetaching($attachData);

            return back()->with('success', "Đã ghi danh thành công {$newCount} sinh viên vào lớp học phần.");
        });
    }

    /**
     * Xóa sinh viên khỏi Lớp học phần
     */
    public function remove(SchoolClass $class, User $student)
    {
        $class->students()->detach($student->id);
        return back()->with('success', "Đã xóa sinh viên {$student->name} ({$student->code}) khỏi lớp.");
    }

    /**
     * Import sinh viên từ file text/csv/excel (nhận mã SV hoặc file)
     */
    public function importExcel(Request $request, SchoolClass $class)
    {
        $request->validate([
            'student_codes' => 'nullable|string',
            'file' => 'nullable|file|mimes:csv,txt,xlsx,xls',
        ]);

        $codes = [];

        // 1. Lấy từ textarea nếu có
        if ($request->filled('student_codes')) {
            $rawCodes = preg_split('/[\r\n,]+/', $request->student_codes);
            foreach ($rawCodes as $c) {
                $trimmed = trim($c);
                if (!empty($trimmed)) {
                    $codes[] = $trimmed;
                }
            }
        }

        // 2. Lấy từ file nếu upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $lines = file($file->getRealPath(), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                $parts = str_getcsv($line);
                if (!empty($parts[0])) {
                    $codes[] = trim($parts[0]);
                }
            }
        }

        $codes = array_unique(array_filter($codes));

        if (empty($codes)) {
            return back()->with('error', 'Không tìm thấy mã sinh viên nào để import.');
        }

        return DB::transaction(function () use ($codes, $class) {
            $lockedClass = SchoolClass::where('id', $class->id)->lockForUpdate()->first();

            // Tìm sinh viên theo mã code hoặc email
            $students = User::where('role', 'student')
                ->where(function ($q) use ($codes) {
                    $q->whereIn('code', $codes)->orWhereIn('email', $codes);
                })
                ->get();

            if ($students->isEmpty()) {
                return back()->with('error', 'Không tìm thấy sinh viên nào tương ứng với danh sách mã đã nhập.');
            }

            $existingIds = $lockedClass->students()->pluck('users.id')->toArray();
            $toEnroll = $students->whereNotIn('id', $existingIds);

            if ($toEnroll->isEmpty()) {
                return back()->with('info', 'Tất cả sinh viên trong danh sách đều đã có trong lớp.');
            }

            $currentCount = count($existingIds);
            if ($currentCount + $toEnroll->count() > $lockedClass->max_students) {
                return back()->with('error', "Không thể import: Thêm {$toEnroll->count()} sinh viên sẽ vượt quá sĩ số tối đa ({$lockedClass->max_students}). Hiện tại lớp đã có {$currentCount} sinh viên.");
            }

            $attachData = [];
            foreach ($toEnroll as $st) {
                $attachData[$st->id] = [
                    'enrolled_at' => now(),
                    'status' => 'enrolled',
                ];
            }

            $lockedClass->students()->syncWithoutDetaching($attachData);

            return back()->with('success', "Import thành công {$toEnroll->count()} sinh viên vào lớp học phần.");
        });
    }
}
