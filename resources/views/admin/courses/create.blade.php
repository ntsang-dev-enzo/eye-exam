@extends('layouts.admin')

@section('title', 'Thêm Khóa Học Mới')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 font-medium mb-1">
                <a href="{{ route('admin.khoa-hoc.index') }}" class="hover:text-blue-600 transition-colors">Quản lý Khóa học</a>
                <span>/</span>
                <span class="text-slate-800">Thêm mới</span>
            </div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Thêm Khóa Học Mới</h1>
        </div>
        <a href="{{ route('admin.khoa-hoc.index') }}" 
           class="inline-flex items-center h-9 px-3 bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 font-medium text-xs rounded-md transition-colors">
            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Quay lại
        </a>
    </div>

    <!-- Form Container -->
    <form method="POST" action="{{ route('admin.khoa-hoc.store') }}" class="space-y-6">
        @csrf

        <!-- General Info Card -->
        <div class="bg-white rounded-lg border border-slate-200 p-6 space-y-5">
            <h2 class="text-sm font-semibold text-slate-900 border-b border-slate-200 pb-3">
                Thông tin chung khóa học
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Course Name -->
                <div class="md:col-span-2">
                    <label for="name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Tên khóa học <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Ví dụ: Học kỳ 1 - 2025-2026" 
                        class="w-full h-10 px-3 bg-white border @error('name') border-rose-500 focus:ring-rose-500/20 @else border-slate-200 focus:ring-blue-600 focus:border-blue-600 @enderror rounded-md text-sm font-medium text-slate-900 focus:outline-none focus:ring-1 transition-colors">
                    @error('name')
                        <p class="text-xs font-medium text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Semester -->
                <div>
                    <label for="semester" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Học kỳ <span class="text-rose-500">*</span>
                    </label>
                    <select id="semester" name="semester" 
                        class="w-full h-10 px-3 bg-white border @error('semester') border-rose-500 focus:ring-rose-500/20 @else border-slate-200 focus:ring-blue-600 focus:border-blue-600 @enderror rounded-md text-sm font-medium text-slate-900 focus:outline-none focus:ring-1 transition-colors">
                        <option value="">-- Chọn học kỳ --</option>
                        <option value="Học kỳ 1" {{ old('semester') == 'Học kỳ 1' ? 'selected' : '' }}>Học kỳ 1</option>
                        <option value="Học kỳ 2" {{ old('semester') == 'Học kỳ 2' ? 'selected' : '' }}>Học kỳ 2</option>
                        <option value="Học kỳ 3" {{ old('semester') == 'Học kỳ 3' ? 'selected' : '' }}>Học kỳ 3</option>
                    </select>
                    @error('semester')
                        <p class="text-xs font-medium text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Academic Year -->
                <div>
                    <label for="academic_year" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Năm học <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="academic_year" name="academic_year" value="{{ old('academic_year', '2025-2026') }}" placeholder="2025-2026" 
                        class="w-full h-10 px-3 bg-white border @error('academic_year') border-rose-500 focus:ring-rose-500/20 @else border-slate-200 focus:ring-blue-600 focus:border-blue-600 @enderror rounded-md text-sm font-medium text-slate-900 focus:outline-none focus:ring-1 transition-colors">
                    @error('academic_year')
                        <p class="text-xs font-medium text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label for="description" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Mô tả khóa học
                    </label>
                    <textarea id="description" name="description" rows="3" placeholder="Nhập mô tả ngắn gọn về khóa học..." 
                        class="w-full p-3 bg-white border border-slate-200 rounded-md text-sm font-medium text-slate-900 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-colors">{{ old('description') }}</textarea>
                </div>

                <!-- Status Checkbox Toggle -->
                <div class="md:col-span-2">
                    <label class="flex items-center gap-3 cursor-pointer select-none">
                        <input type="checkbox" name="status" value="active" {{ old('status', 'active') === 'active' ? 'checked' : '' }} 
                            class="w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500 cursor-pointer">
                        <div>
                            <span class="block text-sm font-medium text-slate-900">Kích hoạt khóa học</span>
                            <span class="block text-xs text-slate-500">Khóa học sau khi tạo sẽ ở trạng thái đang hoạt động</span>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <!-- Subject Picker Component -->
        <div class="bg-white rounded-lg border border-slate-200 p-6 space-y-4"
             x-data="{ 
                searchQuery: '', 
                selectedCount: {{ count(old('subjects', [])) }},
                selectedCredits: {{ $subjects->whereIn('id', old('subjects', []))->sum('credits') }},
                updateStats() {
                    const checked = document.querySelectorAll('input[name=\'subjects[]\']:checked');
                    this.selectedCount = checked.length;
                    let total = 0;
                    checked.forEach(el => {
                        total += parseInt(el.getAttribute('data-credits') || 3);
                    });
                    this.selectedCredits = total;
                }
             }"
             x-init="updateStats()">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-200 pb-3 gap-2">
                <div>
                    <h2 class="text-sm font-semibold text-slate-900">
                        Môn học trong khóa học <span class="text-rose-500">*</span>
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">Chọn các môn học thuộc chương trình của khóa học này</p>
                </div>
                <!-- Real-time counter & credits badges -->
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-1 bg-slate-100 text-slate-700 font-medium text-xs rounded border border-slate-200">
                        Đã chọn: <span x-text="selectedCount" class="font-bold text-slate-900"></span> môn
                    </span>
                    <span class="px-2.5 py-1 bg-blue-50 text-blue-700 font-mono font-semibold text-xs rounded border border-blue-200">
                        Tổng: <span x-text="selectedCredits" class="font-bold"></span> TC
                    </span>
                </div>
            </div>

            @error('subjects')
                <p class="text-xs font-medium text-rose-600">{{ $message }}</p>
            @enderror

            <!-- Subject Search Input -->
            <div class="relative">
                <input type="text" x-model="searchQuery" placeholder="Tìm kiếm môn học theo tên hoặc mã môn..." 
                    class="w-full h-10 pl-9 pr-3 bg-white border border-slate-200 rounded-md text-sm font-medium text-slate-900 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-colors">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            <!-- Subject Checkbox List -->
            <div class="max-h-72 overflow-y-auto border border-slate-200 rounded-md divide-y divide-slate-100 bg-white">
                @foreach($subjects as $subject)
                    <label x-show="'{{ strtolower(addslashes($subject->code . ' ' . $subject->name)) }}'.includes(searchQuery.toLowerCase())" 
                        class="flex items-center justify-between p-3 hover:bg-slate-50 cursor-pointer transition-colors">
                        <div class="flex items-center gap-3">
                            <input type="checkbox" name="subjects[]" value="{{ $subject->id }}" 
                                data-credits="{{ $subject->credits ?? 3 }}"
                                {{ in_array($subject->id, old('subjects', [])) ? 'checked' : '' }}
                                @change="updateStats()"
                                class="w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500 cursor-pointer">
                            <div>
                                <span class="font-medium text-slate-900 text-sm block">{{ $subject->name }}</span>
                                <span class="text-xs text-slate-500 font-mono">Mã môn: {{ $subject->code }}</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <span class="px-2 py-0.5 bg-blue-50 text-blue-700 border border-blue-200 font-mono font-semibold text-xs rounded">
                                {{ $subject->credits ?? 3 }} TC
                            </span>
                        </div>
                    </label>
                @endforeach
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('admin.khoa-hoc.index') }}" 
               class="h-10 px-4 bg-white border border-slate-200 hover:bg-slate-50 rounded-md text-slate-700 font-medium text-sm transition-colors flex items-center">
                Hủy bỏ
            </a>
            <button type="submit" 
                    class="h-10 px-4 bg-blue-600 hover:bg-blue-700 text-white font-medium text-sm rounded-md transition-colors shadow-xs">
                Lưu Khóa Học
            </button>
        </div>
    </form>
</div>
@endsection
