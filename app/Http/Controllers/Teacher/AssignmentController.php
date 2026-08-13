<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\SchoolClass;
use App\Models\ExamAssignment;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    public function create(Request $request)
    {
        $classes = SchoolClass::where('teacher_id', auth()->id())->get();
        
        $exams = Exam::where('created_by', auth()->id())
            ->whereIn('status', ['published', 'closed'])
            ->latest()
            ->get();
            
        $selectedClassId = $request->query('class_id');
        $students = [];
        
        if ($selectedClassId) {
            $class = SchoolClass::where('teacher_id', auth()->id())->findOrFail($selectedClassId);
            $students = $class->students()->orderBy('name')->get();
        }

        return view('teacher.exams.assign', compact('classes', 'exams', 'selectedClassId', 'students'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'exams' => 'required|array|min:1',
            'exams.*' => 'exists:exams,id',
            'students' => 'required|array|min:1',
            'students.*' => 'exists:users,id',
            'assign_type' => 'required|in:all,sole'
        ]);

        $class = SchoolClass::where('teacher_id', auth()->id())->findOrFail($request->class_id);
        
        $examIds = $request->exams;
        $studentIds = $request->students;
        $assignType = $request->assign_type;
        
        if ($assignType === 'sole') {
            // Distribute exams alternately
            $examCount = count($examIds);
            foreach ($studentIds as $index => $studentId) {
                $examIndex = $index % $examCount;
                $assignedExamId = $examIds[$examIndex];
                
                // Check if already assigned to avoid duplicates (optional, but good practice)
                ExamAssignment::firstOrCreate([
                    'exam_id' => $assignedExamId,
                    'class_id' => $class->id,
                    'student_id' => $studentId,
                ], [
                    'assigned_by' => auth()->id(),
                    'assigned_at' => now(),
                ]);
            }
        } else {
            // Assign all selected exams to all selected students
            foreach ($studentIds as $studentId) {
                foreach ($examIds as $examId) {
                    ExamAssignment::firstOrCreate([
                        'exam_id' => $examId,
                        'class_id' => $class->id,
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

        return redirect()->route('teacher.exams.index')->with('success', 'Giao đề thi thành công!');
    }
}
