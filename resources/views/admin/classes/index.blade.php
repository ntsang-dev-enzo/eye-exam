@extends('layouts.admin')

@section('title', 'Quản lý lớp học')

@section('content')
<div class="space-y-5">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Quản lý lớp học</h1>
            <p class="text-xs text-slate-500 mt-0.5">Danh sách lớp sinh hoạt, sĩ số và phân công giảng viên bộ môn</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.classes.create') }}" class="h-9 px-3.5 inline-flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-md transition-colors shadow-none">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Thêm lớp học
            </a>
        </div>
    </div>

    <!-- Filter Toolbar -->
    <div class="bg-white border border-slate-200 rounded-lg p-3.5">
        <form action="{{ route('admin.classes.index') }}" method="GET" class="flex flex-wrap items-center gap-2.5">
            <!-- Course Filter -->
            <select name="course_id" onchange="this.form.submit()" class="h-9 px-3 text-sm bg-white border border-slate-200 rounded-md text-slate-700 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-colors">
                <option value="">-- Tất cả khóa học --</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                        {{ $course->name }} ({{ $course->semester }} - {{ $course->academic_year }})
                    </option>
                @endforeach
            </select>

            <!-- Search Input -->
            <div class="relative min-w-[220px] flex-1 sm:flex-initial">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}" 
                       placeholder="Tìm mã hoặc tên lớp..." 
                       class="w-full h-9 pl-9 pr-3 text-sm bg-white border border-slate-200 rounded-md placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-colors">
            </div>

            <!-- Submit Filter -->
            <button type="submit" class="h-9 px-3.5 inline-flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-md transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                Lọc
            </button>

            @if(request()->hasAny(['search', 'course_id']) && (request('search') || request('course_id')))
                <a href="{{ route('admin.classes.index') }}" class="h-9 px-3 inline-flex items-center text-xs font-medium text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 rounded-md transition-colors">
                    Đặt lại
                </a>
            @endif
        </form>
    </div>

    <!-- Alert Message -->
    @if(session('success'))
        <div class="p-3.5 rounded-md bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

    <!-- Classes Table -->
    <div class="bg-white border border-slate-200 rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/75 border-b border-slate-200 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                        <th class="py-3 px-4">Mã lớp</th>
                        <th class="py-3 px-4">Tên lớp</th>
                        <th class="py-3 px-4">Khóa học / Học kỳ</th>
                        <th class="py-3 px-4">GV Chủ nhiệm</th>
                        <th class="py-3 px-4 text-center">Sĩ số</th>
                        <th class="py-3 px-4 text-center">Trạng thái</th>
                        <th class="py-3 px-4 text-center">Phân công môn</th>
                        <th class="py-3 px-4 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($classes as $class)
                        <tr class="hover:bg-slate-50/75 transition-colors">
                            <!-- Code -->
                            <td class="py-3 px-4 font-mono text-xs font-semibold text-slate-700 whitespace-nowrap">
                                {{ $class->code }}
                            </td>

                            <!-- Name -->
                            <td class="py-3 px-4 font-medium text-slate-900 whitespace-nowrap">
                                {{ $class->name }}
                            </td>

                            <!-- Course -->
                            <td class="py-3 px-4 text-slate-600 text-xs whitespace-nowrap">
                                @if($class->course)
                                    {{ $class->course->name }} ({{ $class->course->semester }})
                                @else
                                    <span class="text-slate-400 italic">Chưa gán</span>
                                @endif
                            </td>

                            <!-- Homeroom Teacher -->
                            <td class="py-3 px-4 text-slate-700 text-xs whitespace-nowrap">
                                {{ $class->teacher->name ?? 'Chưa chỉ định' }}
                            </td>

                            <!-- Students Count -->
                            <td class="py-3 px-4 text-center font-mono text-xs text-slate-700 whitespace-nowrap">
                                {{ $class->students_count }}
                            </td>

                            <!-- Status -->
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                @if($class->status == 'active')
                                    <span class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Hoạt động
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                        Đã khóa
                                    </span>
                                @endif
                            </td>

                            <!-- Subject Assignments -->
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                <a href="{{ route('admin.classes.show', $class) }}" class="inline-flex items-center gap-1 text-xs font-medium text-blue-600 hover:text-blue-700 transition-colors" title="Phân công giảng viên giảng dạy">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                    ({{ $class->assigned_subjects_count }} môn)
                                </a>
                            </td>

                            <!-- Actions -->
                            <td class="py-3 px-4 text-right">
                                <div class="inline-flex items-center gap-1">
                                    <a href="{{ route('admin.classes.edit', $class) }}" class="p-1 text-slate-400 hover:text-blue-600 rounded transition-colors" title="Chỉnh sửa">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 px-4 text-center">
                                <div class="max-w-xs mx-auto text-center">
                                    <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                    </div>
                                    <div class="text-sm font-medium text-slate-900">Không tìm thấy lớp học</div>
                                    <p class="text-xs text-slate-500 mt-1">Chưa có lớp học nào hoặc không khớp với bộ lọc tìm kiếm.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($classes->hasPages())
            <div class="px-4 py-3 border-t border-slate-200 bg-slate-50/50">
                {{ $classes->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
