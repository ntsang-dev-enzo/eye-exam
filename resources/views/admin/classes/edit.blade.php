@extends('layouts.admin')

@section('title', 'Cập nhật lớp học')

@section('content')
<div class="max-w-2xl mx-auto space-y-4">
    <!-- Breadcrumb -->
    <div class="flex items-center gap-1.5 text-xs text-slate-500">
        <a href="{{ route('admin.classes.index') }}" class="hover:text-blue-600 transition-colors">Quản lý lớp học</a>
        <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-slate-800 font-medium">Chỉnh sửa</span>
    </div>

    <!-- Form Container -->
    <div class="bg-white border border-slate-200 rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200">
            <h1 class="text-base font-bold text-slate-900">Cập nhật lớp học: <span class="font-mono text-blue-600">{{ $class->code }}</span></h1>
            <p class="text-xs text-slate-500 mt-0.5">Thay đổi thông tin lớp học, giảng viên chủ nhiệm hoặc khóa đào tạo</p>
        </div>

        <form action="{{ route('admin.classes.update', $class) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Mã lớp <span class="text-rose-500">*</span></label>
                        <input type="text" 
                               name="code" 
                               value="{{ old('code', $class->code) }}" 
                               required 
                               class="w-full h-10 px-3 text-sm font-mono uppercase bg-white border border-slate-200 rounded-md focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-colors">
                        @error('code') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Trạng thái</label>
                        <select name="status" class="w-full h-10 px-3 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-colors">
                            <option value="active" {{ old('status', $class->status) == 'active' ? 'selected' : '' }}>Hoạt động</option>
                            <option value="inactive" {{ old('status', $class->status) == 'inactive' ? 'selected' : '' }}>Đã khóa</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Tên lớp <span class="text-rose-500">*</span></label>
                    <input type="text" 
                           name="name" 
                           value="{{ old('name', $class->name) }}" 
                           required 
                           class="w-full h-10 px-3 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-colors">
                    @error('name') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Khóa học / Chương trình đào tạo</label>
                    <select name="course_id" class="w-full h-10 px-3 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-colors">
                        <option value="">-- Chọn khóa học (Tùy chọn) --</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ old('course_id', $class->course_id) == $course->id ? 'selected' : '' }}>
                                {{ $course->name }} ({{ $course->semester }} - {{ $course->academic_year }})
                            </option>
                        @endforeach
                    </select>
                    @error('course_id') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Giảng viên chủ nhiệm <span class="text-rose-500">*</span></label>
                    <select name="teacher_id" required class="w-full h-10 px-3 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-colors">
                        <option value="">-- Chọn giảng viên --</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}" {{ old('teacher_id', $class->teacher_id) == $teacher->id ? 'selected' : '' }}>
                                {{ $teacher->name }} ({{ $teacher->code }})
                            </option>
                        @endforeach
                    </select>
                    @error('teacher_id') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Mô tả thêm</label>
                    <textarea name="description" rows="3" class="w-full p-3 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-colors">{{ old('description', $class->description) }}</textarea>
                </div>
            </div>

            <div class="px-6 py-3.5 bg-slate-50 border-t border-slate-200 flex items-center justify-end gap-2.5">
                <a href="{{ route('admin.classes.index') }}" class="h-9 px-4 inline-flex items-center text-xs font-medium text-slate-700 hover:text-slate-900 bg-white border border-slate-200 hover:bg-slate-50 rounded-md transition-colors">
                    Hủy
                </a>
                <button type="submit" class="h-9 px-4 inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-md transition-colors">
                    Cập nhật thay đổi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
