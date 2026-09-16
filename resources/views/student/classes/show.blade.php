@extends('layouts.student')

@section('title', 'Chi tiết Lớp học')

@section('content')
<div class="space-y-6 sm:space-y-8">

    <!-- Breadcrumb & Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="space-y-1.5">
            <!-- Breadcrumb -->
            <div class="flex items-center gap-2 text-xs">
                <a href="{{ route('student.classes.index') }}"
                    class="font-semibold text-blue-600 hover:text-blue-800 hover:underline transition-colors flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    Lớp học của tôi
                </a>
                <svg class="w-3.5 h-3.5 text-slate-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path></svg>
                <span class="font-bold text-slate-500 font-mono">{{ $class->code }}</span>
            </div>

            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 border border-blue-100 text-blue-700 text-xs font-bold tracking-wide uppercase">
                <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
                Chi tiết lớp học
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-[#0a2540] tracking-tight">{{ $class->name }}</h1>
        </div>

        <a href="{{ route('student.classes.index') }}"
            class="px-4 py-2.5 bg-white/80 backdrop-blur-sm hover:bg-blue-50 text-slate-600 hover:text-blue-700 font-bold text-sm rounded-xl border border-blue-100 hover:border-blue-300 shadow-sm transition-all self-start sm:self-auto flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Quay lại
        </a>
    </div>

    <!-- Class Details Card -->
    <div class="bg-white/80 backdrop-blur-sm rounded-3xl border border-blue-100 p-6 sm:p-8 shadow-[0_10px_35px_-10px_rgba(59,130,246,0.08)]">
        <div class="flex items-center gap-3 mb-6 pb-5 border-b border-blue-50">
            <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white shadow-md shadow-blue-500/20">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <h2 class="text-lg font-extrabold text-[#0a2540] tracking-tight">Thông tin lớp học</h2>
                <p class="text-xs text-slate-400 font-medium">Thông tin chi tiết về lớp học và giảng viên</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="p-4 bg-blue-50/60 rounded-2xl border border-blue-100">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Mã Lớp</p>
                <p class="text-xl font-black text-blue-600 font-mono">{{ $class->code }}</p>
            </div>
            <div class="p-4 bg-blue-50/60 rounded-2xl border border-blue-100">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Khóa học / Học kỳ</p>
                <p class="text-sm font-bold text-[#0a2540] leading-snug">
                    {{ $class->course ? $class->course->name . ' (' . $class->course->semester . ')' : 'Chưa cập nhật' }}
                </p>
            </div>
            <div class="p-4 bg-blue-50/60 rounded-2xl border border-blue-100">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Giảng viên Chủ nhiệm</p>
                <p class="text-sm font-bold text-[#0a2540]">{{ $class->teacher->name ?? 'N/A' }}</p>
                @if(isset($class->teacher->email))
                    <p class="text-xs text-blue-600 font-mono mt-0.5">{{ $class->teacher->email }}</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Subject Teachers & Documents -->
    <div class="bg-white/80 backdrop-blur-sm rounded-3xl border border-blue-100 p-6 sm:p-8 shadow-[0_10px_35px_-10px_rgba(59,130,246,0.08)] space-y-5">

        <!-- Section Header -->
        <div class="flex items-center justify-between border-b border-blue-50 pb-5">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                </div>
                <div>
                    <h3 class="text-lg font-extrabold text-[#0a2540] tracking-tight">Môn học & Tài liệu học tập</h3>
                    <p class="text-xs text-slate-400 font-medium">Các học phần và tài liệu được phân công cho lớp</p>
                </div>
            </div>
            @if(!$subjectTeachers->isEmpty())
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-100 font-mono">
                    {{ $subjectTeachers->count() }} môn học
                </span>
            @endif
        </div>

        @if($subjectTeachers->isEmpty())
            <div class="py-12 text-center">
                <div class="w-16 h-16 bg-gradient-to-tr from-blue-50 to-indigo-50 text-blue-400 rounded-3xl flex items-center justify-center mx-auto mb-4 border border-blue-100">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <h4 class="text-sm font-bold text-[#0a2540] mb-1">Chưa có môn học nào</h4>
                <p class="text-xs text-slate-500">Chưa có môn học nào được phân công cho lớp này.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($subjectTeachers as $st)
                    <div class="bg-gradient-to-b from-white via-white to-blue-50/30 rounded-2xl border border-blue-100 p-4 space-y-3 flex flex-col justify-between hover:shadow-md hover:shadow-blue-500/10 hover:border-blue-300 hover:-translate-y-0.5 transition-all duration-200 group">
                        <div>
                            <span class="text-xs font-mono text-blue-600 font-extrabold block mb-1 bg-blue-50 px-2 py-0.5 rounded-lg border border-blue-100 w-fit">{{ $st->subject_code }}</span>
                            <h4 class="font-bold text-[#0a2540] text-sm leading-snug group-hover:text-blue-700 transition-colors">{{ $st->subject_name }}</h4>
                            <p class="text-xs text-slate-500 font-medium mt-1.5 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                GV: <span class="font-bold text-slate-700">{{ $st->teacher_name }}</span>
                            </p>
                        </div>
                        <a href="{{ route('student.classes.subjects.documents.index', ['class' => $class->id, 'subject' => $st->subject_id]) }}"
                           class="inline-flex items-center gap-1.5 px-3 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold text-xs rounded-xl shadow-sm shadow-blue-500/20 hover:shadow-md hover:shadow-blue-500/30 transition-all w-full justify-center">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Xem tài liệu học tập
                        </a>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>
@endsection
