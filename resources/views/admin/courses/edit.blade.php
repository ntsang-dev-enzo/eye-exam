@extends('layouts.admin')

@section('title', 'Chỉnh Sửa Khóa Học - ' . $course->name)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 text-sm text-slate-500 font-medium mb-1">
                <a href="{{ route('admin.khoa-hoc.index') }}" class="hover:text-indigo-600 transition-colors">Quản lý Khóa học</a>
                <span>/</span>
                <span class="text-slate-800">Chỉnh sửa</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Chỉnh Sửa Khóa Học</h1>
        </div>
        <a href="{{ route('admin.khoa-hoc.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-sm rounded-xl transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Quay lại
        </a>
    </div>

    <!-- Form Container -->
    <form method="POST" action="{{ route('admin.khoa-hoc.update', $course) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- General Info Card -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-6">
            <h2 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                Thông tin khóa học
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Course Name -->
                <div class="md:col-span-2">
                    <label for="name" class="block text-sm font-bold text-slate-700 mb-2">
                        Tên khóa học <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name', $course->name) }}" placeholder="Ví dụ: Học kỳ 1 - 2025-2026" 
                        class="w-full px-4 py-3 bg-slate-50 border @error('name') border-rose-500 focus:ring-rose-500/20 @else border-slate-200 focus:ring-indigo-500/20 @enderror rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:border-indigo-500 focus:ring-2 transition-all">
                    @error('name')
                        <p class="text-xs font-semibold text-rose-500 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Semester -->
                <div>
                    <label for="semester" class="block text-sm font-bold text-slate-700 mb-2">
                        Học kỳ <span class="text-rose-500">*</span>
                    </label>
                    <select id="semester" name="semester" 
                        class="w-full px-4 py-3 bg-slate-50 border @error('semester') border-rose-500 focus:ring-rose-500/20 @else border-slate-200 focus:ring-indigo-500/20 @enderror rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:border-indigo-500 focus:ring-2 transition-all">
                        <option value="">-- Chọn học kỳ --</option>
                        <option value="Học kỳ 1" {{ old('semester', $course->semester) == 'Học kỳ 1' ? 'selected' : '' }}>Học kỳ 1</option>
                        <option value="Học kỳ 2" {{ old('semester', $course->semester) == 'Học kỳ 2' ? 'selected' : '' }}>Học kỳ 2</option>
                        <option value="Học kỳ 3" {{ old('semester', $course->semester) == 'Học kỳ 3' ? 'selected' : '' }}>Học kỳ 3</option>
                    </select>
                    @error('semester')
                        <p class="text-xs font-semibold text-rose-500 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Academic Year -->
                <div>
                    <label for="academic_year" class="block text-sm font-bold text-slate-700 mb-2">
                        Năm học <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="academic_year" name="academic_year" value="{{ old('academic_year', $course->academic_year) }}" placeholder="2025-2026" 
                        class="w-full px-4 py-3 bg-slate-50 border @error('academic_year') border-rose-500 focus:ring-rose-500/20 @else border-slate-200 focus:ring-indigo-500/20 @enderror rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:border-indigo-500 focus:ring-2 transition-all">
                    @error('academic_year')
                        <p class="text-xs font-semibold text-rose-500 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-bold text-slate-700 mb-2">
                        Mô tả khóa học
                    </label>
                    <textarea id="description" name="description" rows="3" placeholder="Nhập mô tả ngắn gọn..." 
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all">{{ old('description', $course->description) }}</textarea>
                </div>

                <!-- Status Select -->
                <div class="md:col-span-2">
                    <label for="status" class="block text-sm font-bold text-slate-700 mb-2">
                        Trạng thái hoạt động
                    </label>
                    <select id="status" name="status" 
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all">
                        <option value="active" {{ old('status', $course->status) == 'active' ? 'selected' : '' }}>Đang hoạt động (Active)</option>
                        <option value="inactive" {{ old('status', $course->status) == 'inactive' ? 'selected' : '' }}>Tạm dừng (Inactive)</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Subject Picker Component -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-4"
             x-data="{ 
                searchQuery: '', 
                selectedCount: {{ count(old('subjects', $selectedSubjectIds)) }}
             }">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div>
                    <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        Môn học trong khóa học <span class="text-rose-500">*</span>
                    </h2>
                    <p class="text-xs text-slate-500 font-medium">Chỉnh sửa danh sách các môn học thuộc khóa học này</p>
                </div>
                <!-- Real-time counter badge -->
                <span class="px-3.5 py-1.5 bg-indigo-50 border border-indigo-100 text-indigo-700 font-black text-xs rounded-full">
                    Đã chọn: <span x-text="selectedCount" class="text-indigo-600"></span> môn học
                </span>
            </div>

            @error('subjects')
                <p class="text-xs font-semibold text-rose-500">{{ $message }}</p>
            @enderror

            <!-- Subject Search Input -->
            <div class="relative">
                <input type="text" x-model="searchQuery" placeholder="🔍 Tìm kiếm môn học theo tên hoặc mã môn..." 
                    class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all">
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>

            <!-- Subject Checkbox List -->
            <div class="max-h-72 overflow-y-auto border border-slate-200/80 rounded-xl divide-y divide-slate-100 bg-slate-50/50 p-2 space-y-1">
                @foreach($subjects as $subject)
                    <label x-show="'{{ strtolower(addslashes($subject->code . ' ' . $subject->name)) }}'.includes(searchQuery.toLowerCase())" 
                        class="flex items-center justify-between p-3 bg-white hover:bg-indigo-50/50 rounded-xl border border-transparent hover:border-indigo-200 cursor-pointer transition-all">
                        <div class="flex items-center gap-3">
                            <input type="checkbox" name="subjects[]" value="{{ $subject->id }}" 
                                {{ in_array($subject->id, old('subjects', $selectedSubjectIds)) ? 'checked' : '' }}
                                @change="selectedCount = document.querySelectorAll('input[name=\'subjects[]\']:checked').length"
                                class="w-5 h-5 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500 cursor-pointer">
                            <div>
                                <span class="font-bold text-slate-800 text-sm block">{{ $subject->name }}</span>
                                <span class="text-xs text-slate-400 font-mono">Mã môn: {{ $subject->code }}</span>
                            </div>
                        </div>
                    </label>
                @endforeach
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex items-center justify-end gap-4 pt-4">
            <a href="{{ route('admin.khoa-hoc.index') }}" class="px-6 py-3 bg-white border border-slate-300 rounded-xl text-slate-700 font-bold text-sm hover:bg-slate-50 transition-colors">
                Hủy bỏ
            </a>
            <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm rounded-xl shadow-lg shadow-indigo-500/30 transition-all active:scale-95">
                Cập Nhật Khóa Học
            </button>
        </div>
    </form>
</div>
@endsection
