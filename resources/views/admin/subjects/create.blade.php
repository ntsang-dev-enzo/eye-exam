@extends('layouts.admin')

@section('title', 'Thêm Môn Học Mới')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 font-medium mb-1">
                <a href="{{ route('admin.subjects.index') }}" class="hover:text-blue-600 transition-colors">Quản lý Môn học</a>
                <span>/</span>
                <span class="text-slate-800">Thêm mới</span>
            </div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Thêm Môn Học Mới</h1>
        </div>
        <a href="{{ route('admin.subjects.index') }}" 
           class="inline-flex items-center h-9 px-3 bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 font-medium text-xs rounded-md transition-colors">
            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Quay lại
        </a>
    </div>

    <form action="{{ route('admin.subjects.store') }}" method="POST" class="bg-white rounded-lg border border-slate-200 p-6 space-y-5">
        @csrf

        @if($errors->any())
            <div class="bg-rose-50 text-rose-700 p-3.5 rounded-md text-sm border border-rose-200">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <!-- Code -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Mã môn học <span class="text-rose-500">*</span></label>
                <input type="text" name="code" value="{{ old('code') }}" required 
                    class="w-full h-10 px-3 bg-white border border-slate-200 rounded-md text-sm font-medium text-slate-900 uppercase focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-colors" placeholder="VD: CS101">
            </div>
            
            <!-- Name -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Tên môn học <span class="text-rose-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required 
                    class="w-full h-10 px-3 bg-white border border-slate-200 rounded-md text-sm font-medium text-slate-900 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-colors" placeholder="VD: Lập trình C++">
            </div>

            <!-- Credits -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Số tín chỉ (TC) <span class="text-rose-500">*</span></label>
                <input type="number" name="credits" min="1" max="15" value="{{ old('credits', 3) }}" required 
                    class="w-full h-10 px-3 bg-white border border-slate-200 rounded-md text-sm font-medium text-slate-900 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-colors" placeholder="3">
            </div>
        </div>

        <!-- Description -->
        <div>
            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Mô tả học phần</label>
            <textarea name="description" rows="3" 
                class="w-full p-3 bg-white border border-slate-200 rounded-md text-sm font-medium text-slate-900 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-colors" placeholder="Giới thiệu nội dung tóm tắt môn học...">{{ old('description') }}</textarea>
        </div>

        <!-- Teachers Assignment -->
        <div class="space-y-2">
            <div class="flex items-center justify-between">
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Phân công Giảng viên phụ trách</label>
                <input type="text" id="teacher-search" placeholder="Tìm giảng viên, khoa..." 
                    class="h-8 text-xs rounded-md border border-slate-200 bg-white px-2.5 w-48 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600">
            </div>
            <div class="bg-white border border-slate-200 rounded-md max-h-56 overflow-y-auto p-3 divide-y divide-slate-100" id="teachers-list">
                @forelse($departments as $department => $teachers)
                    <div class="department-group py-2.5 first:pt-0 last:pb-0">
                        <h4 class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-2">
                            {{ $department ?: 'Chưa phân khoa' }}
                        </h4>
                        <div class="space-y-1">
                            @foreach($teachers as $teacher)
                                <label class="teacher-item flex items-center gap-3 p-2 hover:bg-slate-50 rounded-md cursor-pointer transition-colors" data-search="{{ strtolower($teacher->name . ' ' . $teacher->email . ' ' . $department) }}">
                                    <input type="checkbox" name="teachers[]" value="{{ $teacher->id }}" 
                                        class="w-4 h-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500 cursor-pointer" 
                                        {{ (is_array(old('teachers')) && in_array($teacher->id, old('teachers'))) ? 'checked' : '' }}>
                                    <div>
                                        <p class="text-sm font-medium text-slate-900">{{ $teacher->name }}</p>
                                        <p class="text-xs text-slate-500">{{ $teacher->email }}</p>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-500 p-2 text-center">Chưa có tài khoản giảng viên nào trong hệ thống.</p>
                @endforelse
            </div>
            <p class="text-[11px] text-slate-500">* Các giảng viên được phân công sẽ có quyền tạo câu hỏi và tổ chức đề thi cho môn học này.</p>
        </div>

        <!-- Status -->
        <div class="flex items-center gap-2.5 pt-1">
            <input type="checkbox" name="status" id="status" value="1" 
                class="w-4 h-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500 cursor-pointer" 
                {{ old('status', true) ? 'checked' : '' }}>
            <label for="status" class="text-sm font-medium text-slate-700 cursor-pointer">
                Kích hoạt môn học (Cho phép sử dụng trong các khóa học và kỳ thi)
            </label>
        </div>

        <div class="pt-4 border-t border-slate-200 flex items-center justify-end gap-3">
            <a href="{{ route('admin.subjects.index') }}" 
               class="h-10 px-4 text-sm font-medium text-slate-700 bg-white border border-slate-200 rounded-md hover:bg-slate-50 transition-colors flex items-center">
                Hủy bỏ
            </a>
            <button type="submit" 
                    class="h-10 px-4 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 transition-colors shadow-xs">
                Lưu môn học
            </button>
        </div>
    </form>
</div>

<script>
    document.getElementById('teacher-search')?.addEventListener('input', function(e) {
        const term = e.target.value.toLowerCase();
        const groups = document.querySelectorAll('.department-group');
        
        groups.forEach(group => {
            const items = group.querySelectorAll('.teacher-item');
            let hasVisibleItem = false;
            
            items.forEach(item => {
                const searchData = item.getAttribute('data-search');
                if (searchData.includes(term)) {
                    item.style.display = 'flex';
                    hasVisibleItem = true;
                } else {
                    item.style.display = 'none';
                }
            });
            
            group.style.display = hasVisibleItem ? 'block' : 'none';
        });
    });
</script>
@endsection
