@extends('layouts.admin')

@section('title', 'Chi tiết Khóa học - ' . $course->name)

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb & Top Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 font-medium mb-1">
                <a href="{{ route('admin.khoa-hoc.index') }}" class="hover:text-blue-600 transition-colors">Quản lý Khóa học</a>
                <span>/</span>
                <span class="text-slate-800">{{ $course->name }}</span>
            </div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">{{ $course->name }}</h1>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('admin.khoa-hoc.edit', $course) }}" 
               class="inline-flex items-center h-9 px-3.5 bg-blue-600 hover:bg-blue-700 text-white font-medium text-xs rounded-md shadow-xs transition-colors">
                <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Chỉnh sửa
            </a>
            <a href="{{ route('admin.khoa-hoc.index') }}" 
               class="inline-flex items-center h-9 px-3 bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 font-medium text-xs rounded-md transition-colors">
                <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Quay lại
            </a>
        </div>
    </div>

    <!-- Course Info Card (Clean SaaS Summary Strip) -->
    <div class="bg-white rounded-lg border border-slate-200 p-5">
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            <div>
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 block mb-1">Học Kỳ</span>
                <span class="inline-flex items-center px-2 py-0.5 bg-slate-100 text-slate-800 font-medium text-xs rounded border border-slate-200">
                    {{ $course->semester }}
                </span>
            </div>
            <div>
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 block mb-1">Năm Học</span>
                <span class="text-slate-900 font-medium font-mono text-sm block">{{ $course->academic_year }}</span>
            </div>
            <div>
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 block mb-1">Tổng Số Môn Học</span>
                <span class="text-slate-900 font-semibold text-sm block">{{ $course->subjects->count() }} môn</span>
            </div>
            <div>
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 block mb-1">Tổng Số Tín Chỉ</span>
                <span class="text-blue-700 font-bold font-mono text-sm block">{{ $course->total_credits }} TC</span>
            </div>
            <div>
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 block mb-1">Trạng Thái</span>
                @if($course->status === 'active')
                    <span class="inline-flex items-center px-2 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded text-xs font-medium">
                        Hoạt động
                    </span>
                @else
                    <span class="inline-flex items-center px-2 py-0.5 bg-slate-100 text-slate-700 border border-slate-200 rounded text-xs font-medium">
                        Tạm dừng
                    </span>
                @endif
            </div>
        </div>

        @if($course->description)
            <div class="mt-4 pt-4 border-t border-slate-200">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 block mb-1">Mô tả</span>
                <p class="text-sm text-slate-600 leading-relaxed">{{ $course->description }}</p>
            </div>
        @endif
    </div>

    <!-- Subjects List Card -->
    <div class="bg-white rounded-lg border border-slate-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-slate-900">
                Danh sách Môn học thuộc Khóa học
            </h2>
            <span class="text-xs text-slate-400">Cập nhật: {{ $course->updated_at->format('d/m/Y H:i') }}</span>
        </div>

        @if($course->subjects->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-semibold tracking-wider text-slate-500 uppercase">
                            <th class="py-3 px-5">Mã Môn</th>
                            <th class="py-3 px-5">Tên Môn Học</th>
                            <th class="py-3 px-5 text-center">Tín Chỉ</th>
                            <th class="py-3 px-5">Giảng Viên Phụ Trách</th>
                            <th class="py-3 px-5 text-center">Trạng Thái</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 text-sm font-medium">
                        @foreach($course->subjects as $subject)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-3.5 px-5 font-mono font-semibold text-slate-900 text-xs">
                                    {{ $subject->code }}
                                </td>
                                <td class="py-3.5 px-5 font-semibold text-slate-900">
                                    {{ $subject->name }}
                                </td>
                                <td class="py-3.5 px-5 text-center">
                                    <span class="px-2 py-0.5 bg-blue-50 text-blue-700 border border-blue-200 rounded text-xs font-mono font-semibold">
                                        {{ $subject->credits ?? 3 }} TC
                                    </span>
                                </td>
                                <td class="py-3.5 px-5 text-slate-600">
                                    @if($subject->teachers && $subject->teachers->count() > 0)
                                        <div class="flex items-center gap-2">
                                            <span class="w-6 h-6 rounded-full bg-slate-100 border border-slate-200 text-slate-700 flex items-center justify-center text-[10px] font-semibold">
                                                {{ substr($subject->teachers->first()->name, 0, 1) }}
                                            </span>
                                            <span class="text-sm text-slate-800">{{ $subject->teachers->pluck('name')->join(', ') }}</span>
                                        </div>
                                    @else
                                        <span class="text-xs text-slate-400 italic">Chưa phân công</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-5 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Đang giảng dạy
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-8 text-center text-slate-500 text-sm">
                Chưa có môn học nào được liên kết với khóa học này.
            </div>
        @endif
    </div>
</div>
@endsection
