@extends('layouts.teacher')

@section('title', 'Lớp học phần phụ trách')

@section('content')
<div class="max-w-6xl mx-auto space-y-6 sm:space-y-8">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2">
        <div class="space-y-1.5">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 border border-blue-100 text-blue-700 text-xs font-bold tracking-wide uppercase">
                <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
                Tổ chức & Đào tạo
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-[#0a2540] tracking-tight">Lớp học phần phụ trách</h1>
            <p class="text-sm text-slate-500 font-medium">Danh sách các lớp học phần bạn được phân công đứng lớp giảng dạy</p>
        </div>

        <div class="flex items-center gap-3 shrink-0">
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-4 border border-blue-100 shadow-[0_4px_20px_-4px_rgba(59,130,246,0.1)] flex items-center gap-3.5 min-w-[175px]">
                <div class="w-11 h-11 rounded-2xl bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 v5m-4 0h4"></path></svg>
                </div>
                <div>
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-0.5">Tổng số lớp</span>
                    <span class="text-xl font-black text-slate-900 font-mono">{{ $classes->count() }}</span>
                    <span class="text-xs font-bold text-slate-400 font-mono ml-1">lớp</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Classes Grid -->
    @if($classes->isEmpty())
        <div class="bg-white/80 backdrop-blur-sm p-16 rounded-3xl border border-blue-100 shadow-[0_10px_35px_-10px_rgba(59,130,246,0.08)] text-center">
            <div class="w-20 h-20 bg-gradient-to-tr from-blue-50 to-indigo-50 text-blue-400 rounded-3xl flex items-center justify-center mx-auto mb-4 border border-blue-100">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 v5m-4 0h4"></path></svg>
            </div>
            <h3 class="text-lg font-extrabold text-[#0a2540] mb-2">Chưa có lớp học phần nào</h3>
            <p class="text-sm text-slate-500 max-w-sm mx-auto">Bạn hiện tại chưa được phân công đứng lớp học phần nào trong hệ thống.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($classes as $class)
                <div class="bg-white/80 backdrop-blur-sm rounded-3xl border border-blue-100 shadow-[0_4px_20px_-4px_rgba(59,130,246,0.07)] hover:shadow-xl hover:shadow-blue-500/10 hover:border-blue-300 transition-all duration-300 p-6 flex flex-col justify-between group hover:-translate-y-1">
                    <div class="space-y-4">
                        <div class="flex justify-between items-center gap-2">
                            <span class="px-3 py-1 bg-blue-50 text-blue-700 font-extrabold text-xs rounded-xl border border-blue-100 font-mono flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path></svg>
                                {{ $class->code }}
                            </span>
                            <div class="flex items-center gap-1.5 text-xs font-medium">
                                @if($class->status == 'open' || $class->status == 'active')
                                    <span class="inline-flex items-center gap-1.5 text-emerald-600">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                                        Đang mở
                                    </span>
                                @elseif($class->status == 'ongoing')
                                    <span class="inline-flex items-center gap-1.5 text-blue-600">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500 shrink-0"></span>
                                        Đang học
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 text-slate-500">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400 shrink-0"></span>
                                        Đã đóng
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-start gap-3.5">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white shrink-0 shadow-md shadow-blue-500/20 group-hover:scale-105 transition-transform duration-200">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 v5m-4 0h4"></path></svg>
                            </div>
                            <div class="min-w-0">
                                <h3 class="text-lg font-black text-[#0a2540] group-hover:text-blue-600 transition-colors leading-snug">{{ $class->name }}</h3>
                                @if($class->subject)
                                    <p class="text-xs text-blue-600 font-semibold mt-0.5">
                                        {{ $class->subject->name }} ({{ $class->subject->credits }} TC)
                                    </p>
                                @elseif($class->course)
                                    <p class="text-xs text-blue-600 font-semibold mt-0.5">{{ $class->course->name }}</p>
                                @endif
                                @if($class->semester)
                                    <p class="text-[11px] text-slate-500 font-medium">{{ $class->semester->name }} ({{ $class->semester->academic_year }})</p>
                                @endif
                                <p class="text-xs text-slate-400 mt-1 line-clamp-2">{{ $class->description ?? 'Không có mô tả' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 pt-4 border-t border-blue-50 flex items-center justify-between">
                        <div class="flex items-center text-xs font-bold text-slate-500 bg-blue-50/60 px-2.5 py-1.5 rounded-lg border border-blue-100">
                            <svg class="w-3.5 h-3.5 mr-1.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <span class="font-mono text-blue-700">{{ $class->students_count }}</span>
                            <span class="ml-1 text-slate-400">sinh viên</span>
                        </div>
                        <a href="{{ route('teacher.classes.show', $class) }}"
                            class="px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold text-xs rounded-xl shadow-sm shadow-blue-500/20 hover:shadow-md hover:shadow-blue-500/30 transition-all flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            Quản lý Lớp
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>
@endsection
