@extends('layouts.admin')

@section('title', 'Chi tiết lớp ' . $class->code)

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb & Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="space-y-1">
            <div class="flex items-center gap-1.5 text-xs text-slate-500">
                <a href="{{ route('admin.classes.index') }}" class="hover:text-blue-600 transition-colors">Quản lý lớp học</a>
                <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-slate-800 font-medium">Chi tiết lớp học</span>
            </div>
            <div class="flex flex-wrap items-center gap-2.5">
                <h1 class="text-xl font-bold text-slate-900 tracking-tight">Lớp: {{ $class->code }} - {{ $class->name }}</h1>
                @if($class->course)
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                        {{ $class->course->name }} ({{ $class->course->semester }})
                    </span>
                @endif
            </div>
            <p class="text-xs text-slate-500">
                GV Chủ nhiệm: <strong class="text-slate-700">{{ $class->teacher->name ?? 'N/A' }}</strong> 
                <span class="mx-1 text-slate-300">•</span>
                Sĩ số: <strong class="text-slate-700">{{ $class->students->count() }} sinh viên</strong>
            </p>
        </div>
        <div>
            <a href="{{ route('admin.classes.index') }}" class="h-9 px-3.5 inline-flex items-center gap-1.5 text-xs font-medium text-slate-700 hover:text-slate-900 bg-white border border-slate-200 hover:bg-slate-50 rounded-md transition-colors">
                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Quay lại
            </a>
        </div>
    </div>

    <!-- Feedback Alerts -->
    @if(session('success'))
        <div class="p-3.5 rounded-md bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif
    
    @if($errors->any())
        <div class="p-3.5 rounded-md bg-rose-50 border border-rose-200 text-rose-700 text-sm space-y-1">
            @foreach($errors->all() as $error)
                <p class="flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                    {{ $error }}
                </p>
            @endforeach
        </div>
    @endif

    <!-- Section 1: Phân công Giảng dạy Môn học -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 items-start">
        <!-- Form phân công -->
        <div class="lg:col-span-1 bg-white border border-slate-200 rounded-lg p-5">
            <div class="mb-4">
                <h2 class="text-sm font-bold text-slate-900">Phân công giảng dạy</h2>
                <p class="text-xs text-slate-500 mt-0.5">Chỉ định giảng viên phụ trách từng môn học cho lớp này</p>
            </div>
            
            <form action="{{ route('admin.classes.assign', $class) }}" method="POST" class="space-y-3.5">
                @csrf
                
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Môn học</label>
                    <select name="subject_id" class="w-full h-9 px-3 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-colors" required>
                        <option value="">-- Chọn môn học --</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->code }} - {{ $subject->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Giảng viên phụ trách</label>
                    <select name="teacher_id" class="w-full h-9 px-3 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-colors" required>
                        <option value="">-- Chọn giảng viên --</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}">{{ $teacher->name }} ({{ $teacher->code }})</option>
                        @endforeach
                    </select>
                </div>
                
                <button type="submit" class="w-full h-9 inline-flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-md transition-colors">
                    Lưu phân công
                </button>
            </form>
        </div>

        <!-- Bảng danh sách phân công -->
        <div class="lg:col-span-2 bg-white border border-slate-200 rounded-lg overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-200 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Môn học đã phân công</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Danh sách giảng viên được giao quyền quản lý đề và chấm thi</p>
                </div>
                <span class="text-xs text-slate-500 font-mono">{{ $assignments->count() }} môn</span>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/75 border-b border-slate-200 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                            <th class="py-2.5 px-4">Môn học</th>
                            <th class="py-2.5 px-4">Giảng viên phụ trách</th>
                            <th class="py-2.5 px-4 text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @forelse($assignments as $assignment)
                            <tr class="hover:bg-slate-50/75 transition-colors">
                                <td class="py-3 px-4 font-medium text-slate-900">
                                    {{ $assignment->subject_name }}
                                </td>
                                <td class="py-3 px-4 text-slate-700 text-xs">
                                    <span class="font-medium text-blue-700">{{ $assignment->teacher_name }}</span>
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <form action="{{ route('admin.classes.remove-assign', [$class, $assignment->subject_id]) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn hủy phân công môn học này?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs font-medium text-slate-400 hover:text-rose-600 transition-colors">
                                            Hủy phân công
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-8 px-4 text-center text-xs text-slate-500">
                                    Chưa có môn học nào được phân công cho lớp này.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Section 2: Danh sách Sinh viên trong Lớp -->
    <div class="bg-white border border-slate-200 rounded-lg overflow-hidden">
        <div class="p-4 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-sm font-bold text-slate-900">Danh sách sinh viên trong lớp</h2>
                <p class="text-xs text-slate-500 mt-0.5">Sĩ số hiện tại: {{ $class->students->count() }} sinh viên</p>
            </div>
            
            <!-- Form Thêm sinh viên -->
            <form action="{{ route('admin.classes.add-student', $class) }}" method="POST" class="flex items-center gap-2">
                @csrf
                <select name="student_id" required class="h-9 px-3 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-colors min-w-[240px]">
                    <option value="">-- Thêm sinh viên vào lớp --</option>
                    @foreach($availableStudents as $st)
                        <option value="{{ $st->id }}">{{ $st->name }} ({{ $st->code ?? $st->email }})</option>
                    @endforeach
                </select>
                <button type="submit" class="h-9 px-3.5 inline-flex items-center gap-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-md transition-colors shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Thêm SV
                </button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/75 border-b border-slate-200 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                        <th class="py-3 px-4 w-12 text-center">STT</th>
                        <th class="py-3 px-4">Mã sinh viên</th>
                        <th class="py-3 px-4">Họ và tên</th>
                        <th class="py-3 px-4">Email</th>
                        <th class="py-3 px-4 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($class->students as $index => $student)
                        <tr class="hover:bg-slate-50/75 transition-colors">
                            <td class="py-3 px-4 text-xs text-slate-400 text-center font-mono">{{ $index + 1 }}</td>
                            <td class="py-3 px-4 font-mono text-xs font-semibold text-slate-800">
                                <span class="px-2 py-0.5 bg-slate-100 border border-slate-200 rounded">
                                    {{ $student->code ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 font-medium text-slate-900">{{ $student->name }}</td>
                            <td class="py-3 px-4 text-slate-500 text-xs">{{ $student->email }}</td>
                            <td class="py-3 px-4 text-right">
                                <form action="{{ route('admin.classes.remove-student', [$class, $student->id]) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa sinh viên này khỏi lớp?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-medium text-rose-600 hover:text-rose-700 transition-colors">
                                        Xóa khỏi lớp
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-10 px-4 text-center text-slate-500 text-xs">
                                Chưa có sinh viên nào trong lớp này. Vui lòng chọn sinh viên để thêm vào lớp.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
