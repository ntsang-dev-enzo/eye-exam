<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Subject;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $subjects = auth()->user()->subjects()->where('status', true)->get();
        
        $query = Category::with('subject')
            ->withCount(['questions', 'exams'])
            ->where(function($q) {
                $q->where('created_by', auth()->id())
                  ->orWhereNull('created_by');
            });

        if ($request->has('subject_id') && $request->subject_id != '') {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        $categories = $query->orderBy('subject_id')->orderBy('name')->get();

        // Group categories by subject for clean organization
        $categoriesBySubject = $categories->groupBy(function($cat) {
            return $cat->subject ? $cat->subject->name : 'Chung / Chưa gắn môn';
        });

        return view('teacher.categories.index', compact('categories', 'categoriesBySubject', 'subjects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'subject_id' => 'required|exists:subjects,id',
            'description' => 'nullable|string|max:1000',
        ]);

        Category::create([
            'name' => trim($request->name),
            'subject_id' => $request->subject_id,
            'created_by' => auth()->id(),
            'description' => $request->description,
        ]);

        return redirect()->route('teacher.categories.index')->with('success', 'Tạo danh mục môn học thành công!');
    }

    public function update(Request $request, Category $category)
    {
        if ($category->created_by && $category->created_by !== auth()->id()) {
            abort(403, 'Bạn không có quyền sửa danh mục này.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'subject_id' => 'required|exists:subjects,id',
            'description' => 'nullable|string|max:1000',
        ]);

        $category->update([
            'name' => trim($request->name),
            'subject_id' => $request->subject_id,
            'description' => $request->description,
        ]);

        return redirect()->route('teacher.categories.index')->with('success', 'Cập nhật danh mục thành công!');
    }

    public function destroy(Category $category)
    {
        if ($category->created_by && $category->created_by !== auth()->id()) {
            abort(403, 'Bạn không có quyền xóa danh mục này.');
        }

        // Set category_id to null for questions in this category
        $category->questions()->update(['category_id' => null]);
        $category->exams()->update(['category_id' => null]);

        $category->delete();

        return redirect()->route('teacher.categories.index')->with('success', 'Đã xóa danh mục thành công!');
    }
}
