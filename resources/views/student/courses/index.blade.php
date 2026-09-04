@extends('layouts.student')

@section('title', 'Khóa học & Môn học')

@section('content')
<div class="space-y-6" x-data="{ selectedCourse: null }">
    <!-- Top Header Bar with Credits & GPA Metrics -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200/80 pb-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Khóa học & Môn học</h1>
            <p class="text-xs text-slate-500 font-medium mt-0.5">Danh sách các chương trình đào tạo và môn học trong lộ trình học tập</p>
        </div>

        <!-- Right Top Banner Metrics -->
        <div class="flex items-center gap-3 shrink-0 flex-wrap sm:flex-nowrap">
            <!-- Metric 1: Completed Credits vs Required Credits (150 TC) -->
            <div class="bg-white rounded-xl px-4 py-2.5 border border-slate-200/80 shadow-2xs flex items-center gap-3 min-w-[200px]">
                <div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold shrink-0 border border-emerald-100">
                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between gap-1">
                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Đã tích lũy</span>
                        <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.2 rounded border border-emerald-100">{{ $progressPercent }}%</span>
                    </div>
                    <div class="flex items-baseline gap-1">
                        <span class="text-sm font-black text-slate-900 font-mono">{{ $completedCredits }}</span>
                        <span class="text-xs font-semibold text-slate-400 font-mono">/ {{ $requiredCredits }} TC</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-1.5 mt-1 overflow-hidden">
                        <div class="bg-emerald-500 h-1.5 rounded-full transition-all duration-500" style="width: {{ $progressPercent }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Metric 2: Current Semester Credits -->
            <div class="bg-white rounded-xl px-4 py-2.5 border border-slate-200/80 shadow-2xs flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold shrink-0 border border-blue-100">
                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477-4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                </div>
                <div>
                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Kỳ này học</span>
                    <span class="text-sm font-black text-blue-600 font-mono">{{ $totalCredits ?? 0 }} TC</span>
                </div>
            </div>

            <!-- Metric 3: GPA -->
            <div class="bg-white rounded-xl px-4 py-2.5 border border-slate-200/80 shadow-2xs flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold shrink-0 border border-indigo-100">
                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path></svg>
                </div>
                <div>
                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">GPA tích lũy</span>
                    <span class="text-sm font-black text-slate-800">3.65 / 4.0</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-2xs">
        <form method="GET" action="{{ route('student.khoa-hoc.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="sm:col-span-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="🔍 Tìm kiếm khóa học, môn học..." 
                    class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200/80 rounded-xl text-xs font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all">
            </div>
            <div>
                <select name="semester" onchange="this.form.submit()" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200/80 rounded-xl text-xs font-medium text-slate-800 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all">
                    <option value="">-- Tất cả học kỳ --</option>
                    <option value="Học kỳ 1" {{ request('semester') == 'Học kỳ 1' ? 'selected' : '' }}>Học kỳ 1</option>
                    <option value="Học kỳ 2" {{ request('semester') == 'Học kỳ 2' ? 'selected' : '' }}>Học kỳ 2</option>
                    <option value="Học kỳ 3" {{ request('semester') == 'Học kỳ 3' ? 'selected' : '' }}>Học kỳ 3</option>
                </select>
            </div>
        </form>
    </div>

    <!-- Courses Grid -->
    @if($courses->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($courses as $course)
                <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-2xs hover:shadow-md transition-all flex flex-col justify-between group">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="px-2.5 py-1 bg-blue-50 text-blue-700 font-bold text-xs rounded-lg border border-blue-100">
                                {{ $course->semester }} - {{ $course->academic_year }}
                            </span>
                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-100">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                Đang học
                            </span>
                        </div>

                        <div>
                            <h2 class="text-base font-bold text-slate-900 group-hover:text-blue-600 transition-colors leading-snug">
                                {{ $course->name }}
                            </h2>
                            @if($course->description)
                                <p class="text-xs text-slate-500 mt-1 line-clamp-2">{{ $course->description }}</p>
                            @endif
                        </div>

                        <!-- Subjects preview -->
                        @if($course->subjects->count() > 0)
                            <div class="space-y-1.5">
                                <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block">Các môn học trong khóa:</span>
                                <div class="flex flex-wrap gap-1">
                                    @foreach($course->subjects->take(3) as $subj)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 text-slate-700">
                                            {{ $subj->name }}
                                        </span>
                                    @endforeach
                                    @if($course->subjects->count() > 3)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-blue-50 text-blue-600 font-bold">
                                            +{{ $course->subjects->count() - 3 }} môn khác
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-between">
                            <span class="text-xs font-semibold text-slate-500">Khối lượng học phần</span>
                            <div class="flex items-center gap-1.5">
                                <span class="text-xs font-bold text-slate-700 bg-white px-2 py-0.5 rounded border border-slate-200">{{ $course->subjects->count() }} môn</span>
                                <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded border border-blue-100 font-mono">{{ $course->total_credits }} TC</span>
                            </div>
                        </div>
                    </div>

                    <div class="pt-5">
                        <button type="button" @click="selectedCourse = {{ json_encode($course) }}" 
                            class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-bold text-xs rounded-xl shadow-2xs transition-all flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            Xem chi tiết các môn học
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white rounded-2xl border border-slate-200/80 p-12 text-center shadow-2xs">
            <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-3 border border-blue-100">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477-4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
            </div>
            <h3 class="text-base font-bold text-slate-900 mb-1">Chưa có khóa học nào</h3>
            <p class="text-xs text-slate-500">Hiện tại chưa có thông tin khóa học mới.</p>
        </div>
    @endif

    <!-- Subjects Modal -->
    <template x-teleport="body">
        <div x-show="selectedCourse !== null" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" @click="selectedCourse = null"></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative bg-white rounded-2xl p-6 sm:p-8 max-w-xl w-full shadow-xl text-left border border-slate-200/80">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-5">
                        <div>
                            <span class="text-xs font-bold text-blue-600 uppercase tracking-wider block" x-text="selectedCourse?.semester + ' (' + selectedCourse?.academic_year + ')'"></span>
                            <h3 class="text-lg font-bold text-slate-900" x-text="selectedCourse?.name"></h3>
                        </div>
                        <button type="button" @click="selectedCourse = null" class="p-1.5 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <div class="space-y-2.5 max-h-80 overflow-y-auto pr-1">
                        <template x-for="subject in selectedCourse?.subjects" :key="subject.id">
                            <div class="p-3.5 rounded-xl border border-slate-100 bg-slate-50/80 flex items-center justify-between">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h4 class="font-bold text-slate-900 text-xs" x-text="subject.name"></h4>
                                        <span class="px-2 py-0.5 bg-blue-50 text-blue-700 border border-blue-200/60 rounded-md font-mono font-bold text-[10px]" x-text="(subject.credits || 3) + ' TC'"></span>
                                    </div>
                                    <span class="text-[11px] font-mono text-slate-400" x-text="'Mã môn: ' + subject.code"></span>
                                </div>
                                <span class="px-2.5 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-200/60 rounded-full font-bold text-[11px]">
                                    Đang học
                                </span>
                            </div>
                        </template>
                    </div>

                    <div class="mt-6 pt-4 border-t border-slate-100 text-right">
                        <button type="button" @click="selectedCourse = null" class="px-5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition-colors">
                            Đóng
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>
@endsection
