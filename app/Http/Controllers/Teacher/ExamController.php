<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\Question;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ExamController extends Controller
{
    public function index(Request $request)
    {
        $subjects = auth()->user()->subjects()->where('status', true)->get();
        $classes = \App\Models\SchoolClass::where('teacher_id', auth()->id())->get();
        
        $query = Exam::with('subject')->where('created_by', auth()->id());
        
        if ($request->has('subject_id') && $request->subject_id != '') {
            $query->where('subject_id', $request->subject_id);
        }
        
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }
        
        if ($request->has('class_id') && $request->class_id != '') {
            $classId = $request->class_id;
            $query->whereHas('assignments', function($q) use ($classId) {
                $q->where('class_id', $classId);
            });
        }
        
        $exams = $query->latest()->paginate(15);
        
        return view('teacher.exams.index', compact('exams', 'subjects', 'classes'));
    }

    public function create()
    {
        $subjects = auth()->user()->subjects()->where('status', true)->get();
        return view('teacher.exams.create', compact('subjects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'title' => 'required|string|max:255',
            'duration_minutes' => 'required|integer|min:1',
            'max_attempts' => 'required|integer|min:1',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date|after_or_equal:start_at',
            'questions' => 'required|array|min:1', // Array of question IDs
            'points' => 'required|array', // Array of points per question
        ]);

        $exam = Exam::create([
            'subject_id' => $request->subject_id,
            'created_by' => auth()->id(),
            'code' => strtoupper(Str::random(8)),
            'title' => $request->title,
            'description' => $request->description,
            'duration_minutes' => $request->duration_minutes,
            'max_attempts' => $request->max_attempts,
            'start_at' => $request->start_at,
            'end_at' => $request->end_at,
            'total_questions' => count($request->questions),
            'status' => 'closed',
            'shuffle_questions' => $request->has('shuffle_questions'),
            'shuffle_answers' => $request->has('shuffle_answers'),
            'allow_review' => $request->has('allow_review'),
        ]);

        foreach ($request->questions as $index => $questionId) {
            $exam->questions()->attach($questionId, [
                'question_order' => $index + 1,
                'points' => $request->points[$questionId] ?? 1,
            ]);
        }

        return redirect()->route('teacher.exams.index')->with('success', 'Tạo đề thi thành công!');
    }

    public function edit(Exam $exam)
    {
        if ($exam->created_by !== auth()->id()) {
            abort(403, 'Bạn không có quyền sửa đề thi này.');
        }

        $subjects = auth()->user()->subjects()->where('status', true)->get();
        $exam->load(['questions' => function($q) {
            $q->orderBy('exam_questions.question_order');
        }]);
        
        return view('teacher.exams.edit', compact('exam', 'subjects'));
    }

    public function update(Request $request, Exam $exam)
    {
        if ($exam->created_by !== auth()->id()) {
            abort(403, 'Bạn không có quyền sửa đề thi này.');
        }

        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'title' => 'required|string|max:255',
            'duration_minutes' => 'required|integer|min:1',
            'max_attempts' => 'required|integer|min:1',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date|after_or_equal:start_at',
            'status' => 'required|in:published,closed',
            'questions' => 'required|array|min:1', // Array of question IDs
            'points' => 'required|array', // Array of points per question
        ]);

        $exam->update([
            'subject_id' => $request->subject_id,
            'title' => $request->title,
            'description' => $request->description,
            'duration_minutes' => $request->duration_minutes,
            'max_attempts' => $request->max_attempts,
            'start_at' => $request->start_at,
            'end_at' => $request->end_at,
            'status' => $request->status,
            'total_questions' => count($request->questions),
            'shuffle_questions' => $request->has('shuffle_questions'),
            'shuffle_answers' => $request->has('shuffle_answers'),
            'allow_review' => $request->has('allow_review'),
        ]);

        $syncData = [];
        foreach ($request->questions as $index => $questionId) {
            $syncData[$questionId] = [
                'question_order' => $index + 1,
                'points' => $request->points[$questionId] ?? 1,
            ];
        }
        $exam->questions()->sync($syncData);

        return redirect()->route('teacher.exams.index')->with('success', 'Cập nhật đề thi thành công!');
    }

    public function updateStatus(Request $request, Exam $exam)
    {
        if ($exam->created_by !== auth()->id()) {
            abort(403, 'Bạn không có quyền sửa đề thi này.');
        }

        $request->validate([
            'status' => 'required|in:published,closed'
        ]);

        $exam->update(['status' => $request->status]);

        return redirect()->route('teacher.exams.index')->with('success', 'Cập nhật trạng thái thành công!');
    }

    public function results(Exam $exam)
    {
        if ($exam->created_by !== auth()->id()) {
            abort(403, 'Bạn không có quyền xem kết quả đề thi này.');
        }

        $attempts = ExamAttempt::with('student')
            ->where('exam_id', $exam->id)
            ->where('status', 'submitted')
            ->get();
            
        $stats = [
            'total' => $attempts->count(),
            'average' => $attempts->avg('score_value') ?? 0,
            'max' => $attempts->max('score_value') ?? 0,
            'min' => $attempts->min('score_value') ?? 0,
            'passed' => $attempts->where('score_value', '>=', 5)->count(),
        ];
        
        $stats['pass_rate'] = $stats['total'] > 0 ? round(($stats['passed'] / $stats['total']) * 100) : 0;

        return view('teacher.exams.results', compact('exam', 'attempts', 'stats'));
    }

    public function monitor(Exam $exam)
    {
        if ($exam->created_by !== auth()->id()) {
            abort(403, 'Bạn không có quyền giám sát đề thi này.');
        }
        return view('teacher.exams.monitor', compact('exam'));
    }

    public function apiMonitor(Exam $exam)
    {
        if ($exam->created_by !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $attempts = ExamAttempt::with('student')
            ->where('exam_id', $exam->id)
            ->where('status', 'in_progress')
            ->get()
            ->map(function ($attempt) {
                return [
                    'id' => $attempt->id,
                    'student_name' => $attempt->student->name ?? 'N/A',
                    'student_code' => $attempt->student->code ?? 'N/A',
                    'out_of_screen_time' => $attempt->out_of_screen_time,
                    'cheat_warnings' => $attempt->cheat_warnings,
                    'started_at' => $attempt->started_at->format('H:i:s'),
                ];
            });

        return response()->json(['attempts' => $attempts]);
    }
}
