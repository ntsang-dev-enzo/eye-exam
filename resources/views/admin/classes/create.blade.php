@extends('layouts.admin')

@section('title', 'Thêm mới Lớp học')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="flex items-center gap-2 mb-6">
        <a href="{{ route('admin.classes.index') }}" class="text-sm font-medium text-slate-500 hover:text-indigo-600 transition-colors">Quản lý lớp học</a>
        <span class="text-slate-400">/</span>
        <span class="text-sm font-medium text-slate-900">Thêm mới</span>
    </div>

    <form action="{{ route('admin.classes.store') }}" method="POST" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        @csrf
        <div class="px-8 py-6 border-b border-slate-200 bg-slate-50/50">
            <h3 class="text-lg font-bold text-slate-800">Thông tin Lớp học</h3>
        </div>

        <div class="p-8 space-y-6">
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Mã lớp <span class="text-rose-500">*</span></label>
                    <input type="text" name="code" value="{{ old('code') }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm text-sm uppercase" placeholder="VD: L01">
                    @error('code') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Trạng thái</label>
                    <select name="status" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm text-sm">
                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Hoạt động</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Đã khóa</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Tên lớp <span class="text-rose-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm text-sm" placeholder="Nhập tên lớp học">
                @error('name') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Giảng viên chủ nhiệm <span class="text-rose-500">*</span></label>
                <select name="teacher_id" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm text-sm">
                    <option value="">-- Chọn giảng viên --</option>
                    @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>
                            {{ $teacher->name }} ({{ $teacher->code }})
                        </option>
                    @endforeach
                </select>
                @error('teacher_id') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Mô tả thêm</label>
                <textarea name="description" rows="3" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm text-sm" placeholder="Ghi chú về lớp học này...">{{ old('description') }}</textarea>
            </div>
        </div>

        <div class="px-8 py-5 border-t border-slate-200 bg-slate-50 flex items-center justify-end gap-3">
            <a href="{{ route('admin.classes.index') }}" class="px-5 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-200 rounded-xl transition-colors">Hủy</a>
            <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl shadow-sm transition-colors">Lưu lớp học</button>
        </div>
    </form>
</div>
@endsection
