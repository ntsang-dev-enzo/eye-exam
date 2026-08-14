@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold text-slate-800">Lớp: {{ $class->code }} - {{ $class->name }}</h1>
                @if($class->course)
                    <span class="px-3 py-1 bg-indigo-50 text-indigo-700 font-bold text-xs rounded-full border border-indigo-100">
                        {{ $class->course->name }} ({{ $class->course->semester }} - {{ $class->course->academic_year }})
                    </span>
                @endif
            </div>
            <p class="text-slate-500 mt-1">
                Giảng viên chủ nhiệm: <span class="font-bold text-indigo-600">{{ $class->teacher->name ?? 'N/A' }}</span> 
                | Sĩ số: <span class="font-bold text-slate-800">{{ $class->students->count() }} sinh viên</span>
            </p>
        </div>
        <a href="{{ route('admin.classes.index') }}" class="px-4 py-2 bg-white border border-slate-200 text-slate-600 rounded-lg hover:bg-slate-50 transition-colors font-medium text-sm self-start sm:self-auto">
            Quay lại
        </a>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-100 font-medium">
            {{ session('success') }}
        </div>
    @endif
    
    @if($errors->any())
        <div class="p-4 rounded-lg bg-rose-50 text-rose-700 border border-rose-100 font-medium">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <!-- Section 1: Phân công Giảng dạy Môn học -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Assign Form -->
        <div class="md:col-span-1">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <h3 class="font-bold text-slate-800 mb-2 text-lg">Phân công Giảng dạy</h3>
                <p class="text-sm text-slate-500 mb-6">Chỉ định giảng viên phụ trách môn học cụ thể cho lớp này.</p>
                
                <form action="{{ route('admin.classes.assign', $class) }}" method="POST" class="space-y-4">
                    @csrf
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Môn học</label>
                        <select name="subject_id" class="w-full rounded-xl border border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 p-2.5 text-sm" required>
                            <option value="">-- Chọn Môn học --</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->code }} - {{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Giảng viên bộ môn</label>
                        <select name="teacher_id" class="w-full rounded-xl border border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 p-2.5 text-sm" required>
                            <option value="">-- Chọn Giảng viên --</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}">{{ $teacher->name }} ({{ $teacher->code }})</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <button type="submit" class="w-full px-4 py-2.5 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 transition-colors shadow-sm text-sm">
                        Lưu Phân công
                    </button>
                </form>
            </div>
        </div>

        <!-- Assignments List -->
        <div class="md:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-6 border-b border-slate-100">
                    <h3 class="font-bold text-slate-800 text-lg">Danh sách Môn học & Giảng viên phụ trách</h3>
                </div>
                
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="px-6 py-4 text-sm font-semibold text-slate-600">Môn học</th>
                            <th class="px-6 py-4 text-sm font-semibold text-slate-600">Giảng viên phụ trách</th>
                            <th class="px-6 py-4 text-sm font-semibold text-slate-600 text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($assignments as $assignment)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 font-bold text-slate-800">{{ $assignment->subject_name }}</td>
                                <td class="px-6 py-4 font-medium text-indigo-600">{{ $assignment->teacher_name }}</td>
                                <td class="px-6 py-4 text-right">
                                    <form action="{{ route('admin.classes.remove-assign', [$class, $assignment->subject_id]) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa phân công này?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-500 hover:text-rose-700 font-medium text-sm transition-colors">
                                            Xóa
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-12 text-center text-slate-500">
                                    Chưa có môn học nào được phân công cho lớp này.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Section 2: Quản lý Danh sách Sinh viên trong Lớp -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="font-bold text-slate-800 text-lg">Danh sách Sinh viên trong Lớp</h3>
                <p class="text-xs text-slate-500 mt-0.5">Tổng số: {{ $class->students->count() }} sinh viên</p>
            </div>
            
            <!-- Form Thêm sinh viên -->
            <form action="{{ route('admin.classes.add-student', $class) }}" method="POST" class="flex items-center gap-2">
                @csrf
                <select name="student_id" required class="px-3 py-2 border border-slate-300 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500 shadow-sm w-64">
                    <option value="">-- Thêm sinh viên vào lớp --</option>
                    @foreach($availableStudents as $st)
                        <option value="{{ $st->id }}">{{ $st->name }} ({{ $st->code ?? $st->email }})</option>
                    @endforeach
                </select>
                <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm rounded-xl transition-colors shadow-sm whitespace-nowrap">
                    + Thêm SV
                </button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase">STT</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Mã SV</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Họ và Tên</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Email</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($class->students as $index => $student)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 text-sm text-slate-500">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 text-sm font-mono font-bold text-slate-800">{{ $student->code ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-sm font-semibold text-slate-900">{{ $student->name }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $student->email }}</td>
                            <td class="px-6 py-4 text-right">
                                <form action="{{ route('admin.classes.remove-student', [$class, $student->id]) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa sinh viên này khỏi lớp?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-600 hover:text-rose-800 font-medium text-sm transition-colors px-2 py-1 bg-rose-50 rounded-lg">
                                        Xóa khỏi lớp
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-500">
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
