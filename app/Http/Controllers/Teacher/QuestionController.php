<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Question;
use App\Models\Subject;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function index(Request $request)
    {
        $subjects = auth()->user()->subjects()->where('status', true)->get();
        $categories = Category::where('created_by', auth()->id())
            ->orWhereNull('created_by')
            ->get();
        
        $query = Question::with(['subject', 'category', 'creator'])->where('created_by', auth()->id());
        
        if ($request->has('subject_id') && $request->subject_id != '') {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->has('category_id') && $request->category_id != '') {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('type') && $request->type != '') {
            $query->where('type', $request->type);
        }

        if ($request->has('difficulty') && $request->difficulty != '') {
            $query->where('difficulty', $request->difficulty);
        }

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('content', 'like', "%{$search}%");
        }
        
        $questions = $query->latest()->paginate(15);
        
        return view('teacher.questions.index', compact('questions', 'subjects', 'categories'));
    }

    public function create()
    {
        $subjects = auth()->user()->subjects()->where('status', true)->get();
        $categories = Category::where('created_by', auth()->id())
            ->orWhereNull('created_by')
            ->get();
        return view('teacher.questions.create', compact('subjects', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'category_id' => 'nullable|exists:categories,id',
            'new_category_name' => 'nullable|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:multiple_choice,essay',
            'difficulty' => 'required|in:easy,medium,hard',
            'answers' => 'required_if:type,multiple_choice|array|min:2',
            'correct_answer' => 'required_if:type,multiple_choice',
        ]);

        $categoryId = $request->category_id;
        if (!empty($request->new_category_name)) {
            $cat = Category::firstOrCreate([
                'name' => trim($request->new_category_name),
                'subject_id' => $request->subject_id,
            ], [
                'created_by' => auth()->id(),
            ]);
            $categoryId = $cat->id;
        }

        $question = Question::create([
            'subject_id' => $request->subject_id,
            'category_id' => $categoryId,
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

    public function edit(Question $question)
    {
        if ($question->created_by !== auth()->id()) {
            abort(403, 'Bạn không có quyền sửa câu hỏi này.');
        }

        $subjects = auth()->user()->subjects()->where('status', true)->get();
        $categories = Category::where('created_by', auth()->id())
            ->orWhereNull('created_by')
            ->get();
        $question->load('answers');
        
        return view('teacher.questions.edit', compact('question', 'subjects', 'categories'));
    }

    public function update(Request $request, Question $question)
    {
        if ($question->created_by !== auth()->id()) {
            abort(403, 'Bạn không có quyền sửa câu hỏi này.');
        }

        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'category_id' => 'nullable|exists:categories,id',
            'new_category_name' => 'nullable|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:multiple_choice,essay',
            'difficulty' => 'required|in:easy,medium,hard',
            'answers' => 'required_if:type,multiple_choice|array|min:2',
            'correct_answer' => 'required_if:type,multiple_choice',
        ]);

        $categoryId = $request->category_id;
        if (!empty($request->new_category_name)) {
            $cat = Category::firstOrCreate([
                'name' => trim($request->new_category_name),
                'subject_id' => $request->subject_id,
            ], [
                'created_by' => auth()->id(),
            ]);
            $categoryId = $cat->id;
        }

        $question->update([
            'subject_id' => $request->subject_id,
            'category_id' => $categoryId,
            'content' => $request->content,
            'type' => $request->type,
            'difficulty' => $request->difficulty,
            'explanation' => $request->explanation,
        ]);

        if ($request->type === 'multiple_choice' && $request->has('answers')) {
            $question->answers()->delete();
            
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
        } elseif ($request->type === 'essay') {
             $question->answers()->delete();
        }

        return redirect()->route('teacher.questions.index')->with('success', 'Cập nhật câu hỏi thành công.');
    }

    public function destroy(Question $question)
    {
        if ($question->created_by !== auth()->id()) {
            abort(403, 'Bạn không có quyền xóa câu hỏi này.');
        }

        $question->delete();

        return redirect()->route('teacher.questions.index')->with('success', 'Đã xóa câu hỏi thành công.');
    }

    public function apiIndex(Request $request)
    {
        $query = Question::with(['subject', 'category'])->where('created_by', auth()->id());
        
        if ($request->has('subject_id') && $request->subject_id != '') {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->has('category_id') && $request->category_id != '') {
            $query->where('category_id', $request->category_id);
        }
        
        $questions = $query->latest()->get();
        return response()->json($questions);
    }

    public function apiStore(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'category_id' => 'nullable|exists:categories,id',
            'new_category_name' => 'nullable|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:multiple_choice,essay',
            'difficulty' => 'required|in:easy,medium,hard',
            'answers' => 'required_if:type,multiple_choice|array|min:2',
            'correct_answer' => 'required_if:type,multiple_choice',
        ]);

        $categoryId = $request->category_id;
        if (!empty($request->new_category_name)) {
            $cat = Category::firstOrCreate([
                'name' => trim($request->new_category_name),
                'subject_id' => $request->subject_id,
            ], [
                'created_by' => auth()->id(),
            ]);
            $categoryId = $cat->id;
        }

        $question = Question::create([
            'subject_id' => $request->subject_id,
            'category_id' => $categoryId,
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
            'question' => $question->load(['subject', 'category'])
        ]);
    }

    public function apiCategories(Request $request)
    {
        $query = Category::query();
        if ($request->has('subject_id') && $request->subject_id != '') {
            $query->where(function($q) use ($request) {
                $q->where('subject_id', $request->subject_id)
                  ->orWhereNull('subject_id');
            });
        }
        $query->where(function($q) {
            $q->where('created_by', auth()->id())
              ->orWhereNull('created_by');
        });

        return response()->json($query->orderBy('name')->get());
    }

    public function bulkAssignCategory(Request $request)
    {
        $request->validate([
            'question_ids' => 'required|array|min:1',
            'question_ids.*' => 'exists:questions,id',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        Question::where('created_by', auth()->id())
            ->whereIn('id', $request->question_ids)
            ->update(['category_id' => $request->category_id]);

        return back()->with('success', 'Đã cập nhật danh mục cho các câu hỏi đã chọn!');
    }

    public function apiCategoryStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'subject_id' => 'nullable|exists:subjects,id',
        ]);

        $cat = Category::firstOrCreate([
            'name' => trim($request->name),
            'subject_id' => $request->subject_id,
        ], [
            'created_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'category' => $cat
        ]);
    }
}
