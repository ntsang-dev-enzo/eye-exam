<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Subject::query();
        
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }
        
        $subjects = $query->latest()->paginate(10);
        return view('admin.subjects.index', compact('subjects'));
    }

    public function create()
    {
        $departments = \App\Models\User::where('role', 'teacher')
            ->where('status', 'active')
            ->get()
            ->groupBy('department');
            
        return view('admin.subjects.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|unique:subjects,code|max:50',
            'name' => 'required|string|max:255',
            'credits' => 'required|integer|min:1|max:15',
            'description' => 'nullable|string',
            'status' => 'boolean',
            'teachers' => 'nullable|array',
            'teachers.*' => 'exists:users,id',
        ]);

        $subject = Subject::create([
            'code' => strtoupper($request->code),
            'name' => $request->name,
            'credits' => $request->credits,
            'description' => $request->description,
            'status' => $request->has('status'),
        ]);

        if ($request->has('teachers')) {
            $subject->teachers()->sync($request->teachers);
        }

        return redirect()->route('admin.subjects.index')->with('success', 'Thêm môn học và phân công giảng viên thành công.');
    }

    public function edit(Subject $subject)
    {
        $departments = \App\Models\User::where('role', 'teacher')
            ->where('status', 'active')
            ->get()
            ->groupBy('department');
            
        return view('admin.subjects.edit', compact('subject', 'departments'));
    }

    public function update(Request $request, Subject $subject)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:subjects,code,' . $subject->id,
            'name' => 'required|string|max:255',
            'credits' => 'required|integer|min:1|max:15',
            'description' => 'nullable|string',
            'status' => 'boolean',
            'teachers' => 'nullable|array',
            'teachers.*' => 'exists:users,id',
        ]);

        $subject->update([
            'code' => strtoupper($request->code),
            'name' => $request->name,
            'credits' => $request->credits,
            'description' => $request->description,
            'status' => $request->has('status'),
        ]);

        if ($request->has('teachers')) {
            $subject->teachers()->sync($request->teachers);
        } else {
            $subject->teachers()->detach();
        }

        return redirect()->route('admin.subjects.index')->with('success', 'Cập nhật môn học thành công.');
    }
}
