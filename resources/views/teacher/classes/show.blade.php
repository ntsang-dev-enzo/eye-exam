@extends('layouts.teacher')

@section('title', 'Chi tiết Lớp học')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('teacher.classes.index') }}" class="text-sm font-medium text-gray-500 hover:text-indigo-600 transition-colors">Quản lý lớp</a>
                <span class="text-gray-400">/</span>
                <span class="text-sm font-medium text-gray-900">{{ $class->code }}</span>
            </div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold text-gray-900">{{ $class->name }}</h1>
                @if($isHomeroom)
                    <span class="px-2.5 py-1 bg-indigo-50 text-indigo-700 font-bold text-xs rounded-full border border-indigo-100">
                        Lớp chủ nhiệm
                    </span>
                @else
                    <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 font-bold text-xs rounded-full border border-emerald-100">
                        Giảng dạy bộ môn
                    </span>
                @endif
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('teacher.assignments.create', ['class_id' => $class->id]) }}" class="inline-flex items-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl shadow-sm transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                Giao đề cho lớp
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-100 font-medium text-sm">
            {{ session('success') }}
        </div>
    @endif

    <!-- Stats or Info -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
        <div>
            <p class="text-xs text-gray-500 mb-1 font-medium">Mã lớp</p>
            <p class="font-bold text-gray-900">{{ $class->code }}</p>
        </div>
        <div>
            <p class="text-xs text-gray-500 mb-1 font-medium">Khóa học / Học kỳ</p>
            <p class="font-bold text-indigo-600">
                {{ $class->course ? $class->course->name . ' (' . $class->course->semester . ')' : 'Chưa gắn khóa học' }}
            </p>
        </div>
        <div>
            <p class="text-xs text-gray-500 mb-1 font-medium">Sĩ số</p>
            <p class="font-bold text-gray-900">{{ $students->count() }} sinh viên</p>
        </div>
    </div>

    <!-- Section: Môn học & Tài liệu học tập -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
        <div class="flex items-center justify-between border-b border-gray-100 pb-3">
            <div>
                <h3 class="text-lg font-bold text-gray-800">Môn học & Tài liệu học tập</h3>
                <p class="text-xs text-gray-500">Danh sách các môn học được phân công giảng dạy trong lớp</p>
            </div>
            <span class="text-xs font-mono font-bold text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-lg border border-indigo-100">
                {{ $classSubjects->count() }} môn học
            </span>
        </div>

        @if($classSubjects->isEmpty())
            <p class="text-xs text-gray-400 py-4 text-center italic">Chưa có môn học nào được phân công cho lớp này.</p>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($classSubjects as $cs)
                    @php
                        $isMySubject = ($cs->teacher_id == auth()->id() || auth()->user()->role === 'admin');
                    @endphp
                    <div class="p-4 rounded-xl border {{ $isMySubject ? 'border-indigo-100 bg-indigo-50/20' : 'border-gray-100 bg-gray-50/50' }} flex flex-col justify-between space-y-3">
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-xs font-mono font-bold text-indigo-600">{{ $cs->subject_code }}</span>
                                @if($isMySubject)
                                    <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-100">
                                        Bạn phụ trách
                                    </span>
                                @endif
                            </div>
                            <h4 class="font-bold text-gray-900 text-sm leading-snug">{{ $cs->subject_name }}</h4>
                            <p class="text-xs text-gray-500 mt-1">Giảng viên: <span class="font-semibold text-gray-700">{{ $cs->teacher_name }}</span></p>
                        </div>

                        <div>
                            @if($isMySubject)
                                <a href="{{ route('teacher.classes.subjects.documents.index', ['class' => $class->id, 'subject' => $cs->subject_id]) }}" class="w-full py-2 px-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-xs transition-colors inline-flex items-center justify-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    Quản lý tài liệu học tập
                                </a>
                            @else
                                <span class="w-full py-1.5 px-3 bg-gray-100 text-gray-400 font-semibold text-xs rounded-xl inline-flex items-center justify-center">
                                    Giảng viên khác phụ trách
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Student List Section -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden space-y-4">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex flex-col md:flex-row justify-between items-center gap-4">
            <div>
                <h3 class="text-lg font-bold text-gray-800">Danh sách sinh viên trong lớp</h3>
                <p class="text-xs text-gray-500">Tổng số {{ $students->count() }} sinh viên</p>
            </div>
            
            <div class="flex items-center gap-3">
                <form action="{{ route('teacher.classes.show', $class) }}" method="GET" class="flex items-center">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm tên, mã SV..." class="px-3 py-2 rounded-xl border border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 text-sm shadow-sm">
                </form>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 border-b border-gray-100 text-xs uppercase text-gray-500 font-semibold">
                    <tr>
                        <th class="px-6 py-3 w-16 text-center">STT</th>
                        <th class="px-6 py-3">Mã SV</th>
                        <th class="px-6 py-3">Họ và tên</th>
                        <th class="px-6 py-3">Email</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($students as $index => $student)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 text-center text-gray-400 font-medium">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 font-mono font-bold text-gray-900">{{ $student->code ?? 'N/A' }}</td>
                            <td class="px-6 py-4 font-bold text-gray-900">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-black text-xs">
                                        {{ mb_substr($student->name, 0, 1) }}
                                    </div>
                                    {{ $student->name }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-600">{{ $student->email }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                Không tìm thấy sinh viên nào trong lớp này.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
