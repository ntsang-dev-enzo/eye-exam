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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Subject Teachers List -->
        <div class="lg:col-span-1 bg-white rounded-3xl border border-gray-100 p-6 shadow-sm space-y-4">
            <h3 class="font-black text-lg text-gray-900 border-b border-gray-100 pb-3">Môn học & Giảng viên Bộ môn</h3>
            
            @forelse($subjectTeachers as $st)
                <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100 space-y-1">
                    <span class="text-xs font-mono text-indigo-600 font-bold block">{{ $st->subject_code }}</span>
                    <h4 class="font-bold text-gray-900 text-sm">{{ $st->subject_name }}</h4>
                    <p class="text-xs text-gray-600 font-medium">Giảng viên: <span class="font-bold text-gray-800">{{ $st->teacher_name }}</span></p>
                </div>
            @empty
                <p class="text-sm text-gray-500 py-4 text-center">Chưa có môn học nào được phân công.</p>
            @endforelse
        </div>

        <!-- Classmates List -->
        <div class="lg:col-span-2 bg-white rounded-3xl border border-gray-100 p-6 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <h3 class="font-black text-lg text-gray-900">Danh sách Bạn cùng lớp</h3>
                <span class="px-3 py-1 bg-indigo-50 text-indigo-700 font-bold text-xs rounded-full border border-indigo-100">
                    Sĩ số: {{ $class->students->count() }} sinh viên
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-400 font-bold">
                        <tr>
                            <th class="px-4 py-3 w-12 text-center">STT</th>
                            <th class="px-4 py-3">Mã SV</th>
                            <th class="px-4 py-3">Họ và tên</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($class->students as $index => $student)
                            <tr class="hover:bg-gray-50/50 transition-colors {{ $student->id === auth()->id() ? 'bg-indigo-50/40 font-bold' : '' }}">
                                <td class="px-4 py-3 text-center text-gray-400 text-xs">{{ $index + 1 }}</td>
                                <td class="px-4 py-3 font-mono text-xs font-bold text-gray-800">{{ $student->code ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-gray-900 font-medium flex items-center gap-2">
                                    {{ $student->name }}
                                    @if($student->id === auth()->id())
                                        <span class="px-2 py-0.5 bg-indigo-600 text-white font-bold text-[10px] rounded-full">Bạn</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-8 text-center text-gray-400">Không có danh sách bạn cùng lớp.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
