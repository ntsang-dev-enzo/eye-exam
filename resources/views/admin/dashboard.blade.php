@extends('layouts.admin')

@section('title', 'Tổng quan Quản trị')

@section('content')
<div class="space-y-6">
    <!-- Page Header with Primary Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Tổng quan hệ thống</h1>
            <p class="text-sm text-slate-500 mt-0.5">Thống kê hoạt động, đề thi khảo thí và cơ sở dữ liệu đào tạo toàn trường</p>
        </div>
        <div class="flex items-center gap-2.5">
            <a href="{{ route('admin.subjects.create') }}" 
               class="inline-flex items-center h-9 px-3.5 bg-white hover:bg-slate-50 text-slate-700 text-sm font-medium rounded-md border border-slate-200 transition-colors">
                <svg class="w-4 h-4 mr-1.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tạo môn học
            </a>
            <a href="{{ route('admin.khoa-hoc.create') }}" 
               class="inline-flex items-center h-9 px-3.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tạo khóa học
            </a>
        </div>
    </div>

    <!-- Clean Metric Strip (SaaS Style, Subtle Borders, No Watermarks) -->
    <div class="bg-white border border-slate-200 rounded-lg grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 divide-y sm:divide-y-0 sm:divide-x divide-slate-200">
        <!-- Metric 1: Users -->
        <div class="p-5">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Tổng người dùng</p>
            <p class="text-2xl font-bold text-slate-900 mt-1">{{ number_format($totalUsers) }}</p>
            <p class="text-xs text-slate-500 mt-1.5 flex items-center gap-1.5">
                <span class="font-medium text-slate-700">{{ $studentCount }}</span> sinh viên
                <span class="text-slate-300">•</span>
                <span class="font-medium text-slate-700">{{ $teacherCount }}</span> giảng viên
            </p>
        </div>

        <!-- Metric 2: Subjects -->
        <div class="p-5">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Môn học</p>
            <p class="text-2xl font-bold text-slate-900 mt-1">{{ number_format($totalSubjects) }}</p>
            <p class="text-xs text-slate-500 mt-1.5">
                <span class="font-medium text-slate-700">{{ $activeSubjects }}</span> môn đang hoạt động
            </p>
        </div>

        <!-- Metric 3: Courses & Classes -->
        <div class="p-5">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Khóa học & Lớp</p>
            <p class="text-2xl font-bold text-slate-900 mt-1">{{ number_format($totalCourses) }}</p>
            <p class="text-xs text-slate-500 mt-1.5 flex items-center gap-1.5">
                <span class="font-medium text-slate-700">{{ $totalCourses }}</span> khóa học
                <span class="text-slate-300">•</span>
                <span class="font-medium text-slate-700">{{ $totalClasses }}</span> lớp học
            </p>
        </div>

        <!-- Metric 4: Exams -->
        <div class="p-5">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Đề thi khảo thí</p>
            <p class="text-2xl font-bold text-slate-900 mt-1">{{ number_format($totalExams) }}</p>
            <p class="text-xs text-slate-500 mt-1.5">
                <span class="font-medium text-blue-700">{{ $publishedExams }}</span> đề đang mở thi
            </p>
        </div>
    </div>

    <!-- Main Section: Recent Exams Table -->
    <div class="bg-white border border-slate-200 rounded-lg overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
            <div>
                <h2 class="text-base font-semibold text-slate-900">Đề thi gần đây</h2>
                <p class="text-xs text-slate-500 mt-0.5">Danh sách các đề thi mới nhất được tạo trong hệ thống</p>
            </div>
            <a href="{{ route('admin.exams.index') }}" 
               class="text-xs font-semibold text-blue-600 hover:text-blue-700 transition-colors">
                Xem tất cả ({{ $totalExams }}) →
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                        <th class="py-3 px-5">Mã đề</th>
                        <th class="py-3 px-5">Tên đề thi</th>
                        <th class="py-3 px-5">Môn học</th>
                        <th class="py-3 px-5">Người tạo</th>
                        <th class="py-3 px-5 text-center">Thời lượng</th>
                        <th class="py-3 px-5 text-center">Trạng thái</th>
                        <th class="py-3 px-5 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-sm">
                    @forelse($recentExams as $exam)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3 px-5 font-mono text-xs font-semibold text-slate-900">
                                {{ $exam->code }}
                            </td>
                            <td class="py-3 px-5 font-medium text-slate-900">
                                {{ $exam->title }}
                            </td>
                            <td class="py-3 px-5 text-slate-600">
                                {{ $exam->subject->name ?? '—' }}
                            </td>
                            <td class="py-3 px-5 text-slate-600">
                                {{ $exam->creator->name ?? '—' }}
                            </td>
                            <td class="py-3 px-5 text-center font-mono text-xs text-slate-700">
                                {{ $exam->duration }} phút
                            </td>
                            <td class="py-3 px-5 text-center">
                                @if($exam->status === 'published')
                                    <span class="text-xs font-medium text-emerald-600">Đang mở</span>
                                @else
                                    <span class="text-xs font-medium text-slate-500">Đóng</span>
                                @endif
                            </td>
                            <td class="py-3 px-5 text-right">
                                <a href="{{ route('admin.exams.monitor', $exam) }}" 
                                   class="inline-flex items-center text-xs font-semibold text-blue-600 hover:text-blue-700">
                                    Giám sát
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-sm text-slate-500">
                                Chưa có đề thi nào trong hệ thống
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Secondary Section: Recent Courses Table -->
    <div class="bg-white border border-slate-200 rounded-lg overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
            <div>
                <h2 class="text-base font-semibold text-slate-900">Khóa học & Học kỳ đào tạo</h2>
                <p class="text-xs text-slate-500 mt-0.5">Các khóa học đang được phân bổ trong hệ thống</p>
            </div>
            <a href="{{ route('admin.khoa-hoc.index') }}" 
               class="text-xs font-semibold text-blue-600 hover:text-blue-700 transition-colors">
                Xem tất cả ({{ $totalCourses }}) →
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                        <th class="py-3 px-5">Tên khóa học</th>
                        <th class="py-3 px-5">Học kỳ</th>
                        <th class="py-3 px-5">Năm học</th>
                        <th class="py-3 px-5 text-center">Số môn học</th>
                        <th class="py-3 px-5 text-center">Tổng tín chỉ</th>
                        <th class="py-3 px-5 text-center">Trạng thái</th>
                        <th class="py-3 px-5 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-sm">
                    @forelse($recentCourses as $course)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3 px-5 font-semibold text-slate-900">
                                <a href="{{ route('admin.khoa-hoc.show', $course) }}" class="hover:text-blue-600 transition-colors">
                                    {{ $course->name }}
                                </a>
                            </td>
                            <td class="py-3 px-5 text-slate-600">
                                {{ $course->semester }}
                            </td>
                            <td class="py-3 px-5 text-slate-600 font-mono text-xs">
                                {{ $course->academic_year }}
                            </td>
                            <td class="py-3 px-5 text-center text-slate-700 font-medium">
                                {{ $course->subjects_count }} môn
                            </td>
                            <td class="py-3 px-5 text-center font-mono text-xs font-semibold text-slate-700">
                                {{ $course->total_credits }} TC
                            </td>
                            <td class="py-3 px-5 text-center">
                                @if($course->status === 'active')
                                    <span class="text-xs font-medium text-emerald-600">Hoạt động</span>
                                @else
                                    <span class="text-xs font-medium text-slate-500">Tạm dừng</span>
                                @endif
                            </td>
                            <td class="py-3 px-5 text-right">
                                <div class="flex items-center justify-end gap-3 text-xs">
                                    <a href="{{ route('admin.khoa-hoc.edit', $course) }}" class="font-medium text-slate-600 hover:text-blue-600">
                                        Sửa
                                    </a>
                                    <a href="{{ route('admin.khoa-hoc.show', $course) }}" class="font-medium text-blue-600 hover:text-blue-700">
                                        Chi tiết
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-sm text-slate-500">
                                Chưa có khóa học nào được tạo
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
