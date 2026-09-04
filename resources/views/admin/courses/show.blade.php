@extends('layouts.admin')

@section('title', 'Chi tiết Khóa học - ' . $course->name)

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb & Top Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-sm text-slate-500 font-medium mb-1">
                <a href="{{ route('admin.khoa-hoc.index') }}" class="hover:text-indigo-600 transition-colors">Quản lý Khóa học</a>
                <span>/</span>
                <span class="text-slate-800">{{ $course->name }}</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">{{ $course->name }}</h1>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.khoa-hoc.edit', $course) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl shadow-md transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                Chỉnh sửa
            </a>
            <a href="{{ route('admin.khoa-hoc.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-sm rounded-xl transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Quay lại
            </a>
        </div>
    </div>

    <!-- Course Info Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block mb-1">Học Kỳ</span>
                <span class="inline-flex items-center px-3 py-1 bg-indigo-50 text-indigo-700 font-bold text-sm rounded-lg border border-indigo-100">
                    {{ $course->semester }}
                </span>
            </div>
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block mb-1">Năm Học</span>
                <span class="text-slate-900 font-bold text-base block">{{ $course->academic_year }}</span>
            </div>
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block mb-1">Tổng Số Môn Học</span>
                <span class="text-slate-900 font-bold text-base block">{{ $course->subjects->count() }} môn</span>
            </div>
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block mb-1">Tổng Số Tín Chỉ</span>
                <span class="text-indigo-600 font-black text-base font-mono block">{{ $course->total_credits }} TC</span>
            </div>
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block mb-1">Trạng Thái</span>
                @if($course->status === 'active')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-full font-bold text-xs">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        Đang hoạt động
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-slate-100 text-slate-600 border border-slate-200 rounded-full font-bold text-xs">
                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                        Tạm dừng
                    </span>
                @endif
            </div>
        </div>

        @if($course->description)
            <div class="mt-6 pt-6 border-t border-slate-100">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block mb-1">Mô tả</span>
                <p class="text-sm text-slate-600 font-medium leading-relaxed">{{ $course->description }}</p>
            </div>
        @endif
    </div>

    <!-- Subjects List Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden space-y-4 p-6">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477-4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                Danh sách Môn học thuộc Khóa học
            </h2>
            <span class="text-xs text-slate-400 font-semibold">Cập nhật: {{ $course->updated_at->format('d/m/Y H:i') }}</span>
        </div>

        @if($course->subjects->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/70 border-b border-slate-200/80 text-[11px] font-black tracking-wider text-slate-500 uppercase">
                            <th class="py-3.5 px-4">Mã Môn</th>
                            <th class="py-3.5 px-4">Tên Môn Học</th>
                            <th class="py-3.5 px-4 text-center">Tín Chỉ</th>
                            <th class="py-3.5 px-4">Giảng Viên Phụ Trách</th>
                            <th class="py-3.5 px-4">Trạng Thái</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm font-medium">
                        @foreach($course->subjects as $subject)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-4 px-4 font-mono font-bold text-indigo-600 text-xs">
                                    {{ $subject->code }}
                                </td>
                                <td class="py-4 px-4 font-bold text-slate-900">
                                    {{ $subject->name }}
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <span class="px-2.5 py-1 bg-indigo-50 text-indigo-700 border border-indigo-100/80 rounded-lg text-xs font-mono font-bold">
                                        {{ $subject->credits ?? 3 }} TC
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-slate-600">
                                    @if($subject->teachers && $subject->teachers->count() > 0)
                                        <div class="flex items-center gap-2">
                                            <span class="w-7 h-7 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center text-xs font-bold">
                                                {{ substr($subject->teachers->first()->name, 0, 1) }}
                                            </span>
                                            <span class="font-semibold text-slate-800">{{ $subject->teachers->pluck('name')->join(', ') }}</span>
                                        </div>
                                    @else
                                        <span class="text-xs font-semibold text-slate-400 italic">Chưa phân công</span>
                                    @endif
                                </td>
                                <td class="py-4 px-4">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Đang giảng dạy
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-8 text-center text-slate-400">
                <p class="text-sm font-medium">Chưa có môn học nào được liên kết với khóa học này.</p>
            </div>
        @endif
    </div>
</div>
@endsection
