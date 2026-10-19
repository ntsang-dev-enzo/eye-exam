@extends('layouts.admin')

@section('title', 'Thêm môn học mới')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Thêm Môn học mới</h1>
            <p class="text-sm text-slate-500 mt-0.5">Khởi tạo học phần và phân công danh sách giảng viên phụ trách</p>
        </div>
        <a href="{{ route('admin.subjects.index') }}" class="text-sm text-slate-600 hover:text-slate-900">Quay lại</a>
    </div>

    @if($errors->any())
        <div class="p-3.5 bg-rose-50 border border-rose-200 rounded-md text-rose-800 text-sm">
            <ul class="list-disc pl-5 space-y-1">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.subjects.store') }}" method="POST" class="bg-white rounded-lg border border-slate-200 p-6 space-y-5">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Mã môn học <span class="text-rose-500">*</span></label>
                <input type="text" name="code" value="{{ old('code') }}" required placeholder="VD: CS101, IT202..." 
                    class="w-full h-10 px-3 border border-slate-200 rounded-md text-sm uppercase font-mono focus:border-blue-600 focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Số tín chỉ <span class="text-rose-500">*</span></label>
                <input type="number" name="credits" value="{{ old('credits', 3) }}" min="1" max="15" required 
                    class="w-full h-10 px-3 border border-slate-200 rounded-md text-sm focus:border-blue-600 focus:outline-none">
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">Tên môn học <span class="text-rose-500">*</span></label>
            <input type="text" name="name" value="{{ old('name') }}" required placeholder="VD: Lập trình Web nâng cao..." 
                class="w-full h-10 px-3 border border-slate-200 rounded-md text-sm focus:border-blue-600 focus:outline-none">
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">Ngành trực thuộc <span class="text-rose-500">*</span></label>
            <select name="department_id" required class="w-full h-10 px-3 border border-slate-200 rounded-md text-sm bg-white focus:border-blue-600 focus:outline-none">
                <option value="">-- Chọn Ngành quản lý môn học này --</option>
                @foreach($faculties as $f)
                    <optgroup label="Khoa {{ $f->name }}">
                        @foreach($f->departments as $d)
                            <option value="{{ $d->id }}" {{ old('department_id') == $d->id ? 'selected' : '' }}>
                                {{ $d->name }} ({{ $d->code }})
                            </option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">Mô tả học phần</label>
            <textarea name="description" rows="3" placeholder="Tóm tắt nội dung môn học..." class="w-full p-3 border border-slate-200 rounded-md text-sm focus:border-blue-600 focus:outline-none">{{ old('description') }}</textarea>
        </div>

        <!-- Phân công giảng viên có chuyên môn dạy môn học này -->
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">Giảng viên đủ điều kiện giảng dạy môn này</label>
            <p class="text-xs text-slate-500 mb-2">Chỉ các giảng viên được tích chọn ở đây mới có thể được phân công đứng lớp học phần của môn này.</p>
            <div class="max-h-56 overflow-y-auto border border-slate-200 rounded-md p-3 divide-y divide-slate-100 bg-slate-50/50">
                @forelse($teachers as $teacher)
                    <label class="flex items-center gap-2.5 py-1.5 cursor-pointer hover:bg-slate-100/50 px-1 rounded">
                        <input type="checkbox" name="teachers[]" value="{{ $teacher->id }}" 
                            {{ in_array($teacher->id, old('teachers', [])) ? 'checked' : '' }}
                            class="w-4 h-4 text-blue-600 rounded border-slate-300">
                        <div class="text-sm">
                            <span class="font-medium text-slate-900">{{ $teacher->name }}</span>
                            <span class="text-xs text-slate-500">({{ $teacher->code ?? $teacher->email }})</span>
                        </div>
                    </label>
                @empty
                    <p class="text-xs text-slate-400 py-2 text-center">Chưa có tài khoản Giảng viên nào trong hệ thống.</p>
                @endforelse
            </div>
        </div>

        <div class="pt-2">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="status" value="1" {{ old('status', '1') == '1' ? 'checked' : '' }} class="w-4 h-4 text-blue-600 rounded border-slate-300">
                <span class="text-sm font-medium text-slate-700">Kích hoạt trạng thái hoạt động của môn học</span>
            </label>
        </div>

        <div class="pt-4 flex justify-end gap-3 border-t border-slate-100">
            <a href="{{ route('admin.subjects.index') }}" class="px-4 py-2 border border-slate-200 text-slate-700 text-sm rounded-md hover:bg-slate-50">Hủy</a>
            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md shadow-xs">Lưu Môn học</button>
        </div>
    </form>
</div>
@endsection
