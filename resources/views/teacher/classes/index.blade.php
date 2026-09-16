@extends('layouts.teacher')

@section('title', 'Quản lý Lớp học')

@section('content')
<div class="max-w-6xl mx-auto space-y-8" x-data="{ activeTab: 'homeroom' }">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2">
        <div class="space-y-1.5">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 border border-blue-100 text-blue-700 text-xs font-bold tracking-wide uppercase">
                <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
                Giảng viên
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-[#0a2540] tracking-tight">Quản lý Lớp học</h1>
            <p class="text-sm text-slate-500 font-medium">Danh sách các lớp học bạn làm chủ nhiệm và các lớp được phân công dạy bộ môn</p>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <div class="flex border-b border-blue-100 gap-1">
        <button @click="activeTab = 'homeroom'"
            :class="{ 'border-blue-600 text-blue-700 bg-blue-50/60 font-black': activeTab === 'homeroom', 'border-transparent text-slate-500 font-medium hover:text-blue-600 hover:bg-blue-50/40': activeTab !== 'homeroom' }"
            class="py-3 px-5 border-b-2 text-sm transition-all flex items-center gap-2 rounded-t-xl">
            <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            Lớp Chủ nhiệm
            <span class="ml-1 px-2 py-0.5 rounded-full text-[11px] font-extrabold bg-blue-100 text-blue-700 font-mono">{{ $homeroomClasses->count() }}</span>
        </button>

        <button @click="activeTab = 'teaching'"
            :class="{ 'border-blue-600 text-blue-700 bg-blue-50/60 font-black': activeTab === 'teaching', 'border-transparent text-slate-500 font-medium hover:text-blue-600 hover:bg-blue-50/40': activeTab !== 'teaching' }"
            class="py-3 px-5 border-b-2 text-sm transition-all flex items-center gap-2 rounded-t-xl">
            <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
            Lớp Giảng dạy
            <span class="ml-1 px-2 py-0.5 rounded-full text-[11px] font-extrabold bg-blue-100 text-blue-700 font-mono">{{ $teachingClasses->count() }}</span>
        </button>
    </div>

    <!-- Tab 1: Homeroom Classes -->
    <div x-show="activeTab === 'homeroom'">
        @if($homeroomClasses->isEmpty())
            <div class="bg-white/80 backdrop-blur-sm p-16 rounded-3xl border border-blue-100 shadow-[0_10px_35px_-10px_rgba(59,130,246,0.08)] text-center">
                <div class="w-20 h-20 bg-gradient-to-tr from-blue-50 to-indigo-50 text-blue-400 rounded-3xl flex items-center justify-center mx-auto mb-4 border border-blue-100">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
                <h3 class="text-lg font-extrabold text-[#0a2540] mb-2">Không làm chủ nhiệm lớp nào</h3>
                <p class="text-sm text-slate-500 max-w-sm mx-auto">Bạn hiện tại chưa làm giảng viên chủ nhiệm cho lớp học nào.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($homeroomClasses as $class)
                    <div class="bg-white/80 backdrop-blur-sm rounded-3xl border border-blue-100 shadow-[0_4px_20px_-4px_rgba(59,130,246,0.07)] hover:shadow-xl hover:shadow-blue-500/10 hover:border-blue-300 transition-all duration-300 p-6 flex flex-col justify-between group hover:-translate-y-1">
                        <div class="space-y-4">
                            <div class="flex justify-between items-center gap-2">
                                <span class="px-3 py-1 bg-blue-50 text-blue-700 font-extrabold text-xs rounded-xl border border-blue-100 font-mono flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path></svg>
                                    {{ $class->code }}
                                </span>
                                <span class="px-2.5 py-1 rounded-lg text-xs font-bold {{ $class->status == 'active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-rose-50 text-rose-700 border border-rose-100' }} flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $class->status == 'active' ? 'bg-emerald-500 animate-pulse' : 'bg-rose-400' }}"></span>
                                    {{ $class->status == 'active' ? 'Hoạt động' : 'Khóa' }}
                                </span>
                            </div>

                            <div class="flex items-start gap-3.5">
                                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white shrink-0 shadow-md shadow-blue-500/20 group-hover:scale-105 transition-transform duration-200">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                </div>
                                <div class="min-w-0">
                                    <h3 class="text-lg font-black text-[#0a2540] group-hover:text-blue-600 transition-colors leading-snug">{{ $class->name }}</h3>
                                    @if($class->course)
                                        <p class="text-xs text-blue-600 font-semibold mt-0.5">{{ $class->course->name }} ({{ $class->course->semester }})</p>
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

    <!-- Tab 2: Teaching Classes -->
    <div x-show="activeTab === 'teaching'" style="display: none;">
        @if($teachingClasses->isEmpty())
            <div class="bg-white/80 backdrop-blur-sm p-16 rounded-3xl border border-blue-100 shadow-[0_10px_35px_-10px_rgba(59,130,246,0.08)] text-center">
                <div class="w-20 h-20 bg-gradient-to-tr from-blue-50 to-indigo-50 text-blue-400 rounded-3xl flex items-center justify-center mx-auto mb-4 border border-blue-100">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                </div>
                <h3 class="text-lg font-extrabold text-[#0a2540] mb-2">Chưa có phân công bộ môn</h3>
                <p class="text-sm text-slate-500 max-w-sm mx-auto">Bạn chưa được phân công dạy môn học nào cho các lớp khác.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($teachingClasses as $class)
                    <div class="bg-white/80 backdrop-blur-sm rounded-3xl border border-blue-100 shadow-[0_4px_20px_-4px_rgba(59,130,246,0.07)] hover:shadow-xl hover:shadow-blue-500/10 hover:border-blue-300 transition-all duration-300 p-6 flex flex-col justify-between group hover:-translate-y-1">
                        <div class="space-y-4">
                            <div class="flex justify-between items-center gap-2">
                                <span class="px-3 py-1 bg-blue-50 text-blue-700 font-extrabold text-xs rounded-xl border border-blue-100 font-mono flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path></svg>
                                    {{ $class->code }}
                                </span>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    Giảng dạy
                                </span>
                            </div>

                            <div class="flex items-start gap-3.5">
                                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white shrink-0 shadow-md shadow-blue-500/20 group-hover:scale-105 transition-transform duration-200">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                </div>
                                <div class="min-w-0">
                                    <h3 class="text-lg font-black text-[#0a2540] group-hover:text-blue-600 transition-colors leading-snug">{{ $class->name }}</h3>
                                    @if($class->course)
                                        <p class="text-xs text-blue-600 font-semibold mt-0.5">{{ $class->course->name }} ({{ $class->course->semester }})</p>
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
                                class="px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold text-xs rounded-xl shadow-sm shadow-blue-500/20 hover:shadow-md hover:shadow-blue-500/30 transition-all inline-flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                Tài liệu & Lớp học
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>
@endsection
