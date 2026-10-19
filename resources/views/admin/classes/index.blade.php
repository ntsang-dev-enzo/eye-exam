@extends('layouts.admin')

@section('title', 'Quản lý Lớp học phần')

@section('content')
<div class="space-y-6">
    <!-- Top Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Danh sách Lớp học phần</h1>
            <p class="text-sm text-slate-500 mt-0.5">Quản lý các lớp môn học theo học kỳ, giảng viên đứng lớp và sĩ số sinh viên</p>
        </div>
        <div>
            <a href="{{ route('admin.classes.create') }}" 
               class="inline-flex items-center h-9 px-3.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md shadow-xs transition-colors">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Mở Lớp học phần mới
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="p-3.5 bg-emerald-50 border border-emerald-200 rounded-md flex items-center justify-between text-emerald-800 text-sm font-medium">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                <span>{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">&times;</button>
        </div>
    @endif

    @if(session('error'))
        <div class="p-3.5 bg-rose-50 border border-rose-200 rounded-md flex items-center justify-between text-rose-800 text-sm font-medium">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <span>{{ session('error') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700">&times;</button>
        </div>
    @endif

    <!-- Filters Bar -->
    <div class="bg-white rounded-lg border border-slate-200 p-4">
        <form action="{{ route('admin.classes.index') }}" method="GET" class="space-y-3">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                <!-- Search -->
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Tìm kiếm</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Mã hoặc tên lớp..." 
                        class="w-full h-9 px-3 bg-white border border-slate-200 rounded-md text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:border-blue-600">
                </div>

                <!-- Semester -->
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Học kỳ</label>
                    <select name="semester_id" onchange="this.form.submit()" class="w-full h-9 px-3 bg-white border border-slate-200 rounded-md text-sm text-slate-700 focus:border-blue-600">
                        <option value="">-- Tất cả học kỳ --</option>
                        @foreach($semesters as $sem)
                            <option value="{{ $sem->id }}" {{ (request('semester_id') == $sem->id || (!request()->filled('semester_id') && !request()->has('all_semesters') && $sem->is_current)) ? 'selected' : '' }}>
                                {{ $sem->name }} ({{ $sem->academic_year }}) {{ $sem->is_current ? '★ Hiện tại' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Subject -->
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Môn học</label>
                    <select name="subject_id" onchange="this.form.submit()" class="w-full h-9 px-3 bg-white border border-slate-200 rounded-md text-sm text-slate-700 focus:border-blue-600">
                        <option value="">-- Tất cả môn học --</option>
                        @foreach($subjects as $sub)
                            <option value="{{ $sub->id }}" {{ request('subject_id') == $sub->id ? 'selected' : '' }}>
                                {{ $sub->code }} - {{ $sub->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Faculty -->
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Khoa</label>
                    <select name="faculty_id" onchange="this.form.submit()" class="w-full h-9 px-3 bg-white border border-slate-200 rounded-md text-sm text-slate-700 focus:border-blue-600">
                        <option value="">-- Tất cả khoa --</option>
                        @foreach($faculties as $fac)
                            <option value="{{ $fac->id }}" {{ request('faculty_id') == $fac->id ? 'selected' : '' }}>
                                {{ $fac->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex items-center justify-between pt-1">
                <input type="hidden" name="all_semesters" value="{{ request()->has('all_semesters') || request()->filled('semester_id') ? '1' : '0' }}">
                <div class="text-xs text-slate-500">
                    Hiển thị tổng số: <span class="font-bold text-slate-700">{{ $classes->total() }}</span> lớp học phần
                </div>
                @if(request()->anyFilled(['search', 'semester_id', 'subject_id', 'faculty_id']))
                    <a href="{{ route('admin.classes.index', ['all_semesters' => 1]) }}" class="text-xs text-blue-600 hover:text-blue-800 font-medium">Xóa bộ lọc</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Container -->
    <div class="bg-white rounded-lg border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                        <th class="py-3 px-4">Mã Lớp HP</th>
                        <th class="py-3 px-4">Tên Lớp / Môn học</th>
                        <th class="py-3 px-4">Học kỳ</th>
                        <th class="py-3 px-4">Giảng viên đứng lớp</th>
                        <th class="py-3 px-4 text-center">Sĩ số (Đã ĐK / Tối đa)</th>
                        <th class="py-3 px-4 text-center">Trạng thái</th>
                        <th class="py-3 px-4 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($classes as $class)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3 px-4 font-mono font-semibold text-blue-700">
                                <a href="{{ route('admin.classes.show', $class) }}" class="hover:underline">
                                    {{ $class->code }}
                                </a>
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-medium text-slate-900">{{ $class->name }}</div>
                                @if($class->subject)
                                    <div class="text-xs text-slate-500 flex items-center gap-1.5 mt-0.5">
                                        <span class="font-mono text-blue-600">{{ $class->subject->code }}</span>
                                        <span>•</span>
                                        <span>{{ $class->subject->name }}</span>
                                        <span class="text-[11px] font-semibold text-slate-600 font-mono">{{ $class->subject->credits }} TC</span>
                                    </div>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-xs">
                                @if($class->semester)
                                    <div class="font-semibold text-slate-800">{{ $class->semester->name }}</div>
                                    <div class="text-slate-400 text-[11px]">{{ $class->semester->academic_year }}</div>
                                @else
                                    <span class="text-slate-400">---</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-sm">
                                @if($class->teacher)
                                    <div class="font-medium text-slate-900">{{ $class->teacher->name }}</div>
                                    <div class="text-xs text-slate-400">{{ $class->teacher->email }}</div>
                                @else
                                    <span class="text-amber-600 text-xs font-medium">Chưa phân công</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                @php
                                    $isFull = $class->students_count >= $class->max_students;
                                @endphp
                                <span class="text-xs font-mono font-semibold {{ $isFull ? 'text-rose-600' : 'text-slate-700' }}">
                                    {{ $class->students_count }} / {{ $class->max_students }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($class->status === 'open')
                                    <span class="inline-flex items-center gap-1.5 text-xs font-medium text-emerald-600">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                                        Đang mở ĐK
                                    </span>
                                @elseif($class->status === 'ongoing')
                                    <span class="inline-flex items-center gap-1.5 text-xs font-medium text-blue-600">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500 shrink-0"></span>
                                        Đang học
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-500">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400 shrink-0"></span>
                                        Đã đóng
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right space-x-2">
                                <a href="{{ route('admin.classes.edit', $class) }}" class="text-blue-600 hover:text-blue-800 font-medium text-xs">Sửa</a>
                                <form action="{{ route('admin.classes.destroy', $class) }}" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc muốn xóa lớp học phần này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-600 hover:text-rose-800 font-medium text-xs">Xóa</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-slate-400">Không tìm thấy Lớp học phần nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($classes->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $classes->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
