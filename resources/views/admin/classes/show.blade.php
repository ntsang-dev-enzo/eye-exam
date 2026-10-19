@extends('layouts.admin')

@section('title', 'Quản lý Sinh viên Lớp học phần: ' . $class->name)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.classes.index') }}" class="text-slate-500 hover:text-slate-800 text-sm">← Quay lại danh sách lớp</a>
            </div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight mt-1">Lớp học phần: {{ $class->name }}</h1>
            <p class="text-sm text-slate-500 font-mono">{{ $class->code }} • {{ $class->semester->name ?? '' }} ({{ $class->semester->academic_year ?? '' }})</p>
        </div>
        <div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $class->students->count() >= $class->max_students ? 'bg-rose-100 text-rose-800' : 'bg-blue-100 text-blue-800' }}">
                Sĩ số: {{ $class->students->count() }} / {{ $class->max_students }} sinh viên
            </span>
        </div>
    </div>

    @if(session('success'))
        <div class="p-3.5 bg-emerald-50 border border-emerald-200 rounded-md flex items-center justify-between text-emerald-800 text-sm font-medium">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                <span>{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">&times;</button>
        </div>
    @endif

    @if(session('error'))
        <div class="p-3.5 bg-rose-50 border border-rose-200 rounded-md flex items-center justify-between text-rose-800 text-sm font-medium">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <span>{{ session('error') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700">&times;</button>
        </div>
    @endif

    <!-- Thông tin tổng quan lớp -->
    <div class="bg-white rounded-lg border border-slate-200 p-4 grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
        <div>
            <div class="text-xs text-slate-400 font-medium">Môn học</div>
            <div class="font-semibold text-slate-800">{{ $class->subject->name ?? '---' }}</div>
            <div class="text-xs text-blue-600 font-mono">{{ $class->subject->code ?? '' }} ({{ $class->subject->credits ?? 0 }} TC)</div>
        </div>
        <div>
            <div class="text-xs text-slate-400 font-medium">Giảng viên đứng lớp</div>
            <div class="font-semibold text-slate-800">{{ $class->teacher->name ?? 'Chưa phân công' }}</div>
            <div class="text-xs text-slate-500">{{ $class->teacher->email ?? '' }}</div>
        </div>
        <div>
            <div class="text-xs text-slate-400 font-medium">Trạng thái lớp</div>
            <div>
                @if($class->status === 'open')
                    <span class="inline-flex items-center gap-1.5 text-xs font-medium text-emerald-600">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                        Đang mở ĐK
                    </span>
                @elseif($class->status === 'ongoing')
                    <span class="inline-flex items-center gap-1.5 text-xs font-medium text-blue-600">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500 shrink-0"></span>
                        Đang diễn ra
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-500">
                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400 shrink-0"></span>
                        Đã kết thúc
                    </span>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Danh sách sinh viên đã ghi danh (2 cột) -->
        <div class="lg:col-span-2 space-y-4">
            <div class="bg-white rounded-lg border border-slate-200 overflow-hidden">
                <div class="p-4 border-b border-slate-200 flex items-center justify-between">
                    <h3 class="font-bold text-slate-900 text-sm">Danh sách Sinh viên chính thức ({{ $class->students->count() }})</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200 bg-slate-50 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                                <th class="py-2.5 px-3">STT</th>
                                <th class="py-2.5 px-3">Mã SV</th>
                                <th class="py-2.5 px-3">Họ và tên</th>
                                <th class="py-2.5 px-3">Email</th>
                                <th class="py-2.5 px-3 text-right">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse($class->students as $index => $student)
                                <tr class="hover:bg-slate-50/70 transition-colors">
                                    <td class="py-2.5 px-3 text-slate-400 text-xs">{{ $index + 1 }}</td>
                                    <td class="py-2.5 px-3 font-mono font-semibold text-slate-800 text-xs">{{ $student->code ?? '---' }}</td>
                                    <td class="py-2.5 px-3 font-medium text-slate-900">{{ $student->name }}</td>
                                    <td class="py-2.5 px-3 text-slate-500 text-xs">{{ $student->email }}</td>
                                    <td class="py-2.5 px-3 text-right">
                                        <form action="{{ route('admin.classes.remove-student', [$class, $student]) }}" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc muốn xóa sinh viên này khỏi lớp?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-rose-600 hover:text-rose-800 text-xs font-medium">Xóa</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-6 text-center text-slate-400 text-xs">Chưa có sinh viên nào trong lớp này.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Panel Ghi danh & Import sinh viên (1 cột) -->
        <div class="space-y-6">
            <!-- Ghi danh sinh viên thủ công -->
            <div class="bg-white rounded-lg border border-slate-200 p-4 space-y-4">
                <h3 class="font-bold text-slate-900 text-sm">Thêm sinh viên vào lớp</h3>
                
                @if($class->students->count() >= $class->max_students)
                    <div class="p-3 bg-amber-50 border border-amber-200 rounded text-amber-800 text-xs">
                        Lớp học phần này đã đạt sĩ số tối đa ({{ $class->max_students }}). Vui lòng nâng sĩ số tối đa nếu muốn thêm tiếp.
                    </div>
                @else
                    <form action="{{ route('admin.classes.enroll', $class) }}" method="POST" class="space-y-3">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Chọn sinh viên khả dụng</label>
                            <select name="student_ids[]" multiple size="6" required class="w-full p-2 border border-slate-200 rounded-md text-xs bg-white focus:border-blue-600 focus:outline-none">
                                @forelse($availableStudents as $st)
                                    <option value="{{ $st->id }}">{{ $st->code }} - {{ $st->name }} ({{ $st->email }})</option>
                                @empty
                                    <option value="" disabled>Tất cả sinh viên đều đã có trong lớp</option>
                                @endforelse
                            </select>
                            <p class="text-[11px] text-slate-400 mt-1">Giữ Ctrl / Cmd để chọn nhiều sinh viên cùng lúc.</p>
                        </div>
                        <button type="submit" class="w-full py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-md shadow-xs transition-colors">
                            Ghi danh vào lớp
                        </button>
                    </form>
                @endif
            </div>

            <!-- Import nhanh theo danh sách mã / file -->
            <div class="bg-white rounded-lg border border-slate-200 p-4 space-y-4">
                <h3 class="font-bold text-slate-900 text-sm">Import theo Mã SV</h3>
                <form action="{{ route('admin.classes.import-students', $class) }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Dán danh sách mã SV (hoặc email)</label>
                        <textarea name="student_codes" rows="4" placeholder="VD:&#10;SV001&#10;SV002&#10;student3@school.edu.vn" class="w-full p-2 border border-slate-200 rounded-md text-xs font-mono focus:border-blue-600 focus:outline-none"></textarea>
                        <p class="text-[11px] text-slate-400 mt-1">Mỗi mã một dòng hoặc cách nhau bằng dấu phẩy.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Hoặc tải lên file CSV/TXT</label>
                        <input type="file" name="file" accept=".csv,.txt" class="block w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200">
                    </div>

                    <button type="submit" class="w-full py-2 bg-slate-800 hover:bg-slate-900 text-white text-xs font-medium rounded-md shadow-xs transition-colors">
                        Xác nhận Import
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
