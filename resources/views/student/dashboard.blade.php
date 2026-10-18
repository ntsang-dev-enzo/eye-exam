@extends('layouts.student')

@section('title', 'Bảng điều khiển Sinh viên')

@section('content')
<div class="max-w-6xl mx-auto space-y-6 pb-16">

    <!-- 1. Header & Welcome Banner -->
    <div class="bg-gradient-to-r from-blue-700 via-blue-600 to-slate-900 rounded-2xl p-6 sm:p-8 text-white relative overflow-hidden shadow-xs border border-blue-700/50">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 border border-white/15 text-xs font-semibold text-blue-100 mb-3">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Cổng thi trực tuyến EyeExam
                </div>
                <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight mb-2">Xin chào, {{ auth()->user()->name }}!</h1>
                <div class="flex items-center gap-2 sm:gap-3 flex-wrap text-xs sm:text-sm text-blue-100/90 mt-2">
                    <span class="inline-flex items-center gap-1.5 bg-white/10 px-2.5 py-1 rounded-lg border border-white/15">
                        <span class="text-blue-200">Mã SV:</span>
                        <strong class="text-white font-mono font-bold">{{ auth()->user()->code }}</strong>
                    </span>
                    <span class="inline-flex items-center gap-1.5 bg-white/10 px-2.5 py-1 rounded-lg border border-white/15">
                        <svg class="w-3.5 h-3.5 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        <span class="text-blue-200">Lớp:</span>
                        <strong class="text-white font-bold">{{ auth()->user()->class_name ?? 'Chưa phân lớp' }}</strong>
                    </span>
                    <span class="inline-flex items-center gap-1.5 bg-white/10 px-2.5 py-1 rounded-lg border border-white/15">
                        <svg class="w-3.5 h-3.5 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        <span class="text-blue-200">Chuyên ngành:</span>
                        <strong class="text-white font-bold">{{ auth()->user()->department_name }}</strong>
                    </span>
                </div>
            </div>

            <div class="shrink-0 flex items-center gap-3">
                <a href="{{ route('student.exams.index') }}" class="px-4 py-2.5 rounded-xl bg-white hover:bg-blue-50 text-blue-900 font-bold text-xs transition-all shadow-xs active:scale-95 flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    Tất cả kỳ thi
                </a>
            </div>
        </div>
    </div>

    @if(!auth()->user()->face_registered)
    <div class="bg-amber-50 border border-amber-200/80 rounded-2xl p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-2xs">
        <div class="flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center shrink-0 shadow-xs">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
            <div>
                <h4 class="font-bold text-amber-900 text-sm">Tài khoản chưa cập nhật nhận diện khuôn mặt (Face ID)</h4>
                <p class="text-xs text-amber-700 mt-0.5">Hệ thống quy định thí sinh bắt buộc phải có dữ liệu khuôn mặt mới được tham gia thi. Vui lòng dành 1 phút cập nhật khuôn mặt qua webcam.</p>
            </div>
        </div>
        <a href="{{ route('student.face.register') }}" class="shrink-0 px-4 py-2.5 bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold rounded-xl shadow-xs transition-all flex items-center justify-center gap-2 active:scale-95">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span>Cập nhật khuôn mặt ngay</span>
        </a>
    </div>
    @endif

    <!-- 2. Summary Statistics Grid (3 columns - đã bỏ mục điểm trung bình) -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <!-- Card 1: Bài thi được giao -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-2xs flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-[11px] text-slate-500 font-semibold uppercase tracking-wider">Bài thi được giao</p>
                <p class="text-2xl font-extrabold text-slate-900">{{ $stats['total_assigned'] }}</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
        </div>

        <!-- Card 2: Lượt hoàn thành -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-2xs flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-[11px] text-slate-500 font-semibold uppercase tracking-wider">Lượt hoàn thành</p>
                <p class="text-2xl font-extrabold text-emerald-600">{{ $stats['total_submitted'] }}</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>

        <!-- Card 3: Đang làm dở -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-2xs flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-[11px] text-slate-500 font-semibold uppercase tracking-wider">Đang làm dở</p>
                <p class="text-2xl font-extrabold text-amber-600">{{ $stats['in_progress_count'] }}</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>
    </div>

    <!-- 3. Section: Danh Sách Kỳ Thi & Bộ Lọc Theo Kỳ / Môn Học -->
    <div class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-200/80 pb-3">
            <div>
                <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    Kỳ thi & Bài thi được giao
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Lọc nhanh theo học kỳ và môn học trong kỳ để tìm kiếm bài thi chính xác</p>
            </div>
        </div>

        <!-- Filter Card: Kỳ & Môn học trong kỳ & Tìm kiếm -->
        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-2xs space-y-3">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-end">
                <!-- Dropdown Học kỳ -->
                <div class="lg:col-span-4">
                    <label for="filterSemester" class="block text-xs font-bold text-slate-700 mb-1 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        Học kỳ
                    </label>
                    <div class="relative">
                        <select id="filterSemester" onchange="onSemesterFilterChange()" class="w-full text-xs font-semibold rounded-xl border border-slate-300 bg-slate-50/50 py-2.5 pl-3 pr-8 text-slate-800 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100 transition-all appearance-none cursor-pointer">
                            <option value="">-- Tất cả học kỳ --</option>
                            @foreach($semesters as $sem)
                                <option value="{{ $sem }}" {{ ($selectedSemester ?? '') === $sem ? 'selected' : '' }}>{{ $sem }}</option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2.5 text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>

                <!-- Dropdown Môn học trong kỳ -->
                <div class="lg:col-span-4">
                    <label for="filterSubject" class="block text-xs font-bold text-slate-700 mb-1 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        Môn học trong kỳ
                    </label>
                    <div class="relative">
                        <select id="filterSubject" onchange="applyClientExamFilters()" class="w-full text-xs font-semibold rounded-xl border border-slate-300 bg-slate-50/50 py-2.5 pl-3 pr-8 text-slate-800 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100 transition-all appearance-none cursor-pointer">
                            <option value="">-- Tất cả môn học --</option>
                            @foreach($subjectsWithSemester as $subj)
                                <option value="{{ $subj['id'] }}" data-semester="{{ $subj['semester'] }}" {{ ((string)($selectedSubjectId ?? '')) === (string)$subj['id'] ? 'selected' : '' }}>
                                    {{ $subj['name'] }} ({{ $subj['code'] }})
                                </option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2.5 text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>

                <!-- Tìm kiếm theo tên / mã -->
                <div class="lg:col-span-3">
                    <label for="filterSearch" class="block text-xs font-bold text-slate-700 mb-1 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        Tìm kiếm bài thi
                    </label>
                    <input type="text" id="filterSearch" oninput="applyClientExamFilters()" placeholder="Tên bài thi hoặc mã đề..." class="w-full text-xs font-medium rounded-xl border border-slate-300 bg-slate-50/50 py-2.5 px-3 text-slate-800 placeholder-slate-400 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100 transition-all">
                </div>

                <!-- Nút đặt lại -->
                <div class="lg:col-span-1">
                    <button type="button" onclick="resetAllExamFilters()" class="w-full px-3 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-1 active:scale-95" title="Đặt lại bộ lọc">
                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                        <span>Đặt lại</span>
                    </button>
                </div>
            </div>

            <!-- Active filter status -->
            <div class="flex items-center justify-between text-xs text-slate-500 pt-2 border-t border-slate-100">
                <span>Hiển thị <strong id="visibleExamCount" class="text-blue-600 font-bold">{{ $assignedExams->count() }}</strong> / {{ $assignedExams->count() }} bài thi</span>
                <span id="activeFilterDesc" class="text-slate-400 italic">Đang hiển thị tất cả</span>
            </div>
        </div>

        @if($assignedExams->isEmpty())
            <div class="bg-white rounded-2xl p-12 text-center border border-slate-200/80 shadow-2xs">
                <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-slate-50 mb-3 text-slate-400">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <h3 class="text-base font-bold text-slate-900 mb-1">Chưa có bài thi nào được giao</h3>
                <p class="text-xs text-slate-500">Giảng viên chưa phân công bài thi nào cho bạn vào thời điểm này.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="examsContainer">
                <!-- Empty state khi lọc không thấy -->
                <div id="filterEmptyState" class="hidden col-span-full bg-white rounded-2xl p-10 text-center border border-slate-200/80 shadow-2xs">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-amber-50 mb-2.5 text-amber-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900 mb-1">Không tìm thấy bài thi phù hợp</h3>
                    <p class="text-xs text-slate-500 max-w-sm mx-auto mb-3">Không có bài thi nào khớp với kỳ học hoặc môn học bạn đã chọn.</p>
                    <button type="button" onclick="resetAllExamFilters()" class="px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-xs transition-all inline-flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                        Xóa bộ lọc
                    </button>
                </div>

                @foreach($assignedExams as $exam)
                    <div class="exam-item-card bg-white rounded-2xl border border-slate-200/80 shadow-2xs hover:shadow-md transition-all flex flex-col justify-between overflow-hidden relative group"
                         data-semester="{{ $exam->semester ?? '' }}"
                         data-subject-id="{{ $exam->subject_id }}"
                         data-title="{{ strtolower($exam->title) }}"
                         data-code="{{ strtolower($exam->code) }}">
                        <!-- Accent Top Border -->
                        <div class="h-1.5 w-full bg-{{ $exam->status_color }}-500"></div>

                        <div class="p-5 flex-1 flex flex-col justify-between space-y-4">
                            <!-- Header tags -->
                            <div>
                                <div class="flex items-center justify-between gap-2 mb-2.5">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-{{ $exam->status_color }}-50 text-{{ $exam->status_color }}-700 border border-{{ $exam->status_color }}-200/60">
                                        {{ $exam->calculated_status }}
                                    </span>
                                    <span class="text-[11px] font-mono font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-md">#{{ $exam->code }}</span>
                                </div>

                                <h3 class="text-base font-bold text-slate-900 line-clamp-2 group-hover:text-blue-600 transition-colors">
                                    {{ $exam->title }}
                                </h3>

                                <div class="flex items-center gap-1.5 mt-2.5 flex-wrap">
                                    @if(!empty($exam->semester))
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                            <svg class="w-3 h-3 mr-1 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            {{ $exam->semester }}
                                        </span>
                                    @endif
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-100">
                                        {{ $exam->subject->name ?? 'Môn học' }}
                                    </span>
                                    @if($exam->category)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                            {{ $exam->category->name }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Meta details (bg-slate-50 rounded-xl p-3) -->
                            <div class="grid grid-cols-2 gap-2 text-xs bg-slate-50 rounded-xl p-3 border border-slate-100">
                                <div>
                                    <span class="text-slate-400 block text-[10px] font-medium uppercase tracking-wider">Thời gian:</span>
                                    <strong class="text-slate-700 font-bold">{{ $exam->duration_minutes }} phút</strong>
                                </div>
                                <div>
                                    <span class="text-slate-400 block text-[10px] font-medium uppercase tracking-wider">Số câu hỏi:</span>
                                    <strong class="text-slate-700 font-bold">{{ $exam->total_questions }} câu</strong>
                                </div>
                            </div>

                            @if($exam->submitted_count > 0)
                                <div class="p-2.5 bg-emerald-50 rounded-xl border border-emerald-100 flex items-center justify-between text-xs">
                                    <span class="text-emerald-800 font-semibold">Điểm cao nhất:</span>
                                    <span class="font-black text-sm text-emerald-700">{{ $exam->score !== null ? $exam->score . ' đ' : 'Chưa chấm' }}</span>
                                </div>
                            @endif

                            <!-- Action Buttons -->
                            <div class="pt-2 space-y-2">
                                @if(!auth()->user()->face_registered)
                                    <a href="{{ route('student.face.register') }}" class="w-full py-2.5 px-4 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white text-xs font-bold rounded-xl shadow-xs transition-all flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                        <span>Cần cập nhật khuôn mặt để thi</span>
                                    </a>
                                @elseif($exam->has_in_progress)
                                    <a href="{{ route('student.exams.take', $exam) }}" class="w-full py-2.5 px-4 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold rounded-xl shadow-2xs transition-all flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        Tiếp tục làm bài
                                    </a>
                                @elseif($exam->can_take)
                                    <form action="{{ route('student.exams.join') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="code" value="{{ $exam->code }}">
                                        <button type="submit" class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-2xs transition-all flex items-center justify-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                            {{ $exam->submitted_count > 0 ? 'Thi lại (Lần ' . ($exam->submitted_count + 1) . ')' : 'Làm bài ngay' }}
                                        </button>
                                    </form>
                                @elseif($exam->calculated_status === 'Sắp thi')
                                    <button disabled class="w-full py-2.5 px-4 bg-slate-100 text-slate-400 text-xs font-bold rounded-xl cursor-not-allowed text-center border border-slate-200/80">
                                        Chưa đến giờ thi
                                    </button>
                                @elseif($exam->calculated_status === 'Đã hết hạn')
                                    <button disabled class="w-full py-2.5 px-4 bg-slate-100 text-slate-400 text-xs font-bold rounded-xl cursor-not-allowed text-center border border-slate-200/80">
                                        Kỳ thi đã kết thúc
                                    </button>
                                @else
                                    <button disabled class="w-full py-2.5 px-4 bg-slate-100 text-slate-400 text-xs font-bold rounded-xl cursor-not-allowed text-center border border-slate-200/80">
                                        Đã hết lượt làm bài
                                    </button>
                                @endif

                                <!-- Review Button -->
                                @if($exam->can_review)
                                    <a href="{{ route('student.exams.review', $exam) }}" class="w-full py-2 px-3 bg-white hover:bg-slate-50 text-blue-700 text-xs font-bold rounded-xl border border-blue-200 transition-colors flex items-center justify-center gap-1.5 shadow-2xs">
                                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        Xem lại kết quả & bài làm
                                    </a>
                                @elseif($exam->submitted_count > 0 && !$exam->allow_review)
                                    <p class="text-[11px] text-slate-400 text-center italic">Giảng viên không mở xem lại bài thi này.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- 4. Section: Lịch Sử Bài Thi Gần Đây -->
    <div class="space-y-4 pt-6">
        <div class="flex items-center justify-between border-b border-slate-200/80 pb-3">
            <div>
                <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Lịch sử làm bài thi gần đây
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Danh sách các phiên làm bài thi bạn đã tham gia</p>
            </div>
        </div>

        @if($attempts->isEmpty())
            <div class="bg-white rounded-2xl p-8 text-center border border-slate-200/80 shadow-2xs text-slate-400">
                <p class="text-xs font-medium">Chưa có lịch sử làm bài nào.</p>
            </div>
        @else
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50/80 text-slate-600 font-bold uppercase tracking-wider border-b border-slate-200/80">
                            <tr>
                                <th class="py-3.5 px-4">Lượt thi</th>
                                <th class="py-3.5 px-4">Đề thi / Môn học</th>
                                <th class="py-3.5 px-4">Thời gian nộp</th>
                                <th class="py-3.5 px-4">Trạng thái</th>
                                <th class="py-3.5 px-4 text-center">Điểm số</th>
                                <th class="py-3.5 px-4 text-right">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                            @foreach($attempts as $attempt)
                                <tr class="hover:bg-slate-50/60 transition-colors">
                                    <td class="py-3.5 px-4 font-mono font-bold text-blue-600">
                                        #{{ $attempt->id }}
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <p class="font-bold text-slate-900 line-clamp-1">{{ $attempt->exam->title }}</p>
                                        <div class="flex items-center gap-1.5 mt-0.5">
                                            <span class="text-[11px] text-slate-500">{{ $attempt->exam->subject->name ?? 'Môn học' }}</span>
                                            @if($attempt->exam->category)
                                                <span class="px-1.5 py-0.5 rounded text-[10px] bg-blue-50 text-blue-700 border border-blue-100">
                                                    {{ $attempt->exam->category->name }}
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-600">
                                        {{ $attempt->submitted_at ? $attempt->submitted_at->format('d/m/Y H:i') : ($attempt->started_at ? $attempt->started_at->format('d/m/Y H:i') : '--') }}
                                    </td>
                                    <td class="py-3.5 px-4">
                                        @if($attempt->status === 'submitted')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                                                Đã nộp bài
                                            </span>
                                        @elseif($attempt->status === 'in_progress')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200/60">
                                                Đang làm dở
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-200/60">
                                                Vi phạm / Khóa
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-center font-black text-sm">
                                        @if($attempt->score_value !== null)
                                            <span class="{{ $attempt->score_value >= 5 ? 'text-emerald-600' : 'text-rose-600' }}">
                                                {{ $attempt->score_value }} đ
                                            </span>
                                        @else
                                            <span class="text-slate-400 font-normal">--</span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            @if($attempt->status === 'in_progress')
                                                <a href="{{ route('student.exams.take', $attempt->exam) }}" class="px-3 py-1 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-lg transition-all text-xs">
                                                    Tiếp tục
                                                </a>
                                            @else
                                                @if($attempt->exam->allow_review)
                                                    <a href="{{ route('student.exams.review', $attempt->exam) }}" class="px-3 py-1 bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 font-bold rounded-lg transition-colors text-xs flex items-center gap-1">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                        Xem lại
                                                    </a>
                                                @else
                                                    <span class="text-[11px] text-slate-400 italic">Không cho xem</span>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>

</div>

<!-- Semester & Subject Filtering Script -->
<script>
    function onSemesterFilterChange() {
        const semesterSelect = document.getElementById('filterSemester');
        const selectedSem = semesterSelect ? semesterSelect.value.trim() : '';
        const subjectSelect = document.getElementById('filterSubject');
        
        if (subjectSelect) {
            const subjectOptions = subjectSelect.querySelectorAll('option');
            let currentSubjectValid = false;

            subjectOptions.forEach(opt => {
                if (!opt.value) {
                    opt.style.display = '';
                    return;
                }
                const optSem = (opt.getAttribute('data-semester') || '').trim();
                if (!selectedSem || optSem === selectedSem) {
                    opt.style.display = '';
                    if (opt.value === subjectSelect.value) {
                        currentSubjectValid = true;
                    }
                } else {
                    opt.style.display = 'none';
                }
            });

            if (subjectSelect.value && !currentSubjectValid) {
                subjectSelect.value = '';
            }
        }

        applyClientExamFilters();
    }

    function applyClientExamFilters() {
        const selectedSemester = document.getElementById('filterSemester')?.value.trim() || '';
        const selectedSubject = document.getElementById('filterSubject')?.value.trim() || '';
        const searchQuery = (document.getElementById('filterSearch')?.value || '').trim().toLowerCase();

        const cards = document.querySelectorAll('.exam-item-card');
        let visibleCount = 0;

        cards.forEach(card => {
            const cardSemester = (card.getAttribute('data-semester') || '').trim();
            const cardSubjectId = (card.getAttribute('data-subject-id') || '').trim();
            const cardTitle = (card.getAttribute('data-title') || '').toLowerCase();
            const cardCode = (card.getAttribute('data-code') || '').toLowerCase();

            const matchSemester = !selectedSemester || cardSemester === selectedSemester;
            const matchSubject = !selectedSubject || cardSubjectId === selectedSubject;
            const matchSearch = !searchQuery || cardTitle.includes(searchQuery) || cardCode.includes(searchQuery);

            if (matchSemester && matchSubject && matchSearch) {
                card.style.display = 'flex';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        const emptyState = document.getElementById('filterEmptyState');
        if (emptyState) {
            if (visibleCount === 0 && cards.length > 0) {
                emptyState.classList.remove('hidden');
            } else {
                emptyState.classList.add('hidden');
            }
        }

        const countElem = document.getElementById('visibleExamCount');
        if (countElem) {
            countElem.textContent = visibleCount;
        }

        const descElem = document.getElementById('activeFilterDesc');
        if (descElem) {
            let descs = [];
            if (selectedSemester) descs.push(selectedSemester);
            if (selectedSubject) {
                const opt = document.querySelector(`#filterSubject option[value="${selectedSubject}"]`);
                if (opt) descs.push(opt.textContent.trim());
            }
            if (searchQuery) descs.push('Từ khóa: "' + searchQuery + '"');

            descElem.textContent = descs.length > 0 ? 'Đang lọc: ' + descs.join(' • ') : 'Đang hiển thị tất cả';
        }
    }

    function resetAllExamFilters() {
        const sem = document.getElementById('filterSemester');
        const subj = document.getElementById('filterSubject');
        const search = document.getElementById('filterSearch');

        if (sem) sem.value = '';
        if (subj) {
            subj.value = '';
            subj.querySelectorAll('option').forEach(opt => opt.style.display = '');
        }
        if (search) search.value = '';

        applyClientExamFilters();
    }

    document.addEventListener('DOMContentLoaded', function() {
        onSemesterFilterChange();
    });
</script>
@endsection
