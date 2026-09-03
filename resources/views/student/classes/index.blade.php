@extends('layouts.student')

@section('title', 'Lớp học của tôi')

@section('content')
<div class="space-y-6">
    <!-- Top Header & Metrics Summary -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200/80 pb-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Lớp học của tôi</h1>
            <p class="text-xs text-slate-500 font-medium mt-0.5">Danh sách các lớp học bạn đang tham gia trong học kỳ hiện tại</p>
        </div>

        <!-- Top Metrics Cards -->
        <div class="flex items-center gap-3">
            <div class="bg-white rounded-xl px-4 py-2 border border-slate-200/80 shadow-2xs flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 v5m-4 0h4"></path></svg>
                </div>
                <div>
                    <span class="text-[10px] text-slate-400 font-semibold uppercase block">Tổng số lớp</span>
                    <span class="text-sm font-extrabold text-slate-900">{{ $classes->count() }} lớp</span>
                </div>
            </div>

            <div class="bg-white rounded-xl px-4 py-2 border border-slate-200/80 shadow-2xs flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <span class="text-[10px] text-slate-400 font-semibold uppercase block">Điểm danh TB</span>
                    <span class="text-sm font-extrabold text-emerald-600">100%</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Classes Grid (grid-cols-3 gap-6) -->
    @if($classes->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($classes as $class)
                <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-2xs hover:shadow-md transition-all flex flex-col justify-between group">
                    <div class="space-y-4">
                        <!-- Top Badges -->
                        <div class="flex items-center justify-between">
                            <span class="px-2.5 py-1 bg-blue-50 text-blue-700 font-bold text-xs rounded-lg border border-blue-100 font-mono">
                                {{ $class->code }}
                            </span>
                            @if($class->course)
                                <span class="text-xs font-semibold text-slate-500">
                                    {{ $class->course->semester }}
                                </span>
                            @endif
                        </div>

                        <!-- Card Header & Icon Thumbnail -->
                        <div class="flex items-start gap-3.5">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200/60 flex items-center justify-center text-blue-600 shrink-0 shadow-2xs group-hover:bg-blue-600 group-hover:text-white transition-all">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path></svg>
                            </div>
                            <div class="min-w-0">
                                <h2 class="text-base font-bold text-slate-900 group-hover:text-blue-600 transition-colors leading-snug line-clamp-2">
                                    {{ $class->name }}
                                </h2>
                                @if($class->course)
                                    <p class="text-xs text-slate-500 mt-0.5 truncate">Khóa: {{ $class->course->name }} ({{ $class->course->academic_year }})</p>
                                @endif
                            </div>
                        </div>

                        @if($class->description)
                            <p class="text-xs text-slate-400 line-clamp-2">{{ $class->description }}</p>
                        @endif

                        <!-- 2-Column Info Panel -->
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 space-y-2">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-semibold text-slate-400 text-[11px]">Giảng viên:</span>
                                <span class="font-bold text-slate-800 truncate max-w-[140px]">{{ $class->teacher->name ?? 'N/A' }}</span>
                            </div>
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-semibold text-slate-400 text-[11px]">Sĩ số lớp:</span>
                                <span class="font-bold text-blue-600">{{ $class->students_count }} sinh viên</span>
                            </div>
                        </div>
                    </div>

                    <div class="pt-5">
                        <a href="{{ route('student.classes.show', $class) }}" 
                            class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-bold text-xs rounded-xl shadow-2xs transition-all flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            Xem chi tiết Lớp
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $classes->links() }}
        </div>
    @else
        <div class="bg-white rounded-2xl border border-slate-200/80 p-12 text-center shadow-2xs">
            <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-3 border border-blue-100">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 v5m-4 0h4"></path></svg>
            </div>
            <h3 class="text-base font-bold text-slate-900 mb-1">Chưa tham gia lớp học nào</h3>
            <p class="text-xs text-slate-500">Bạn hiện tại chưa được xếp vào lớp học nào.</p>
        </div>
    @endif
</div>
@endsection
