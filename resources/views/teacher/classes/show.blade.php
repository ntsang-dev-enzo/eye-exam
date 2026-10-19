@extends('layouts.teacher')

@section('title', 'Chi tiết Lớp học')

@section('content')
<div class="max-w-6xl mx-auto space-y-6 sm:space-y-8">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
        <div class="space-y-1.5">
            <!-- Breadcrumb -->
            <div class="flex items-center gap-2 text-xs">
                <a href="{{ route('teacher.classes.index') }}"
                    class="font-semibold text-blue-600 hover:text-blue-800 hover:underline transition-colors flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    Lớp học phần
                </a>
                <svg class="w-3.5 h-3.5 text-slate-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path></svg>
                <span class="font-bold text-slate-500 font-mono">{{ $class->code }}</span>
            </div>

            <div class="flex flex-wrap items-center gap-2 mt-1">
                <h1 class="text-2xl sm:text-3xl font-black text-[#0a2540] tracking-tight">{{ $class->name }}</h1>
                <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-600">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    Lớp học phần phụ trách
                </span>
            </div>
        </div>

        <div class="flex items-center gap-3 shrink-0">
            <a href="{{ route('teacher.assignments.create', ['class_id' => $class->id]) }}"
                class="inline-flex items-center px-4 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-sm font-bold rounded-xl shadow-md shadow-blue-500/20 hover:shadow-lg hover:shadow-blue-500/30 hover:-translate-y-0.5 transition-all">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                Giao đề cho lớp
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200/80 text-emerald-800 text-xs font-semibold flex items-center gap-2.5 shadow-2xs">
            <span class="w-7 h-7 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </span>
            {{ session('success') }}
        </div>
    @endif

    <!-- Stats Card -->
    <div class="bg-white/80 backdrop-blur-sm rounded-3xl border border-blue-100 shadow-[0_10px_35px_-10px_rgba(59,130,246,0.08)] p-6 sm:p-8">
        <div class="flex items-center gap-3 mb-6 pb-5 border-b border-blue-50">
            <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white shadow-md shadow-blue-500/20">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <h2 class="text-lg font-extrabold text-[#0a2540] tracking-tight">Thông tin lớp học</h2>
                <p class="text-xs text-slate-400 font-medium">Chi tiết về lớp học và sĩ số</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="p-4 bg-blue-50/60 rounded-2xl border border-blue-100">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Mã lớp</p>
                <p class="text-xl font-black text-blue-600 font-mono">{{ $class->code }}</p>
            </div>
            <div class="p-4 bg-blue-50/60 rounded-2xl border border-blue-100">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Học kỳ / Môn học</p>
                <p class="text-sm font-bold text-[#0a2540] leading-snug">
                    @if($class->semester)
                        {{ $class->semester->name }} ({{ $class->semester->academic_year }})
                    @elseif($class->course)
                        {{ $class->course->name }} ({{ $class->course->semester }})
                    @else
                        Chưa cập nhật
                    @endif
                </p>
                @if($class->subject)
                    <p class="text-xs text-blue-600 font-semibold mt-1">
                        {{ $class->subject->name }} ({{ $class->subject->credits }} TC)
                    </p>
                @endif
            </div>
            <div class="p-4 bg-blue-50/60 rounded-2xl border border-blue-100">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Sĩ số</p>
                <div class="flex items-baseline gap-1">
                    <span class="text-xl font-black text-[#0a2540] font-mono">{{ $students->count() }}</span>
                    <span class="text-sm font-bold text-slate-400">sinh viên</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Môn học & Tài liệu -->
    <div class="bg-white/80 backdrop-blur-sm rounded-3xl border border-blue-100 shadow-[0_10px_35px_-10px_rgba(59,130,246,0.08)] p-6 sm:p-8 space-y-5">
        <div class="flex items-center justify-between border-b border-blue-50 pb-5">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                </div>
                <div>
                    <h3 class="text-lg font-extrabold text-[#0a2540] tracking-tight">Môn học & Tài liệu học tập</h3>
                    <p class="text-xs text-slate-400 font-medium">Danh sách môn học được phân công giảng dạy trong lớp</p>
                </div>
            </div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-100 font-mono">
                {{ $classSubjects->count() }} môn học
            </span>
        </div>

        @if($classSubjects->isEmpty())
            <p class="text-xs text-slate-400 py-8 text-center italic">Chưa có môn học nào được phân công cho lớp này.</p>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($classSubjects as $cs)
                    @php
                        $isMySubject = ($cs->teacher_id == auth()->id() || auth()->user()->role === 'admin');
                    @endphp
                    <div class="bg-gradient-to-b from-white via-white to-blue-50/30 rounded-2xl border {{ $isMySubject ? 'border-blue-200' : 'border-blue-100' }} p-4 flex flex-col justify-between space-y-3 hover:shadow-md hover:shadow-blue-500/10 hover:-translate-y-0.5 transition-all duration-200 group">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-mono font-extrabold text-blue-600">{{ $cs->subject_code }}</span>
                                @if($isMySubject)
                                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-600">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                                        Bạn phụ trách
                                    </span>
                                @endif
                            </div>
                            <h4 class="font-bold text-[#0a2540] text-sm leading-snug group-hover:text-blue-700 transition-colors">{{ $cs->subject_name }}</h4>
                            <p class="text-xs text-slate-500 mt-1.5 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                GV: <span class="font-bold text-slate-700">{{ $cs->teacher_name }}</span>
                            </p>
                        </div>

                        <div>
                            @if($isMySubject)
                                <a href="{{ route('teacher.classes.subjects.documents.index', ['class' => $class->id, 'subject' => $cs->subject_id]) }}"
                                    class="w-full py-2.5 px-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold text-xs rounded-xl shadow-sm shadow-blue-500/20 inline-flex items-center justify-center gap-1.5 transition-all">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    Quản lý tài liệu học tập
                                </a>
                            @else
                                <span class="w-full py-2 px-3 bg-slate-100 text-slate-400 font-semibold text-xs rounded-xl inline-flex items-center justify-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                    Giảng viên khác phụ trách
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Student List -->
    <div class="bg-white/80 backdrop-blur-sm rounded-3xl border border-blue-100 shadow-[0_10px_35px_-10px_rgba(59,130,246,0.08)] overflow-hidden">
        <div class="px-6 py-5 border-b border-blue-50 bg-blue-50/30 flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-[#0a2540]">Danh sách sinh viên trong lớp</h3>
                    <p class="text-xs text-slate-400 font-medium">Tổng số <span class="font-bold text-blue-600 font-mono">{{ $students->count() }}</span> sinh viên</p>
                </div>
            </div>

            <form action="{{ route('teacher.classes.show', $class) }}" method="GET">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Tìm tên, mã SV..."
                        class="pl-9 pr-4 py-2 rounded-xl border border-blue-100 bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-sm text-slate-700 placeholder-slate-400 focus:outline-none transition-all">
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-blue-50/40 border-b border-blue-100 text-[11px] uppercase text-slate-400 font-bold tracking-wider">
                    <tr>
                        <th class="px-6 py-3.5 w-16 text-center">STT</th>
                        <th class="px-6 py-3.5">Mã SV</th>
                        <th class="px-6 py-3.5">Họ và tên</th>
                        <th class="px-6 py-3.5">Email</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-blue-50">
                    @forelse($students as $index => $student)
                        <tr class="hover:bg-blue-50/30 transition-colors">
                            <td class="px-6 py-4 text-center text-slate-400 font-bold font-mono text-xs">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 font-mono font-extrabold text-blue-600 text-xs bg-blue-50/30">{{ $student->code ?? 'N/A' }}</td>
                            <td class="px-6 py-4 font-bold text-[#0a2540]">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-black text-xs shrink-0 shadow-sm">
                                        {{ mb_substr($student->name, 0, 1) }}
                                    </div>
                                    {{ $student->name }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-500 text-xs font-mono">{{ $student->email }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-14 text-center">
                                <div class="text-slate-400 text-sm">Không tìm thấy sinh viên nào trong lớp này.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
