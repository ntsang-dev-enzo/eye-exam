@extends('layouts.student')

@section('title', 'Khóa học & Môn học')

@section('content')
<div class="space-y-6 sm:space-y-8" x-data="{ selectedCourse: null }">

    <!-- 1. Top Header Bar: Large Navy Title + Top Right Stat Cards -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 pb-2">
        <!-- Left: Large Title & Short Description -->
        <div class="space-y-1.5 max-w-xl">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 border border-blue-100 text-blue-700 text-xs font-bold tracking-wide uppercase">
                <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
                Chương trình đào tạo
            </div>
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black text-[#0a2540] tracking-tight">
                Khóa học & Môn học
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 font-medium leading-relaxed">
                Theo dõi tiến độ tích lũy tín chỉ, học phần trong kỳ và lộ trình hoàn thành chương trình học.
            </p>
        </div>

        <!-- Right: 3 Prominent Stat Cards (Tín chỉ tích lũy, Tín chỉ đang học, GPA) -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5 sm:gap-4 shrink-0 w-full lg:w-auto">
            
            <!-- Card 1: Số tín chỉ đã tích lũy -->
            <div class="bg-white rounded-2xl sm:rounded-3xl p-4 sm:p-4.5 border border-slate-200/80 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] hover:shadow-md hover:border-blue-200 transition-all duration-200 flex items-center gap-3.5 min-w-[200px] group">
                <div class="w-11 h-11 rounded-2xl bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center shrink-0 shadow-xs group-hover:scale-105 group-hover:bg-blue-600 group-hover:text-white transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between gap-1 mb-0.5">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Đã tích lũy</span>
                        <span class="text-[10px] font-extrabold text-blue-700 bg-blue-50 px-1.5 py-0.5 rounded-md border border-blue-100 font-mono">{{ $progressPercent }}%</span>
                    </div>
                    <div class="flex items-baseline gap-1">
                        <span class="text-lg sm:text-xl font-black text-slate-900 font-mono">{{ $completedCredits }}</span>
                        <span class="text-xs font-bold text-slate-400 font-mono">/ {{ $requiredCredits }} TC</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-1.5 mt-1.5 overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 h-1.5 rounded-full transition-all duration-500" style="width: {{ $progressPercent }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Card 2: Tín chỉ đang học -->
            <div class="bg-white rounded-2xl sm:rounded-3xl p-4 sm:p-4.5 border border-slate-200/80 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] hover:shadow-md hover:border-blue-200 transition-all duration-200 flex items-center gap-3.5 min-w-[175px] group">
                <div class="w-11 h-11 rounded-2xl bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center shrink-0 shadow-xs group-hover:scale-105 group-hover:bg-blue-600 group-hover:text-white transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-0.5">Tín chỉ đang học</span>
                    <div class="flex items-baseline gap-1">
                        <span class="text-lg sm:text-xl font-black text-blue-600 font-mono">{{ $totalCredits ?? 0 }}</span>
                        <span class="text-xs font-bold text-blue-500 font-mono">TC</span>
                    </div>
                    <span class="text-[11px] font-semibold text-emerald-600 mt-0.5 inline-flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        Kỳ hiện tại
                    </span>
                </div>
            </div>

            <!-- Card 3: GPA tích lũy -->
            <div class="bg-white rounded-2xl sm:rounded-3xl p-4 sm:p-4.5 border border-slate-200/80 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] hover:shadow-md hover:border-blue-200 transition-all duration-200 flex items-center gap-3.5 min-w-[175px] group">
                <div class="w-11 h-11 rounded-2xl bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center shrink-0 shadow-xs group-hover:scale-105 group-hover:bg-blue-600 group-hover:text-white transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-0.5">GPA tích lũy</span>
                    <div class="flex items-baseline gap-1">
                        <span class="text-lg sm:text-xl font-black text-[#0a2540] font-mono">{{ number_format($gpa ?? 3.65, 2) }}</span>
                        <span class="text-xs font-bold text-slate-400 font-mono">/ 4.0</span>
                    </div>
                    <span class="inline-flex items-center px-2 py-0.2 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-100 mt-0.5">
                        Xếp loại Giỏi
                    </span>
                </div>
            </div>

        </div>
    </div>

    <!-- 2. Search & Filter Area: Modern Rounded with Color Changing Interaction -->
    <div class="bg-white/80 backdrop-blur-sm rounded-2xl sm:rounded-3xl border border-blue-100 p-4 sm:p-5 shadow-[0_4px_20px_-4px_rgba(59,130,246,0.08)] transition-all">
        <form method="GET" action="{{ route('student.khoa-hoc.index') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3.5">
            
            <!-- Search Input with Interactive Color Changing -->
            <div class="relative flex-1 group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-blue-600 transition-colors duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" 
                    placeholder="Tìm kiếm khóa học, tên môn học, mã học phần..." 
                    class="w-full pl-11 pr-4 py-3 bg-slate-50/70 hover:bg-blue-50/30 focus:bg-white border border-slate-200/90 hover:border-blue-300 focus:border-blue-600 rounded-2xl text-xs sm:text-sm font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-blue-100 transition-all duration-200">
            </div>

            <!-- Semester Select Dropdown with Interactive Color Changing -->
            <div class="relative sm:w-64 group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-blue-600 transition-colors duration-200 z-10">
                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <select name="semester" onchange="this.form.submit()"
                    class="w-full pl-11 pr-10 py-3 bg-slate-50/70 hover:bg-blue-50/30 focus:bg-white border border-slate-200/90 hover:border-blue-300 focus:border-blue-600 rounded-2xl text-xs sm:text-sm font-semibold text-slate-700 focus:outline-none focus:ring-4 focus:ring-blue-100 transition-all duration-200 appearance-none cursor-pointer">
                    <option value="">-- Tất cả học kỳ --</option>
                    <option value="Học kỳ 1" {{ request('semester') == 'Học kỳ 1' ? 'selected' : '' }}>Học kỳ 1</option>
                    <option value="Học kỳ 2" {{ request('semester') == 'Học kỳ 2' ? 'selected' : '' }}>Học kỳ 2</option>
                    <option value="Học kỳ 3" {{ request('semester') == 'Học kỳ 3' ? 'selected' : '' }}>Học kỳ 3</option>
                </select>
                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-400 group-hover:text-blue-600 transition-colors z-10">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>
            </div>

            <!-- Submit Button with Hover & Shadow -->
            <button type="submit" class="px-5 py-3 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-bold text-xs sm:text-sm rounded-2xl shadow-md shadow-blue-500/25 hover:shadow-lg hover:shadow-blue-500/40 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200 flex items-center justify-center gap-2 shrink-0 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                </svg>
                <span>Lọc</span>
            </button>

            <!-- Reset Filter Button if applied -->
            @if(request('search') || request('semester'))
                <a href="{{ route('student.khoa-hoc.index') }}" 
                    class="px-4 py-3 bg-slate-100 hover:bg-rose-50 text-slate-600 hover:text-rose-600 font-bold text-xs sm:text-sm rounded-2xl border border-slate-200 hover:border-rose-200 transition-all duration-200 flex items-center justify-center gap-1.5 shrink-0" 
                    title="Xóa bộ lọc">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    <span>Đặt lại</span>
                </a>
            @endif

        </form>
    </div>

    <!-- 3. Main Content: Course List inside a Large White Frame -->
    <div class="bg-white/80 backdrop-blur-sm rounded-3xl border border-blue-100 p-6 sm:p-8 shadow-[0_10px_35px_-10px_rgba(59,130,246,0.08)] space-y-6">
        
        <!-- Frame Subheader -->
        <div class="flex items-center justify-between border-b border-slate-100 pb-5">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-extrabold text-[#0a2540] tracking-tight">
                        Danh sách khóa học
                    </h2>
                    <p class="text-xs text-slate-400 font-medium">Các học phần được phân bổ theo lộ trình đào tạo của bạn</p>
                </div>
            </div>

            <!-- Course Count Badge -->
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-100 shadow-2xs font-mono">
                {{ $courses->count() }} khóa học
            </span>
        </div>

        <!-- Course Cards Grid OR Empty State -->
        @if($courses->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                @foreach($courses as $course)
                    <div class="bg-gradient-to-b from-white via-white to-blue-50/20 rounded-2xl sm:rounded-3xl border border-slate-200/80 p-5 sm:p-6 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.03)] hover:shadow-xl hover:shadow-blue-500/10 hover:border-blue-300 transition-all duration-300 flex flex-col justify-between group hover:-translate-y-1">
                        
                        <div class="space-y-4">
                            <!-- Card Header: Semester Tag & Active Status -->
                            <div class="flex items-center justify-between gap-2">
                                <span class="px-3 py-1 bg-blue-50 text-blue-700 font-bold text-xs rounded-xl border border-blue-100 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    {{ $course->semester }} - {{ $course->academic_year }}
                                </span>
                                <span class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    Đang học
                                </span>
                            </div>

                            <!-- Course Title & Description -->
                            <div>
                                <h3 class="text-base sm:text-lg font-black text-[#0a2540] group-hover:text-blue-600 transition-colors leading-snug">
                                    {{ $course->name }}
                                </h3>
                                @if($course->description)
                                    <p class="text-xs text-slate-500 mt-1.5 line-clamp-2 leading-relaxed font-medium">
                                        {{ $course->description }}
                                    </p>
                                @endif
                            </div>

                            <!-- Included Subjects Chips Preview -->
                            @if($course->subjects->count() > 0)
                                <div class="space-y-2 pt-1">
                                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">
                                        Các môn học trong khóa:
                                    </span>
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach($course->subjects->take(3) as $subj)
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-slate-100/90 text-slate-700 border border-slate-200/60 group-hover:border-blue-200 transition-colors">
                                                {{ $subj->name }}
                                            </span>
                                        @endforeach
                                        @if($course->subjects->count() > 3)
                                            <span class="inline-flex items-center px-2 py-1 rounded-lg text-[11px] font-bold bg-blue-50 text-blue-600 border border-blue-100">
                                                +{{ $course->subjects->count() - 3 }} môn khác
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <!-- Metrics Box: Subject count & Total Credits -->
                            <div class="p-3.5 bg-slate-50/90 rounded-2xl border border-slate-100 flex items-center justify-between">
                                <span class="text-xs font-semibold text-slate-500 flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                    Khối lượng học phần
                                </span>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-bold text-slate-700 bg-white px-2.5 py-0.5 rounded-lg border border-slate-200 shadow-2xs">
                                        {{ $course->subjects->count() }} môn
                                    </span>
                                    <span class="text-xs font-extrabold text-blue-600 bg-blue-50 px-2.5 py-0.5 rounded-lg border border-blue-100 font-mono shadow-2xs">
                                        {{ $course->total_credits }} TC
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Card Action Button -->
                        <div class="pt-5">
                            <button type="button" @click="selectedCourse = {{ json_encode($course) }}" 
                                class="w-full py-3 px-4 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 active:from-blue-800 active:to-indigo-800 text-white font-bold text-xs sm:text-sm rounded-xl sm:rounded-2xl shadow-md shadow-blue-500/20 hover:shadow-lg hover:shadow-blue-500/35 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200 flex items-center justify-center gap-2 cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                <span>Xem chi tiết các môn học</span>
                            </button>
                        </div>

                    </div>
                @endforeach
            </div>
        @else
            <!-- Empty State: Course Icon + “Chưa có khóa học nào” -->
            <div class="py-16 px-6 text-center">
                <div class="w-20 h-20 bg-gradient-to-tr from-blue-50 to-indigo-50 text-blue-600 rounded-3xl flex items-center justify-center mx-auto mb-4 border border-blue-100 shadow-sm transition-transform duration-300 hover:scale-105">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477-4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <h3 class="text-lg sm:text-xl font-extrabold text-[#0a2540] mb-2">
                    Chưa có khóa học nào
                </h3>
                <p class="text-xs sm:text-sm text-slate-500 max-w-md mx-auto leading-relaxed">
                    Hiện tại chưa có thông tin khóa học nào phù hợp với bộ lọc hoặc bạn chưa được ghi danh vào khóa học trong học kỳ này.
                </p>

                @if(request('search') || request('semester'))
                    <div class="mt-5">
                        <a href="{{ route('student.khoa-hoc.index') }}" 
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200 text-xs font-bold transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Xóa bộ lọc tìm kiếm
                        </a>
                    </div>
                @endif
            </div>
        @endif

    </div>

    <!-- 4. Subjects Detail Modal with Modern High-End Layout -->
    <template x-teleport="body">
        <div x-show="selectedCourse !== null" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" 
                @click="selectedCourse = null"
                x-show="selectedCourse !== null"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0">
            </div>

            <!-- Modal Container -->
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative bg-white rounded-3xl p-6 sm:p-8 max-w-2xl w-full shadow-2xl text-left border border-slate-200/80 transition-all transform"
                    x-show="selectedCourse !== null"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95">
                    
                    <!-- Modal Header -->
                    <div class="flex items-start justify-between border-b border-slate-100 pb-5 mb-5">
                        <div class="space-y-1">
                            <span class="text-xs font-bold text-blue-600 uppercase tracking-wider block" 
                                x-text="selectedCourse?.semester + ' (' + selectedCourse?.academic_year + ')'"></span>
                            <h3 class="text-xl font-black text-[#0a2540]" x-text="selectedCourse?.name"></h3>
                            <p class="text-xs text-slate-500 font-medium" x-text="selectedCourse?.description || 'Danh sách các học phần trực thuộc khóa học'"></p>
                        </div>
                        <button type="button" @click="selectedCourse = null" 
                            class="p-2 text-slate-400 hover:text-slate-600 rounded-xl hover:bg-slate-100 transition-colors"
                            title="Đóng">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Subjects List -->
                    <div class="space-y-3 max-h-96 overflow-y-auto pr-1">
                        <template x-for="(subject, idx) in selectedCourse?.subjects" :key="subject.id">
                            <div class="p-4 rounded-2xl border border-slate-100 bg-slate-50/70 hover:bg-blue-50/40 hover:border-blue-200 transition-all duration-200 flex items-center justify-between group">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <span class="text-[11px] font-mono font-bold text-slate-400 bg-white px-2 py-0.5 rounded-md border border-slate-200" x-text="'#' + (idx + 1)"></span>
                                        <h4 class="font-bold text-slate-900 text-sm group-hover:text-blue-600 transition-colors" x-text="subject.name"></h4>
                                        <span class="px-2.5 py-0.5 bg-blue-50 text-blue-700 border border-blue-200/70 rounded-md font-mono font-bold text-[11px]" x-text="(subject.credits || 3) + ' TC'"></span>
                                    </div>
                                    <div class="flex items-center gap-3 text-xs text-slate-500">
                                        <span class="font-mono" x-text="'Mã học phần: ' + subject.code"></span>
                                        <span class="text-slate-300">•</span>
                                        <span x-text="subject.department || 'Bộ môn Chuyên ngành'"></span>
                                    </div>
                                </div>
                                <span class="px-3 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200/80 rounded-full font-bold text-xs shrink-0 flex items-center gap-1.5 shadow-2xs">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Đang học
                                </span>
                            </div>
                        </template>

                        <template x-if="!selectedCourse?.subjects || selectedCourse?.subjects.length === 0">
                            <div class="text-center py-8 text-slate-400 text-xs">
                                Khóa học này chưa có môn học nào được gán.
                            </div>
                        </template>
                    </div>

                    <!-- Modal Footer -->
                    <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-500" x-text="'Tổng số: ' + (selectedCourse?.subjects?.length || 0) + ' môn học (' + (selectedCourse?.total_credits || 0) + ' tín chỉ)'"></span>
                        <button type="button" @click="selectedCourse = null" 
                            class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 active:bg-slate-300 text-slate-700 font-bold text-xs rounded-xl transition-colors cursor-pointer">
                            Đóng cửa sổ
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </template>

</div>
@endsection
