<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\SchoolClass;
use App\Models\ExamAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssignmentController extends Controller
{
    public function create(Request $request)
    {
        $classes = SchoolClass::where('teacher_id', auth()->id())->orderBy('name')->get();
        
        $exams = Exam::where('created_by', auth()->id())
            ->whereIn('status', ['published', 'closed'])
            ->latest('id')
            ->get();
            
        // Selected classes (support multiple or single)
        $selectedClassIds = (array) $request->query('class_ids', $request->query('class_id') ? [$request->query('class_id')] : []);
        $selectedClassIds = array_filter(array_map('intval', $selectedClassIds));

        $preselectedExamId = $request->query('exam_id') ? intval($request->query('exam_id')) : null;

        $students = collect();
        if (!empty($selectedClassIds)) {
            $selectedClasses = SchoolClass::where('teacher_id', auth()->id())
                ->whereIn('id', $selectedClassIds)
                ->with(['students' => function($q) {
                    $q->orderBy('name');
                }])
                ->get();

            foreach ($selectedClasses as $c) {
                foreach ($c->students as $student) {
                    $student->class_name = $c->name;
                    $student->class_id = $c->id;
                    $students->push($student);
                }
            }

            // De-duplicate if student is in multiple selected classes
            $students = $students->unique('id')->values();
        }

        return view('teacher.exams.assign', compact('classes', 'exams', 'selectedClassIds', 'students', 'preselectedExamId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'class_ids' => 'required|array|min:1',
            'class_ids.*' => 'exists:classes,id',
            'exams' => 'required|array|min:1',
            'exams.*' => 'exists:exams,id',
            'students' => 'required|array|min:1',
            'students.*' => 'exists:users,id',
            'assign_type' => 'required|in:all,sole,sole_1_2,sole_1_2_3,sole_1_2_3_4'
        ]);

        $classIds = array_map('intval', $request->class_ids);
        $examIds = array_values(array_map('intval', $request->exams));
        $studentIds = array_values(array_map('intval', $request->students));
        $assignType = $request->assign_type;
        $examCount = count($examIds);

        // Map students to their class
        $studentClassMap = DB::table('class_students')
            ->whereIn('class_id', $classIds)
            ->whereIn('student_id', $studentIds)
            ->pluck('class_id', 'student_id')
            ->toArray();

        $defaultClassId = $classIds[0];

        if (str_starts_with($assignType, 'sole')) {
            // Determine sole divisor
            $divisor = match($assignType) {
                'sole_1_2' => min(2, $examCount),
                'sole_1_2_3' => min(3, $examCount),
                'sole_1_2_3_4' => min(4, $examCount),
                default => $examCount,
            };

            foreach ($studentIds as $index => $studentId) {
                $examIndex = $index % $divisor;
                $assignedExamId = $examIds[$examIndex];
                $targetClassId = $studentClassMap[$studentId] ?? $defaultClassId;

                ExamAssignment::firstOrCreate([
                    'exam_id' => $assignedExamId,
                    'class_id' => $targetClassId,
                    'student_id' => $studentId,
                ], [
                    'assigned_by' => auth()->id(),
                    'assigned_at' => now(),
                ]);
            }
        } else {
            // Assign all selected exams to all selected students
            foreach ($studentIds as $studentId) {
                $targetClassId = $studentClassMap[$studentId] ?? $defaultClassId;
                foreach ($examIds as $examId) {
                    ExamAssignment::firstOrCreate([
                        'exam_id' => $examId,
                        'class_id' => $targetClassId,
                        'student_id' => $studentId,
                    ], [
                        'assigned_by' => auth()->id(),
                        'assigned_at' => now(),
                    ]);
                }
            }
        }

        // Auto publish exams if they were closed
        Exam::whereIn('id', $examIds)
            ->where('status', 'closed')
            ->update(['status' => 'published']);

        return redirect()->route('teacher.exams.index')->with('success', 'Giao đề thi cho lớp thành công!');
    }
}
