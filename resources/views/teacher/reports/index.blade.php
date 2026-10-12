@extends('layouts.teacher')

@section('title', 'Báo cáo & Thống kê Giảng viên')

@section('content')
<div class="space-y-8 pb-16" x-data="{
    activeTab: 'all',
    currentExamId: {{ $selectedExamId ?? 'null' }},
    currentStudentId: {{ $selectedStudentId ?? 'null' }}
}">
    <!-- ============================================================== -->
    <!-- 1. TOP HEADER & ACTION BUTTONS -->
    <!-- ============================================================== -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white rounded-2xl p-6 sm:p-8 border border-gray-100 shadow-sm relative overflow-hidden">
        <div class="absolute -right-16 -top-16 w-64 h-64 bg-gradient-to-br from-blue-100/60 to-indigo-100/30 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative z-10">
            <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-blue-600 mb-1">
                <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
                Hệ Thống Báo Cáo Thông Minh
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">
                Báo Cáo & Thống Kê Giảng Viên
            </h1>
            <p class="text-sm text-gray-500 mt-1 max-w-2xl">
                Tổng hợp số lượng sinh viên dự thi theo bài, phân tích sơ đồ phổ điểm từ thấp đến cao và theo dõi biểu đồ tiến bộ học tập đồng bộ trực tiếp từ hệ thống.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3 relative z-10 shrink-0">
            <button type="button" onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-gray-200 bg-white text-gray-700 text-sm font-semibold hover:bg-gray-50 hover:border-gray-300 shadow-sm transition-all duration-200">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                In / Xuất PDF
            </button>

            @if($selectedClass)
            <a href="{{ route('teacher.reports.export', ['class_id' => $selectedClass->id]) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white text-sm font-semibold shadow-sm shadow-emerald-200 transition-all duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Xuất Báo Cáo Excel
            </a>
            @endif

            <a href="{{ route('teacher.reports.index') }}" class="p-2.5 rounded-xl border border-gray-200 bg-white text-gray-500 hover:text-blue-600 hover:border-blue-300 transition-colors" title="Làm mới đồng bộ">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
            </a>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- 2. STICKY FILTER BAR (ĐỒNG BỘ THEO LỚP & BÀI THI) -->
    <!-- ============================================================== -->
    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
        <form method="GET" action="{{ route('teacher.reports.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-4 items-end">
            <!-- Chọn Lớp học -->
            <div class="lg:col-span-4">
                <label for="class_id" class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        Lớp Học Phụ Trách
                    </span>
                </label>
                <select name="class_id" id="class_id" onchange="this.form.submit()" class="w-full rounded-xl border-gray-200 text-sm font-semibold text-gray-800 bg-gray-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3.5 transition-all">
                    @forelse($classes as $c)
                        <option value="{{ $c->id }}" {{ $selectedClassId == $c->id ? 'selected' : '' }}>
                            {{ $c->name }} ({{ $c->code }}) — {{ $c->students_count }} sinh viên
                        </option>
                    @empty
                        <option value="">Chưa có lớp nào</option>
                    @endforelse
                </select>
            </div>

            <!-- Chọn Bài thi trọng tâm -->
            <div class="lg:col-span-4">
                <label for="exam_id" class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                        Bài Thi Xem Sơ Đồ Phổ Điểm
                    </span>
                </label>
                <select name="exam_id" id="exam_id" onchange="this.form.submit()" class="w-full rounded-xl border-gray-200 text-sm font-semibold text-gray-800 bg-gray-50/50 hover:bg-white focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 py-2.5 px-3.5 transition-all">
                    @forelse($exams as $e)
                        <option value="{{ $e->id }}" {{ $selectedExamId == $e->id ? 'selected' : '' }}>
                            {{ $e->title }} ({{ $e->code }})
                        </option>
                    @empty
                        <option value="">Chưa có bài thi nào</option>
                    @endforelse
                </select>
            </div>

            <!-- Chọn Sinh viên để xem tiến bộ -->
            <div class="lg:col-span-3">
                <label for="student_id" class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        Sinh Viên Xem Tiến Bộ
                    </span>
                </label>
                <select name="student_id" id="student_id" onchange="this.form.submit()" class="w-full rounded-xl border-gray-200 text-sm font-semibold text-gray-800 bg-gray-50/50 hover:bg-white focus:bg-white focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3.5 transition-all">
                    @forelse($classStudents as $st)
                        <option value="{{ $st->id }}" {{ $selectedStudentId == $st->id ? 'selected' : '' }}>
                            {{ $st->name }} ({{ $st->code ?? 'SV'.$st->id }})
                        </option>
                    @empty
                        <option value="">Lớp chưa có sinh viên</option>
                    @endforelse
                </select>
            </div>

            <div class="lg:col-span-1">
                <button type="submit" class="w-full py-2.5 px-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm transition-colors flex items-center justify-center gap-1 shadow-sm shadow-blue-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                    Lọc
                </button>
            </div>
        </form>
    </div>

    <!-- ============================================================== -->
    <!-- 3. KPI CARDS (TỔNG QUAN HỆ THỐNG) -->
    <!-- ============================================================== -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- KPI 1: Số bài thi -->
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Tổng Bài Thi Đã Giao</p>
                    <h3 class="text-3xl font-black text-gray-900 mt-2">{{ $kpi['total_exams'] }}</h3>
                    <p class="text-xs text-blue-600 font-semibold mt-1 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        {{ $kpi['total_submissions'] }} lượt nộp bài
                    </p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
            </div>
        </div>

        <!-- KPI 2: Tỷ lệ sinh viên thi -->
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Tỷ Lệ Sinh Viên Dự Thi</p>
                    <h3 class="text-3xl font-black text-gray-900 mt-2">{{ $kpi['average_turnout'] }}%</h3>
                    <p class="text-xs text-gray-500 font-medium mt-1">
                        {{ $kpi['total_attended'] }} / {{ $kpi['total_assigned'] }} lượt phân công
                    </p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
            </div>
            <div class="mt-3 w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                <div class="bg-indigo-600 h-1.5 rounded-full" style="width: {{ min(100, $kpi['average_turnout']) }}%"></div>
            </div>
        </div>

        <!-- KPI 3: Điểm trung bình cả lớp -->
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Điểm Trung Bình Lớp</p>
                    <h3 class="text-3xl font-black text-gray-900 mt-2">{{ number_format($kpi['average_class_score'], 1) }}<span class="text-base font-medium text-gray-400">/10</span></h3>
                    <p class="text-xs text-emerald-600 font-semibold mt-1">
                        {{ $kpi['average_class_score'] >= 5.0 ? 'Đạt chuẩn yêu cầu' : 'Dưới mức trung bình' }}
                    </p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
                </div>
            </div>
        </div>

        <!-- KPI 4: Tỷ lệ đạt chuẩn -->
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Tỷ Lệ Đạt (Điểm ≥ 5.0)</p>
                    <h3 class="text-3xl font-black text-gray-900 mt-2">{{ $kpi['average_pass_rate'] }}%</h3>
                    <p class="text-xs text-emerald-600 font-semibold mt-1">
                        Hoàn thành mục tiêu học phần
                    </p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            <div class="mt-3 w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                <div class="bg-emerald-500 h-1.5 rounded-full" style="width: {{ min(100, $kpi['average_pass_rate']) }}%"></div>
            </div>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- 4. SECTION 1: THỐNG KÊ SỐ LƯỢNG SINH VIÊN THI THEO BÀI -->
    <!-- ============================================================== -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden" id="section-attendance">
        <div class="px-6 py-5 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-gradient-to-r from-blue-50/40 via-white to-transparent">
            <div>
                <div class="flex items-center gap-2">
                    <span class="p-2 rounded-xl bg-blue-100 text-blue-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    </span>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">1. Thống Kê Số Lượng Sinh Viên Thi Theo Từng Bài</h2>
                        <p class="text-xs text-gray-500">So sánh số sinh viên đã thi với sĩ số phân công và tỷ lệ đạt chuẩn qua từng kỳ thi</p>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-100">
                    {{ count($examStats) }} Bài thi ghi nhận
                </span>
            </div>
        </div>

        <div class="p-6">
            <!-- Biểu đồ so sánh sĩ số vs số lượng thi -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-8">
                <div class="lg:col-span-8 bg-slate-50/60 rounded-2xl p-5 border border-slate-100">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-bold text-gray-800">Biểu đồ so sánh: Sinh viên dự thi vs Sĩ số được giao</h3>
                        <span class="text-xs text-gray-400">Đơn vị: Sinh viên</span>
                    </div>
                    <div class="relative h-72">
                        <canvas id="examAttendanceChart"></canvas>
                    </div>
                </div>

                <div class="lg:col-span-4 bg-slate-50/60 rounded-2xl p-5 border border-slate-100 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-sm font-bold text-gray-800">Điểm TB & Tỷ Lệ Đạt (%)</h3>
                            <span class="text-xs text-gray-400">Xu hướng</span>
                        </div>
                        <div class="relative h-64">
                            <canvas id="examPerformanceTrendChart"></canvas>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 text-center mt-2 italic">
                        Biến thiên điểm trung bình cả lớp qua các bài kiểm tra
                    </p>
                </div>
            </div>

            <!-- Bảng danh sách thống kê chi tiết từng bài thi -->
            <div class="overflow-x-auto rounded-xl border border-gray-100">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-gray-50/80 text-xs font-bold text-gray-600 uppercase tracking-wider">
                            <th class="px-5 py-3.5 border-b border-gray-100">Tên bài thi & Mã đề</th>
                            <th class="px-4 py-3.5 border-b border-gray-100 text-center">Sĩ số giao</th>
                            <th class="px-4 py-3.5 border-b border-gray-100 text-center">Đã thi</th>
                            <th class="px-4 py-3.5 border-b border-gray-100 text-center">Chưa thi</th>
                            <th class="px-5 py-3.5 border-b border-gray-100">Tỷ lệ tham gia</th>
                            <th class="px-4 py-3.5 border-b border-gray-100 text-center">Điểm TB</th>
                            <th class="px-4 py-3.5 border-b border-gray-100 text-center">Cao / Thấp</th>
                            <th class="px-4 py-3.5 border-b border-gray-100 text-center">Tỷ lệ Đạt</th>
                            <th class="px-4 py-3.5 border-b border-gray-100 text-right">Xem Sơ Đồ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($examStats as $stat)
                        <tr class="hover:bg-blue-50/40 transition-colors {{ $selectedExamId == $stat['exam']->id ? 'bg-blue-50/30' : '' }}">
                            <td class="px-5 py-4">
                                <div class="font-bold text-gray-900">{{ $stat['exam']->title }}</div>
                                <div class="text-xs text-gray-400 mt-0.5 flex items-center gap-2">
                                    <span class="font-mono bg-gray-100 px-1.5 py-0.5 rounded text-gray-600">{{ $stat['exam']->code }}</span>
                                    <span>•</span>
                                    <span>{{ $stat['exam']->duration_minutes }} phút</span>
                                    @if($stat['exam']->start_at)
                                        <span>•</span>
                                        <span>{{ $stat['exam']->start_at->format('d/m/Y') }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-4 text-center font-semibold text-gray-700">
                                {{ $stat['assigned_count'] }}
                            </td>
                            <td class="px-4 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700">
                                    {{ $stat['attended_count'] }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-center">
                                @if($stat['unattended_count'] > 0)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700">
                                        {{ $stat['unattended_count'] }} vắng
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400">0</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 bg-gray-100 rounded-full h-2 overflow-hidden max-w-[100px]">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ min(100, $stat['turnout_rate']) }}%"></div>
                                    </div>
                                    <span class="font-bold text-xs text-gray-800">{{ $stat['turnout_rate'] }}%</span>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-center">
                                <span class="font-bold font-mono px-2 py-1 rounded-lg {{ $stat['avg_score'] >= 7.0 ? 'bg-emerald-50 text-emerald-700' : ($stat['avg_score'] >= 5.0 ? 'bg-blue-50 text-blue-700' : 'bg-rose-50 text-rose-700') }}">
                                    {{ number_format($stat['avg_score'], 1) }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-center font-mono text-xs text-gray-600">
                                <span class="text-emerald-600 font-bold">{{ number_format($stat['max_score'], 1) }}</span> / 
                                <span class="text-rose-500 font-semibold">{{ number_format($stat['min_score'], 1) }}</span>
                            </td>
                            <td class="px-4 py-4 text-center">
                                <span class="font-semibold text-xs {{ $stat['pass_rate'] >= 80 ? 'text-emerald-600' : ($stat['pass_rate'] >= 50 ? 'text-blue-600' : 'text-rose-600') }}">
                                    {{ $stat['pass_rate'] }}% ({{ $stat['passed_count'] }})
                                </span>
                            </td>
                            <td class="px-4 py-4 text-right">
                                <a href="{{ route('teacher.reports.index', ['class_id' => $selectedClassId, 'exam_id' => $stat['exam']->id, 'student_id' => $selectedStudentId]) }}#section-ascending" class="inline-flex items-center gap-1 text-xs font-semibold px-3 py-1.5 rounded-lg border border-indigo-200 text-indigo-700 hover:bg-indigo-50 transition-colors">
                                    Sơ đồ điểm
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="px-6 py-8 text-center text-gray-400">
                                Chưa có dữ liệu bài thi nào cho lớp học đã chọn.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- 5. SECTION 2: SƠ ĐỒ ĐẠT ĐƯỢC ĐIỂM TỪ THẤP ĐẾN CAO CỦA CẢ LỚP -->
    <!-- ============================================================== -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden" id="section-ascending">
        <div class="px-6 py-5 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-gradient-to-r from-indigo-50/40 via-white to-transparent">
            <div>
                <div class="flex items-center gap-2">
                    <span class="p-2 rounded-xl bg-indigo-100 text-indigo-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    </span>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">2. Sơ Đồ Đạt Được Điểm Từ Thấp Đến Cao Của Cả Lớp</h2>
                        <p class="text-xs text-gray-500">
                            Đang xem bài: <strong class="text-indigo-700">{{ $selectedExam ? $selectedExam->title : 'Chưa chọn bài thi' }}</strong> 
                            ({{ $selectedClass ? $selectedClass->name : 'Toàn trường' }})
                        </p>
                    </div>
                </div>
            </div>

            <!-- Selector bài thi nhanh -->
            <div class="flex items-center gap-3">
                <form method="GET" action="{{ route('teacher.reports.index') }}" class="flex items-center gap-2">
                    <input type="hidden" name="class_id" value="{{ $selectedClassId }}">
                    <input type="hidden" name="student_id" value="{{ $selectedStudentId }}">
                    <label for="quick_exam_select" class="text-xs font-bold text-gray-500 uppercase">Đổi bài:</label>
                    <select id="quick_exam_select" name="exam_id" onchange="this.form.submit()" class="rounded-xl border-gray-200 text-xs font-semibold py-1.5 px-3 bg-gray-50 text-gray-700 focus:ring-indigo-500 focus:border-indigo-500">
                        @foreach($exams as $ex)
                            <option value="{{ $ex->id }}" {{ $selectedExamId == $ex->id ? 'selected' : '' }}>
                                {{ Str::limit($ex->title, 32) }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-8">
                <!-- SƠ ĐỒ ĐƯỜNG ĐIỂM TỪ THẤP ĐẾN CAO (ASCENDING CURVE) -->
                <div class="lg:col-span-8 bg-slate-50/60 rounded-2xl p-6 border border-slate-100 flex flex-col justify-between">
                    <div>
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
                            <div>
                                <h3 class="text-base font-bold text-gray-900">Đường cong phổ điểm từ thấp đến cao</h3>
                                <p class="text-xs text-gray-500">Xếp hạng từng sinh viên theo mức điểm tăng dần (0.0 → 10.0)</p>
                            </div>

                            <div class="flex items-center gap-4 text-xs font-semibold">
                                <span class="flex items-center gap-1.5 text-indigo-700">
                                    <span class="w-3 h-3 rounded-full bg-indigo-600 inline-block"></span>
                                    Điểm sinh viên
                                </span>
                                <span class="flex items-center gap-1.5 text-amber-600">
                                    <span class="w-3 h-0.5 bg-amber-500 inline-block"></span>
                                    Điểm TB lớp ({{ $ascendingChart['average_line'] }})
                                </span>
                                <span class="flex items-center gap-1.5 text-emerald-600">
                                    <span class="w-3 h-0.5 border-b-2 border-dashed border-emerald-500 inline-block"></span>
                                    Mốc đạt (5.0)
                                </span>
                            </div>
                        </div>

                        <!-- Canvas Biểu đồ đường từ thấp đến cao -->
                        <div class="relative h-80">
                            <canvas id="ascendingScoreChart"></canvas>
                        </div>
                    </div>

                    <!-- Chỉ số tóm tắt bên dưới đường cong -->
                    <div class="grid grid-cols-3 gap-4 pt-4 mt-4 border-t border-slate-200/60 text-center">
                        <div class="p-2.5 rounded-xl bg-white border border-slate-100 shadow-2xs">
                            <span class="text-xs text-gray-400 font-semibold block">Điểm Thấp Nhất</span>
                            <span class="text-lg font-black text-rose-600 font-mono">
                                {{ !empty($ascendingChart['scores']) ? number_format(min($ascendingChart['scores']), 1) : '0.0' }}
                            </span>
                        </div>
                        <div class="p-2.5 rounded-xl bg-white border border-slate-100 shadow-2xs">
                            <span class="text-xs text-gray-400 font-semibold block">Điểm Trung Bình</span>
                            <span class="text-lg font-black text-amber-600 font-mono">
                                {{ number_format($ascendingChart['average_line'], 1) }}
                            </span>
                        </div>
                        <div class="p-2.5 rounded-xl bg-white border border-slate-100 shadow-2xs">
                            <span class="text-xs text-gray-400 font-semibold block">Điểm Cao Nhất</span>
                            <span class="text-lg font-black text-emerald-600 font-mono">
                                {{ !empty($ascendingChart['scores']) ? number_format(max($ascendingChart['scores']), 1) : '0.0' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- PHÂN BỐ PHỔ ĐIỂM (SCORE BRACKETS DISTRIBUTION) -->
                <div class="lg:col-span-4 bg-slate-50/60 rounded-2xl p-6 border border-slate-100 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-base font-bold text-gray-900">Phổ điểm các dải</h3>
                                <p class="text-xs text-gray-500">Phân bố xếp loại học lực của cả lớp</p>
                            </div>
                        </div>

                        <div class="relative h-52 mb-4">
                            <canvas id="scoreDistributionChart"></canvas>
                        </div>

                        <!-- Danh sách thống kê chi tiết từng dải -->
                        <div class="space-y-2 text-xs">
                            <div class="flex items-center justify-between p-2 rounded-lg bg-white border border-gray-100">
                                <span class="font-semibold text-emerald-700 flex items-center gap-1.5">
                                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Xuất sắc (9.0 - 10.0)
                                </span>
                                <span class="font-bold text-gray-800 font-mono">{{ $scoreDistribution['xuat_sac'] }} sinh viên</span>
                            </div>
                            <div class="flex items-center justify-between p-2 rounded-lg bg-white border border-gray-100">
                                <span class="font-semibold text-blue-700 flex items-center gap-1.5">
                                    <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span> Giỏi (8.0 - < 9.0)
                                </span>
                                <span class="font-bold text-gray-800 font-mono">{{ $scoreDistribution['gioi'] }} sinh viên</span>
                            </div>
                            <div class="flex items-center justify-between p-2 rounded-lg bg-white border border-gray-100">
                                <span class="font-semibold text-indigo-700 flex items-center gap-1.5">
                                    <span class="w-2.5 h-2.5 rounded-full bg-indigo-500"></span> Khá (6.5 - < 8.0)
                                </span>
                                <span class="font-bold text-gray-800 font-mono">{{ $scoreDistribution['kha'] }} sinh viên</span>
                            </div>
                            <div class="flex items-center justify-between p-2 rounded-lg bg-white border border-gray-100">
                                <span class="font-semibold text-amber-700 flex items-center gap-1.5">
                                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span> Trung bình (5.0 - < 6.5)
                                </span>
                                <span class="font-bold text-gray-800 font-mono">{{ $scoreDistribution['trung_binh'] }} sinh viên</span>
                            </div>
                            <div class="flex items-center justify-between p-2 rounded-lg bg-white border border-gray-100">
                                <span class="font-semibold text-rose-700 flex items-center gap-1.5">
                                    <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span> Yếu & Kém (< 5.0)
                                </span>
                                <span class="font-bold text-gray-800 font-mono">{{ $scoreDistribution['yeu'] + $scoreDistribution['kem'] }} sinh viên</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- BẢNG DANH SÁCH CHI TIẾT XẾP HẠNG TỪ THẤP ĐẾN CAO -->
            <div class="mt-6">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                        <span>Danh sách sinh viên xếp hạng từ thấp đến cao trong bài thi</span>
                        <span class="text-xs font-normal text-gray-400">({{ count($classAscendingRanking) }} sinh viên)</span>
                    </h3>
                </div>

                <div class="overflow-x-auto rounded-xl border border-gray-100">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-gray-50/80 text-xs font-bold text-gray-600 uppercase tracking-wider">
                                <th class="px-4 py-3 border-b border-gray-100 text-center w-16">Thứ hạng</th>
                                <th class="px-5 py-3 border-b border-gray-100">Mã SV & Họ Tên</th>
                                <th class="px-4 py-3 border-b border-gray-100 text-center">Điểm số</th>
                                <th class="px-4 py-3 border-b border-gray-100 text-center">Đúng / Sai</th>
                                <th class="px-4 py-3 border-b border-gray-100 text-center">Thời gian nộp</th>
                                <th class="px-4 py-3 border-b border-gray-100 text-center">Xếp loại</th>
                                <th class="px-4 py-3 border-b border-gray-100 text-right">Theo Dõi Tiến Bộ</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($classAscendingRanking as $index => $item)
                            <tr class="hover:bg-indigo-50/30 transition-colors {{ $selectedStudentId == $item['student']->id ? 'bg-indigo-50/40' : '' }}">
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full text-xs font-bold font-mono {{ $item['has_taken'] ? 'bg-indigo-100 text-indigo-700' : 'bg-gray-100 text-gray-400' }}">
                                        #{{ $index + 1 }}
                                    </span>
                                </td>
                                <td class="px-5 py-3">
                                    <div class="font-bold text-gray-900">{{ $item['student']->name }}</div>
                                    <div class="text-xs text-gray-400 font-mono">{{ $item['student']->code ?? 'SV'.$item['student']->id }} • {{ $item['student']->email }}</div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($item['has_taken'])
                                        <span class="font-mono text-base font-extrabold px-3 py-1 rounded-xl {{ $item['score'] >= 8.0 ? 'bg-emerald-50 text-emerald-700' : ($item['score'] >= 5.0 ? 'bg-blue-50 text-blue-700' : 'bg-rose-50 text-rose-700') }}">
                                            {{ number_format($item['score'], 1) }}
                                        </span>
                                    @else
                                        <span class="text-xs font-semibold px-2 py-1 rounded-md bg-gray-100 text-gray-500">Chưa thi</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center text-xs font-mono">
                                    @if($item['has_taken'])
                                        <span class="text-emerald-600 font-bold">{{ $item['correct_answers'] }}</span> / 
                                        <span class="text-rose-500 font-bold">{{ $item['wrong_answers'] }}</span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center text-xs text-gray-500">
                                    {{ $item['submitted_at'] ? $item['submitted_at']->format('d/m/Y H:i') : '--' }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($item['has_taken'])
                                        @if($item['score'] >= 9.0)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">Xuất sắc</span>
                                        @elseif($item['score'] >= 8.0)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800">Giỏi</span>
                                        @elseif($item['score'] >= 6.5)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800">Khá</span>
                                        @elseif($item['score'] >= 5.0)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800">Trung bình</span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800">Yếu / Chưa đạt</span>
                                        @endif
                                    @else
                                        <span class="text-xs text-gray-400">Vắng</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('teacher.reports.index', ['class_id' => $selectedClassId, 'exam_id' => $selectedExamId, 'student_id' => $item['student']->id]) }}#section-progress" class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1.5 rounded-lg bg-gray-100 text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 transition-colors">
                                        Xem tiến bộ
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-6 text-center text-gray-400">Không có dữ liệu sinh viên</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- 6. SECTION 3: THỐNG KÊ ĐIỂM TIẾN BỘ CỦA SINH VIÊN THEO TỪNG BÀI THI -->
    <!-- ============================================================== -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden" id="section-progress">
        <div class="px-6 py-5 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-gradient-to-r from-emerald-50/40 via-white to-transparent">
            <div>
                <div class="flex items-center gap-2">
                    <span class="p-2 rounded-xl bg-emerald-100 text-emerald-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </span>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">3. Thống Kê Điểm Tiến Bộ Của Sinh Viên Theo Từng Bài Thi</h2>
                        <p class="text-xs text-gray-500">
                            Theo dõi mức độ bứt phá, quỹ đạo điểm số cá nhân và so sánh với điểm trung bình lớp qua từng bài thi theo thời gian
                        </p>
                    </div>
                </div>
            </div>

            <!-- Selector sinh viên nhanh -->
            <div class="flex items-center gap-3">
                <form method="GET" action="{{ route('teacher.reports.index') }}" class="flex items-center gap-2">
                    <input type="hidden" name="class_id" value="{{ $selectedClassId }}">
                    <input type="hidden" name="exam_id" value="{{ $selectedExamId }}">
                    <label for="quick_student_select" class="text-xs font-bold text-gray-500 uppercase">Học viên:</label>
                    <select id="quick_student_select" name="student_id" onchange="this.form.submit()" class="rounded-xl border-gray-200 text-xs font-semibold py-1.5 px-3 bg-gray-50 text-gray-700 focus:ring-emerald-500 focus:border-emerald-500">
                        @foreach($classStudents as $st)
                            <option value="{{ $st->id }}" {{ $selectedStudentId == $st->id ? 'selected' : '' }}>
                                {{ $st->name }} ({{ $st->code ?? 'SV' }})
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>

        <div class="p-6">
            <!-- BIỂU ĐỒ TIẾN BỘ CỦA SINH VIÊN ĐƯỢC CHỌN -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-8">
                <div class="lg:col-span-8 bg-slate-50/60 rounded-2xl p-6 border border-slate-100">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-base font-bold text-gray-900">
                                    Đường biểu diễn tiến bộ: <span class="text-emerald-700">{{ $selectedStudent ? $selectedStudent->name : 'Chưa chọn' }}</span>
                                </h3>
                                @if($selectedStudent)
                                    <span class="text-xs px-2 py-0.5 rounded-md font-mono bg-emerald-100 text-emerald-800 font-semibold">{{ $selectedStudent->code ?? 'SV' }}</span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-500">So sánh điểm của sinh viên với điểm trung bình của cả lớp tại từng bài kiểm tra</p>
                        </div>

                        <div class="flex items-center gap-4 text-xs font-semibold">
                            <span class="flex items-center gap-1.5 text-emerald-700">
                                <span class="w-3 h-3 rounded-full bg-emerald-600 inline-block"></span>
                                Điểm sinh viên
                            </span>
                            <span class="flex items-center gap-1.5 text-blue-600">
                                <span class="w-3 h-0.5 border-b-2 border-dashed border-blue-500 inline-block"></span>
                                Điểm TB cả lớp
                            </span>
                        </div>
                    </div>

                    <!-- Canvas biểu đồ tiến bộ -->
                    <div class="relative h-72">
                        <canvas id="studentProgressChart"></canvas>
                    </div>
                </div>

                <!-- THẺ ĐÁNH GIÁ PHONG ĐỘ SINH VIÊN -->
                <div class="lg:col-span-4 bg-slate-50/60 rounded-2xl p-6 border border-slate-100 flex flex-col justify-between">
                    <div>
                        <h3 class="text-base font-bold text-gray-900 mb-1">Chỉ số phát triển cá nhân</h3>
                        <p class="text-xs text-gray-500 mb-4">Hồ sơ tiến độ học phần của sinh viên</p>

                        @php
                            $currentMatrixItem = collect($studentsProgressMatrix)->firstWhere('student.id', $selectedStudentId);
                        @endphp

                        @if($currentMatrixItem && $currentMatrixItem['initial_score'] !== null)
                        <div class="space-y-3">
                            <div class="p-3 bg-white rounded-xl border border-gray-100 flex items-center justify-between">
                                <span class="text-xs font-medium text-gray-500">Điểm bài thi đầu tiên:</span>
                                <span class="font-mono font-bold text-gray-800 text-sm">{{ number_format($currentMatrixItem['initial_score'], 1) }}</span>
                            </div>
                            <div class="p-3 bg-white rounded-xl border border-gray-100 flex items-center justify-between">
                                <span class="text-xs font-medium text-gray-500">Điểm bài thi gần nhất:</span>
                                <span class="font-mono font-bold text-gray-800 text-sm">{{ number_format($currentMatrixItem['latest_score'], 1) }}</span>
                            </div>
                            <div class="p-3 bg-white rounded-xl border border-gray-100 flex items-center justify-between">
                                <span class="text-xs font-medium text-gray-500">Mức độ tăng trưởng (Δ):</span>
                                <span class="font-mono font-black text-sm {{ $currentMatrixItem['score_delta'] > 0 ? 'text-emerald-600' : ($currentMatrixItem['score_delta'] < 0 ? 'text-rose-600' : 'text-gray-600') }}">
                                    {{ $currentMatrixItem['score_delta'] > 0 ? '+'.$currentMatrixItem['score_delta'] : $currentMatrixItem['score_delta'] }}
                                </span>
                            </div>
                            <div class="p-3 bg-white rounded-xl border border-gray-100 flex items-center justify-between">
                                <span class="text-xs font-medium text-gray-500">Điểm cao nhất đạt được:</span>
                                <span class="font-mono font-bold text-emerald-600 text-sm">{{ number_format($currentMatrixItem['highest_score'], 1) }}</span>
                            </div>
                        </div>

                        <div class="mt-4 p-4 rounded-xl {{ $currentMatrixItem['status_type'] === 'success' ? 'bg-emerald-50 text-emerald-800 border border-emerald-100' : ($currentMatrixItem['status_type'] === 'danger' ? 'bg-rose-50 text-rose-800 border border-rose-100' : 'bg-blue-50 text-blue-800 border border-blue-100') }}">
                            <div class="text-xs font-bold uppercase tracking-wider mb-0.5">Nhận Định Phong Độ:</div>
                            <div class="font-bold text-sm flex items-center gap-1.5">
                                @if($currentMatrixItem['status_type'] === 'success')
                                    <span>🚀</span>
                                @elseif($currentMatrixItem['status_type'] === 'primary')
                                    <span>📈</span>
                                @elseif($currentMatrixItem['status_type'] === 'danger')
                                    <span>🔻</span>
                                @else
                                    <span>➖</span>
                                @endif
                                <span>{{ $currentMatrixItem['status_text'] }}</span>
                            </div>
                        </div>
                        @else
                        <div class="py-8 text-center text-gray-400 text-sm">
                            Sinh viên chưa có lịch sử làm bài thi nào.
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- BẢNG TỔNG HỢP TIẾN BỘ CẢ LỚP (PROGRESS MATRIX) -->
            <div class="mt-8">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                        <span>Bảng ma trận điểm và đánh giá tiến độ của toàn bộ sinh viên trong lớp</span>
                        <span class="text-xs font-normal text-gray-400">({{ count($studentsProgressMatrix) }} sinh viên)</span>
                    </h3>
                </div>

                <div class="overflow-x-auto rounded-xl border border-gray-100">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-gray-50/80 text-xs font-bold text-gray-600 uppercase tracking-wider">
                                <th class="px-4 py-3 border-b border-gray-100 text-center w-12">STT</th>
                                <th class="px-5 py-3 border-b border-gray-100">Họ và Tên Sinh Viên</th>
                                @foreach($chronologicalExams as $ex)
                                    <th class="px-3 py-3 border-b border-gray-100 text-center" title="{{ $ex->title }}">
                                        {{ Str::limit($ex->title, 14) }}
                                    </th>
                                @endforeach
                                <th class="px-3 py-3 border-b border-gray-100 text-center">Đầu</th>
                                <th class="px-3 py-3 border-b border-gray-100 text-center">Cuối</th>
                                <th class="px-4 py-3 border-b border-gray-100 text-center">Mức Tiến Bộ (Δ)</th>
                                <th class="px-4 py-3 border-b border-gray-100 text-center">Đánh Giá Phong Độ</th>
                                <th class="px-4 py-3 border-b border-gray-100 text-right">Biểu Đồ</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($studentsProgressMatrix as $idx => $m)
                            <tr class="hover:bg-emerald-50/30 transition-colors {{ $selectedStudentId == $m['student']->id ? 'bg-emerald-50/40' : '' }}">
                                <td class="px-4 py-3 text-center text-xs font-mono font-bold text-gray-400">
                                    {{ $idx + 1 }}
                                </td>
                                <td class="px-5 py-3">
                                    <div class="font-bold text-gray-900">{{ $m['student']->name }}</div>
                                    <div class="text-xs text-gray-400 font-mono">{{ $m['student']->code ?? 'SV'.$m['student']->id }}</div>
                                </td>

                                @foreach($chronologicalExams as $ex)
                                    <td class="px-3 py-3 text-center font-mono text-xs">
                                        @if(isset($m['scores_by_exam'][$ex->id]) && $m['scores_by_exam'][$ex->id] !== null)
                                            <span class="font-bold {{ $m['scores_by_exam'][$ex->id] >= 8.0 ? 'text-emerald-700' : ($m['scores_by_exam'][$ex->id] >= 5.0 ? 'text-blue-700' : 'text-rose-600') }}">
                                                {{ number_format($m['scores_by_exam'][$ex->id], 1) }}
                                            </span>
                                        @else
                                            <span class="text-gray-300">-</span>
                                        @endif
                                    </td>
                                @endforeach

                                <td class="px-3 py-3 text-center font-mono text-xs text-gray-600 font-semibold">
                                    {{ $m['initial_score'] !== null ? number_format($m['initial_score'], 1) : '-' }}
                                </td>
                                <td class="px-3 py-3 text-center font-mono text-xs text-gray-800 font-bold">
                                    {{ $m['latest_score'] !== null ? number_format($m['latest_score'], 1) : '-' }}
                                </td>

                                <td class="px-4 py-3 text-center font-mono text-xs">
                                    @if($m['initial_score'] !== null && $m['latest_score'] !== null)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full font-bold {{ $m['score_delta'] > 0 ? 'bg-emerald-100 text-emerald-800' : ($m['score_delta'] < 0 ? 'bg-rose-100 text-rose-800' : 'bg-gray-100 text-gray-700') }}">
                                            {{ $m['score_delta'] > 0 ? '+'.$m['score_delta'] : $m['score_delta'] }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>

                                <td class="px-4 py-3 text-center">
                                    @if($m['status_type'] === 'success')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            🚀 Tiến bộ vượt bậc
                                        </span>
                                    @elseif($m['status_type'] === 'primary')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-teal-50 text-teal-700 border border-teal-200">
                                            📈 Có tiến bộ
                                        </span>
                                    @elseif($m['status_type'] === 'danger')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                            🔻 Cần chú ý
                                        </span>
                                    @elseif($m['status_type'] === 'info')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">
                                            Mới làm 1 bài
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                            ➖ Giữ vững
                                        </span>
                                    @endif
                                </td>

                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('teacher.reports.index', ['class_id' => $selectedClassId, 'exam_id' => $selectedExamId, 'student_id' => $m['student']->id]) }}#section-progress" class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1.5 rounded-lg border border-emerald-200 text-emerald-700 hover:bg-emerald-50 transition-colors">
                                        Xem đồ thị
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="px-6 py-6 text-center text-gray-400">Không có dữ liệu tiến bộ</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================== -->
<!-- CHART.JS SCRIPTS & CONFIGURATIONS -->
<!-- ============================================================== -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // -------------------------------------------------------------
    // 1. CHART: Exam Attendance (Số lượng sinh viên thi theo bài)
    // -------------------------------------------------------------
    const examAttendanceCtx = document.getElementById('examAttendanceChart');
    if (examAttendanceCtx) {
        new Chart(examAttendanceCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($examComparisonChart['labels']) !!},
                datasets: [
                    {
                        label: 'Số sinh viên đã thi',
                        data: {!! json_encode($examComparisonChart['attended']) !!},
                        backgroundColor: 'rgba(59, 130, 246, 0.85)',
                        borderColor: 'rgb(59, 130, 246)',
                        borderWidth: 1,
                        borderRadius: 8,
                        barPercentage: 0.6,
                    },
                    {
                        label: 'Sĩ số được phân công',
                        data: {!! json_encode($examComparisonChart['assigned']) !!},
                        backgroundColor: 'rgba(226, 232, 240, 0.9)',
                        borderColor: 'rgb(203, 213, 225)',
                        borderWidth: 1,
                        borderRadius: 8,
                        barPercentage: 0.6,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: { family: 'inherit', size: 12, weight: 600 },
                            usePointStyle: true,
                            boxWidth: 8,
                        }
                    },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 12,
                        cornerRadius: 10,
                        titleFont: { family: 'inherit', weight: 700 },
                        bodyFont: { family: 'inherit' },
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'inherit', size: 11 } }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            font: { family: 'inherit', size: 11 }
                        },
                        grid: { color: 'rgba(241, 245, 249, 1)' }
                    }
                }
            }
        });
    }

    // -------------------------------------------------------------
    // 1B. CHART: Exam Performance Trend (Điểm TB & Pass Rate)
    // -------------------------------------------------------------
    const examTrendCtx = document.getElementById('examPerformanceTrendChart');
    if (examTrendCtx) {
        new Chart(examTrendCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($examComparisonChart['labels']) !!},
                datasets: [
                    {
                        label: 'Điểm trung bình (Thang 10)',
                        data: {!! json_encode($examComparisonChart['average_scores']) !!},
                        borderColor: '#f59e0b',
                        backgroundColor: 'rgba(245, 158, 11, 0.1)',
                        borderWidth: 3,
                        pointBackgroundColor: '#f59e0b',
                        pointRadius: 5,
                        tension: 0.3,
                        yAxisID: 'y',
                    },
                    {
                        label: 'Tỷ lệ đạt (%)',
                        data: {!! json_encode($examComparisonChart['pass_rates']) !!},
                        borderColor: '#10b981',
                        backgroundColor: 'transparent',
                        borderWidth: 2,
                        borderDash: [4, 4],
                        pointBackgroundColor: '#10b981',
                        pointRadius: 4,
                        tension: 0.3,
                        yAxisID: 'y1',
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: { family: 'inherit', size: 11, weight: 600 },
                            usePointStyle: true,
                            boxWidth: 8,
                        }
                    },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 10,
                        cornerRadius: 10,
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { display: false }
                    },
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        min: 0,
                        max: 10,
                        ticks: { stepSize: 2 }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        min: 0,
                        max: 100,
                        grid: { drawOnChartArea: false },
                        ticks: {
                            callback: function(v) { return v + '%'; }
                        }
                    }
                }
            }
        });
    }

    // -------------------------------------------------------------
    // 2. CHART: Ascending Score Curve (Sơ đồ điểm từ thấp đến cao)
    // -------------------------------------------------------------
    const ascendingScoreCtx = document.getElementById('ascendingScoreChart');
    if (ascendingScoreCtx) {
        const studentScores = {!! json_encode($ascendingChart['scores']) !!};
        const studentLabels = {!! json_encode($ascendingChart['labels']) !!};
        const studentNames = {!! json_encode($ascendingChart['student_names']) !!};
        const classAvg = {{ $ascendingChart['average_line'] }};

        // Create gradient
        const ctx2 = ascendingScoreCtx.getContext('2d');
        const gradient = ctx2.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(79, 70, 229, 0.35)');
        gradient.addColorStop(1, 'rgba(79, 70, 229, 0.02)');

        new Chart(ascendingScoreCtx, {
            type: 'line',
            data: {
                labels: studentLabels,
                datasets: [
                    {
                        label: 'Điểm sinh viên (Xếp từ thấp đến cao)',
                        data: studentScores,
                        borderColor: '#4f46e5',
                        backgroundColor: gradient,
                        fill: true,
                        borderWidth: 3,
                        pointBackgroundColor: '#4f46e5',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 6,
                        pointHoverRadius: 8,
                        tension: 0.35,
                    },
                    {
                        label: 'Điểm trung bình cả lớp (' + classAvg + ')',
                        data: Array(studentScores.length).fill(classAvg),
                        borderColor: '#f59e0b',
                        borderWidth: 2,
                        borderDash: [5, 5],
                        pointRadius: 0,
                        fill: false,
                    },
                    {
                        label: 'Mốc đạt chuẩn (5.0)',
                        data: Array(studentScores.length).fill(5.0),
                        borderColor: '#10b981',
                        borderWidth: 1.5,
                        borderDash: [3, 3],
                        pointRadius: 0,
                        fill: false,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 12,
                        cornerRadius: 10,
                        titleFont: { family: 'inherit', weight: 700 },
                        callbacks: {
                            title: function(items) {
                                const idx = items[0].dataIndex;
                                return studentNames[idx] || items[0].label;
                            },
                            label: function(item) {
                                if (item.datasetIndex === 0) {
                                    return ' Điểm thi: ' + item.parsed.y + ' / 10.0';
                                }
                                return ' ' + item.dataset.label;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { color: 'rgba(241, 245, 249, 1)' },
                        ticks: { font: { family: 'inherit', size: 11, weight: 600 } }
                    },
                    y: {
                        min: 0,
                        max: 10,
                        ticks: { stepSize: 2 },
                        grid: { color: 'rgba(241, 245, 249, 1)' }
                    }
                }
            }
        });
    }

    // -------------------------------------------------------------
    // 2B. CHART: Score Distribution (Phổ điểm các dải)
    // -------------------------------------------------------------
    const scoreDistCtx = document.getElementById('scoreDistributionChart');
    if (scoreDistCtx) {
        new Chart(scoreDistCtx, {
            type: 'doughnut',
            data: {
                labels: ['Xuất sắc (9-10)', 'Giỏi (8-<9)', 'Khá (6.5-<8)', 'Trung bình (5-<6.5)', 'Yếu & Kém (<5)'],
                datasets: [
                    {
                        data: [
                            {{ $scoreDistribution['xuat_sac'] }},
                            {{ $scoreDistribution['gioi'] }},
                            {{ $scoreDistribution['kha'] }},
                            {{ $scoreDistribution['trung_binh'] }},
                            {{ $scoreDistribution['yeu'] + $scoreDistribution['kem'] }}
                        ],
                        backgroundColor: [
                            '#10b981', // emerald
                            '#3b82f6', // blue
                            '#6366f1', // indigo
                            '#f59e0b', // amber
                            '#ef4444'  // rose
                        ],
                        borderWidth: 2,
                        borderColor: '#ffffff',
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 10,
                        cornerRadius: 10,
                        callbacks: {
                            label: function(item) {
                                return ' ' + item.label + ': ' + item.parsed + ' SV';
                            }
                        }
                    }
                },
                cutout: '65%',
            }
        });
    }

    // -------------------------------------------------------------
    // 3. CHART: Student Progress Trend (Điểm tiến bộ theo từng bài)
    // -------------------------------------------------------------
    const studentProgressCtx = document.getElementById('studentProgressChart');
    if (studentProgressCtx) {
        const studentScores = {!! json_encode($studentProgressChart['student_scores']) !!};
        const classAverages = {!! json_encode($studentProgressChart['class_averages']) !!};
        const examLabels = {!! json_encode($studentProgressChart['labels']) !!};

        const ctx3 = studentProgressCtx.getContext('2d');
        const gradient3 = ctx3.createLinearGradient(0, 0, 0, 260);
        gradient3.addColorStop(0, 'rgba(16, 185, 129, 0.35)');
        gradient3.addColorStop(1, 'rgba(16, 185, 129, 0.02)');

        new Chart(studentProgressCtx, {
            type: 'line',
            data: {
                labels: examLabels,
                datasets: [
                    {
                        label: 'Điểm sinh viên: {{ $studentProgressChart['student_name'] }}',
                        data: studentScores,
                        borderColor: '#10b981',
                        backgroundColor: gradient3,
                        fill: true,
                        borderWidth: 3.5,
                        pointBackgroundColor: '#10b981',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 6,
                        pointHoverRadius: 8,
                        tension: 0.3,
                        spanGaps: true,
                    },
                    {
                        label: 'Điểm trung bình của cả lớp',
                        data: classAverages,
                        borderColor: '#3b82f6',
                        backgroundColor: 'transparent',
                        borderWidth: 2,
                        borderDash: [5, 5],
                        pointBackgroundColor: '#3b82f6',
                        pointRadius: 4,
                        tension: 0.3,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 12,
                        cornerRadius: 10,
                        titleFont: { family: 'inherit', weight: 700 },
                        callbacks: {
                            label: function(item) {
                                return ' ' + item.dataset.label + ': ' + (item.parsed.y !== null ? item.parsed.y + ' điểm' : 'Chưa thi');
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'inherit', size: 11, weight: 600 } }
                    },
                    y: {
                        min: 0,
                        max: 10,
                        ticks: { stepSize: 2 },
                        grid: { color: 'rgba(241, 245, 249, 1)' }
                    }
                }
            }
        });
    }
});
</script>

<style>
@media print {
    /* Hide top nav, sidebar, filter controls and buttons */
    aside, header, form, button, a[href*="export"], a[href*="reports"] {
        display: none !important;
    }
    body, .space-y-8 {
        background: white !important;
        color: black !important;
        padding: 0 !important;
        margin: 0 !important;
        font-size: 11pt !important;
    }
    .shadow-sm, .shadow-md, .shadow-2xs {
        box-shadow: none !important;
    }
    .rounded-2xl, .rounded-xl {
        border-radius: 4px !important;
    }
    .border {
        border-color: #cbd5e1 !important;
    }
    canvas {
        max-width: 100% !important;
    }
}
</style>
@endsection
