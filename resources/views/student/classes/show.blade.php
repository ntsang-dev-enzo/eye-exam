@extends('layouts.student')

@section('title', 'Chi tiết Lớp học')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('student.classes.index') }}" class="text-sm font-medium text-gray-500 hover:text-indigo-600 transition-colors">Lớp học của tôi</a>
                <span class="text-gray-400">/</span>
                <span class="text-sm font-medium text-gray-900">{{ $class->code }}</span>
            </div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">{{ $class->name }}</h1>
        </div>
        <a href="{{ route('student.classes.index') }}" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-sm rounded-xl transition-colors self-start sm:self-auto">
            Quay lại
        </a>
    </div>

    <!-- Class Details Card -->
    <div class="bg-white rounded-3xl border border-gray-100 p-6 shadow-sm grid grid-cols-1 md:grid-cols-3 gap-6">
        <div>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Mã Lớp</p>
            <p class="text-lg font-black text-indigo-600">{{ $class->code }}</p>
        </div>
        <div>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Khóa học / Học kỳ</p>
            <p class="text-base font-bold text-gray-800">
                {{ $class->course ? $class->course->name . ' (' . $class->course->semester . ')' : 'Chưa cập nhật' }}
            </p>
        </div>
        <div>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Giảng viên Chủ nhiệm</p>
            <p class="text-base font-bold text-gray-900">{{ $class->teacher->name ?? 'N/A' }}</p>
            @if(isset($class->teacher->email))
                <p class="text-xs text-gray-500 font-mono">{{ $class->teacher->email }}</p>
            @endif
        </div>
    </div>

    <!-- Subject Teachers & Documents List -->
    <div class="bg-white rounded-3xl border border-gray-100 p-6 shadow-sm space-y-4">
        <h3 class="font-black text-lg text-gray-900 border-b border-gray-100 pb-3">Môn học & Tài liệu học tập</h3>

        @if($subjectTeachers->isEmpty())
            <div class="py-10 text-center">
                <div class="w-12 h-12 bg-indigo-50 text-indigo-400 rounded-2xl flex items-center justify-center mx-auto mb-3 border border-indigo-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <p class="text-sm text-gray-500">Chưa có môn học nào được phân công cho lớp này.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($subjectTeachers as $st)
                    <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100 space-y-3 flex flex-col justify-between">
                        <div>
                            <span class="text-xs font-mono text-indigo-600 font-bold block mb-0.5">{{ $st->subject_code }}</span>
                            <h4 class="font-bold text-gray-900 text-sm leading-snug">{{ $st->subject_name }}</h4>
                            <p class="text-xs text-gray-500 font-medium mt-1">Giảng viên: <span class="font-bold text-gray-700">{{ $st->teacher_name }}</span></p>
                        </div>
                        <a href="{{ route('student.classes.subjects.documents.index', ['class' => $class->id, 'subject' => $st->subject_id]) }}"
                           class="inline-flex items-center gap-1.5 px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-sm transition-colors w-full justify-center">
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
