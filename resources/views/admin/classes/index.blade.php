@extends('layouts.admin')

@section('title', 'Quản lý Lớp học')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="px-6 py-5 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <h3 class="font-semibold text-slate-800">Danh sách Lớp học</h3>
        
        <div class="flex flex-wrap items-center gap-3">
            <form action="{{ route('admin.classes.index') }}" method="GET" class="flex items-center gap-2">
                @if(request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif
                <select name="course_id" onchange="this.form.submit()" class="px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">
                    <option value="">-- Tất cả Khóa học --</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                            {{ $course->name }} ({{ $course->semester }} - {{ $course->academic_year }})
                        </option>
                    @endforeach
                </select>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm kiếm lớp..." class="px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500 w-52 shadow-sm">
            </form>
            
            <a href="{{ route('admin.classes.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors whitespace-nowrap">
                Thêm mới
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="m-6 p-4 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-100">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    <th class="px-6 py-4 border-b border-slate-100 whitespace-nowrap">Mã Lớp</th>
                    <th class="px-6 py-4 border-b border-slate-100 whitespace-nowrap">Tên Lớp</th>
                    <th class="px-6 py-4 border-b border-slate-100 whitespace-nowrap">Khóa học / Học kỳ</th>
                    <th class="px-6 py-4 border-b border-slate-100 whitespace-nowrap">Giảng viên Chủ nhiệm</th>
                    <th class="px-6 py-4 border-b border-slate-100 text-center whitespace-nowrap">Sĩ số</th>
                    <th class="px-6 py-4 border-b border-slate-100 text-center whitespace-nowrap">Trạng thái</th>
                    <th class="px-6 py-4 border-b border-slate-100 text-center whitespace-nowrap">Phân công Bộ môn</th>
                    <th class="px-6 py-4 border-b border-slate-100 text-right whitespace-nowrap">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($classes as $class)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4 text-sm font-bold text-slate-900 whitespace-nowrap">{{ $class->code }}</td>
                        <td class="px-6 py-4 text-sm text-slate-700 font-medium whitespace-nowrap">{{ $class->name }}</td>
                        <td class="px-6 py-4 text-sm text-slate-600 whitespace-nowrap">
                            @if($class->course)
                                <span class="px-2.5 py-1 bg-indigo-50 text-indigo-700 font-semibold text-xs rounded-lg border border-indigo-100 inline-block whitespace-nowrap">
                                    {{ $class->course->name }} ({{ $class->course->semester }})
                                </span>
                            @else
                                <span class="text-slate-400 italic text-xs whitespace-nowrap">Chưa gán</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600 whitespace-nowrap">{{ $class->teacher->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm text-center text-slate-600 font-bold whitespace-nowrap">{{ $class->students_count }}</td>
                        <td class="px-6 py-4 text-center whitespace-nowrap">
                            @if($class->status == 'active')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 whitespace-nowrap">Hoạt động</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-rose-100 text-rose-800 whitespace-nowrap">Đã khóa</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center whitespace-nowrap">
                            <a href="{{ route('admin.classes.show', $class) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-indigo-50 text-indigo-700 hover:bg-indigo-100 border border-indigo-100 transition-colors whitespace-nowrap" title="Phân công giảng viên bộ môn">
                                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                Phân công ({{ $class->assigned_subjects_count }} môn)
                            </a>
                        </td>
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <a href="{{ route('admin.classes.edit', $class) }}" class="p-1.5 text-slate-400 hover:text-indigo-600 rounded-lg hover:bg-indigo-50 transition-colors inline-block" title="Sửa">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-slate-500">
                            Không tìm thấy lớp học nào.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="px-6 py-4 border-t border-slate-100">
        {{ $classes->links() }}
    </div>
</div>
@endsection
