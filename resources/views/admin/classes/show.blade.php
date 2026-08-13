@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Chi tiết Lớp học: {{ $class->code }}</h1>
            <p class="text-slate-500 mt-1">Tên lớp: {{ $class->name }} | Giảng viên chủ nhiệm: <span class="font-bold text-indigo-600">{{ $class->teacher->name ?? 'N/A' }}</span></p>
        </div>
        <a href="{{ route('admin.classes.index') }}" class="px-4 py-2 bg-white border border-slate-200 text-slate-600 rounded-lg hover:bg-slate-50 transition-colors font-medium text-sm">
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

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Assign Form -->
        <div class="md:col-span-1">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 sticky top-6">
                <h3 class="font-bold text-slate-800 mb-4 text-lg">Phân công Giảng dạy</h3>
                <p class="text-sm text-slate-500 mb-6">Chỉ định một giảng viên phụ trách môn học cụ thể cho lớp này.</p>
                
                <form action="{{ route('admin.classes.assign', $class) }}" method="POST" class="space-y-4">
                    @csrf
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Môn học</label>
                        <select name="subject_id" class="w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 p-3" required>
                            <option value="">-- Chọn Môn học --</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->code }} - {{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Giảng viên</label>
                        <select name="teacher_id" class="w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 p-3" required>
                            <option value="">-- Chọn Giảng viên --</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}">{{ $teacher->name }} ({{ $teacher->code }})</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <button type="submit" class="w-full px-4 py-3 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 transition-colors shadow-sm">
                        Lưu Phân công
                    </button>
                </form>
            </div>
        </div>

        <!-- Assignments List -->
        <div class="md:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-6 border-b border-slate-100">
                    <h3 class="font-bold text-slate-800 text-lg">Danh sách Phân công</h3>
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
</div>
@endsection
