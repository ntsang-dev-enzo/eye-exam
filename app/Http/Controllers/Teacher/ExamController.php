<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Question;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ExamController extends Controller
{
    public function index(Request $request)
    {
        $subjects = auth()->user()->subjects()->where('status', true)->get();
        
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
        
        $exams = $query->latest()->paginate(15);
        
        return view('teacher.exams.index', compact('exams', 'subjects'));
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
            'start_at' => $request->start_at,
            'end_at' => $request->end_at,
            'total_questions' => count($request->questions),
            'status' => 'draft',
            'shuffle_questions' => $request->has('shuffle_questions'),
            'shuffle_answers' => $request->has('shuffle_answers'),
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
        if ($exam->created_by !== auth()->id() || $exam->status !== 'draft') {
            abort(403, 'Bạn không có quyền sửa đề thi này hoặc đề thi không còn ở trạng thái nháp.');
        }

        $subjects = auth()->user()->subjects()->where('status', true)->get();
        $exam->load(['questions' => function($q) {
            $q->orderBy('exam_questions.question_order');
        }]);
        
        return view('teacher.exams.edit', compact('exam', 'subjects'));
    }

    public function update(Request $request, Exam $exam)
    {
        if ($exam->created_by !== auth()->id() || $exam->status !== 'draft') {
            abort(403, 'Bạn không có quyền sửa đề thi này hoặc đề thi không còn ở trạng thái nháp.');
        }

        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'title' => 'required|string|max:255',
            'duration_minutes' => 'required|integer|min:1',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date|after_or_equal:start_at',
            'questions' => 'required|array|min:1', // Array of question IDs
            'points' => 'required|array', // Array of points per question
        ]);

        $exam->update([
            'subject_id' => $request->subject_id,
            'title' => $request->title,
            'description' => $request->description,
            'duration_minutes' => $request->duration_minutes,
            'start_at' => $request->start_at,
            'end_at' => $request->end_at,
            'total_questions' => count($request->questions),
            'shuffle_questions' => $request->has('shuffle_questions'),
            'shuffle_answers' => $request->has('shuffle_answers'),
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
            'status' => 'required|in:draft,published,ongoing,closed'
        ]);

        $exam->update(['status' => $request->status]);

        return redirect()->route('teacher.exams.index')->with('success', 'Cập nhật trạng thái thành công!');
    }
}
