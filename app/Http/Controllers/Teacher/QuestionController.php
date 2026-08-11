<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Subject;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function index(Request $request)
    {
        $subjects = auth()->user()->subjects()->where('status', true)->get();
        
        $query = Question::with('subject', 'creator')->where('created_by', auth()->id());
        
        if ($request->has('subject_id') && $request->subject_id != '') {
            $query->where('subject_id', $request->subject_id);
        }
        
        $questions = $query->latest()->paginate(15);
        
        return view('teacher.questions.index', compact('questions', 'subjects'));
    }

    public function create()
    {
        $subjects = auth()->user()->subjects()->where('status', true)->get();
        return view('teacher.questions.create', compact('subjects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'content' => 'required|string',
            'type' => 'required|in:multiple_choice,essay',
            'difficulty' => 'required|in:easy,medium,hard',
            'answers' => 'required_if:type,multiple_choice|array|min:2',
            'correct_answer' => 'required_if:type,multiple_choice',
        ]);

        $question = Question::create([
            'subject_id' => $request->subject_id,
            'created_by' => auth()->id(),
            'content' => $request->content,
            'type' => $request->type,
            'difficulty' => $request->difficulty,
            'explanation' => $request->explanation,
            'status' => true,
        ]);

        if ($request->type === 'multiple_choice' && $request->has('answers')) {
            $labels = ['A', 'B', 'C', 'D', 'E', 'F'];
            foreach ($request->answers as $index => $answerContent) {
                if (!empty($answerContent)) {
                    $question->answers()->create([
                        'label' => $labels[$index] ?? chr(65 + $index),
                        'content' => $answerContent,
                        'is_correct' => ($request->correct_answer == $index)
                    ]);
                }
            }
        }

        return redirect()->route('teacher.questions.index')->with('success', 'Câu hỏi đã được thêm vào ngân hàng.');
    }

    public function apiIndex(Request $request)
    {
        $query = Question::with('subject')->where('created_by', auth()->id());
        
        if ($request->has('subject_id') && $request->subject_id != '') {
            $query->where('subject_id', $request->subject_id);
        }
        
        $questions = $query->latest()->get();
        return response()->json($questions);
    }

    public function apiStore(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'content' => 'required|string',
            'type' => 'required|in:multiple_choice,essay',
            'difficulty' => 'required|in:easy,medium,hard',
            'answers' => 'required_if:type,multiple_choice|array|min:2',
            'correct_answer' => 'required_if:type,multiple_choice',
        ]);

        $question = Question::create([
            'subject_id' => $request->subject_id,
            'created_by' => auth()->id(),
            'content' => $request->content,
            'type' => $request->type,
            'difficulty' => $request->difficulty,
            'explanation' => $request->explanation,
            'status' => true,
        ]);

        if ($request->type === 'multiple_choice' && $request->has('answers')) {
            $labels = ['A', 'B', 'C', 'D', 'E', 'F'];
            foreach ($request->answers as $index => $answerContent) {
                if (!empty($answerContent)) {
                    $question->answers()->create([
                        'label' => $labels[$index] ?? chr(65 + $index),
                        'content' => $answerContent,
                        'is_correct' => ($request->correct_answer == $index)
                    ]);
                }
            }
        }

        return response()->json([
            'success' => true,
            'question' => $question->load('subject')
        ]);
    }
}
