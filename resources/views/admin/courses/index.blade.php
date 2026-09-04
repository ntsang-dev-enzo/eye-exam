@extends('layouts.admin')

@section('title', 'Quản lý Khóa học')

@section('content')
<div class="space-y-6">
    <!-- Top Bar: Title & Primary Action -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Danh sách Khóa học</h1>
            <p class="text-sm text-slate-500 mt-0.5">Quản lý các khóa học, học kỳ và phân bổ chương trình đào tạo</p>
        </div>
        <div>
            <a href="{{ route('admin.khoa-hoc.create') }}" 
               class="inline-flex items-center h-9 px-3.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md shadow-xs transition-colors">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Thêm khóa học mới
            </a>
        </div>
    </div>

    <!-- Alert Flash Messages -->
    @if(session('success'))
        <div class="p-3.5 bg-emerald-50 border border-emerald-200 rounded-md flex items-center justify-between text-emerald-800 text-sm font-medium">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>
    @endif

    <!-- Search & Filter Bar -->
    <div class="bg-white rounded-lg border border-slate-200 p-4">
        <form method="GET" action="{{ route('admin.khoa-hoc.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <!-- Search Text -->
            <div class="lg:col-span-2">
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Tìm kiếm</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nhập tên khóa học..." 
                        class="w-full h-10 pl-9 pr-3 bg-white border border-slate-200 rounded-md text-sm font-medium text-slate-900 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-colors">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>

            <!-- Semester Filter -->
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Học kỳ</label>
                <select name="semester" onchange="this.form.submit()" 
                    class="w-full h-10 px-3 bg-white border border-slate-200 rounded-md text-sm font-medium text-slate-900 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-colors">
                    <option value="">Tất cả học kỳ</option>
                    <option value="Học kỳ 1" {{ request('semester') == 'Học kỳ 1' ? 'selected' : '' }}>Học kỳ 1</option>
                    <option value="Học kỳ 2" {{ request('semester') == 'Học kỳ 2' ? 'selected' : '' }}>Học kỳ 2</option>
                    <option value="Học kỳ 3" {{ request('semester') == 'Học kỳ 3' ? 'selected' : '' }}>Học kỳ 3</option>
                </select>
            </div>

            <!-- Academic Year Filter -->
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Năm học</label>
                <select name="academic_year" onchange="this.form.submit()" 
                    class="w-full h-10 px-3 bg-white border border-slate-200 rounded-md text-sm font-medium text-slate-900 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-colors">
                    <option value="">Tất cả năm học</option>
                    @foreach($academicYears as $year)
                        <option value="{{ $year }}" {{ request('academic_year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Trạng thái</label>
                <select name="status" onchange="this.form.submit()" 
                    class="w-full h-10 px-3 bg-white border border-slate-200 rounded-md text-sm font-medium text-slate-900 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-colors">
                    <option value="">Tất cả</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Hoạt động</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Tạm dừng</option>
                </select>
            </div>
        </form>
    </div>

    <!-- Data Table Container -->
    <div class="bg-white rounded-lg border border-slate-200 overflow-hidden">
        @if($courses->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-semibold tracking-wider text-slate-500 uppercase">
                            <th class="py-3 px-5">Tên Khóa Học</th>
                            <th class="py-3 px-5">Học Kỳ</th>
                            <th class="py-3 px-5">Năm Học</th>
                            <th class="py-3 px-5 text-center">Số Môn Học</th>
                            <th class="py-3 px-5 text-center">Tổng Tín Chỉ</th>
                            <th class="py-3 px-5 text-center">Trạng Thái</th>
                            <th class="py-3 px-5 text-right">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 text-sm font-medium">
                        @foreach($courses as $course)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-3.5 px-5">
                                    <a href="{{ route('admin.khoa-hoc.show', $course) }}" class="font-semibold text-slate-900 hover:text-blue-600 transition-colors block">
                                        {{ $course->name }}
                                    </a>
                                    @if($course->description)
                                        <span class="text-xs text-slate-500 block truncate max-w-xs mt-0.5">{{ $course->description }}</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-5 text-slate-600">
                                    {{ $course->semester }}
                                </td>
                                <td class="py-3.5 px-5 text-slate-600 font-mono text-xs">
                                    {{ $course->academic_year }}
                                </td>
                                <td class="py-3.5 px-5 text-center text-slate-700">
                                    {{ $course->subjects_count }} môn
                                </td>
                                <td class="py-3.5 px-5 text-center font-mono text-xs font-semibold text-slate-700">
                                    {{ $course->total_credits }} TC
                                </td>
                                <td class="py-3.5 px-5 text-center">
                                    @if($course->status === 'active')
                                        <span class="text-xs font-medium text-emerald-600">Hoạt động</span>
                                    @else
                                        <span class="text-xs font-medium text-slate-500">Tạm dừng</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-5 text-right">
                                    <div class="flex items-center justify-end gap-2" x-data="{ showDeleteModal: false }">
                                        <a href="{{ route('admin.khoa-hoc.show', $course) }}" 
                                           class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-slate-100 rounded transition-colors" 
                                           title="Xem chi tiết">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('admin.khoa-hoc.edit', $course) }}" 
                                           class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-slate-100 rounded transition-colors" 
                                           title="Chỉnh sửa">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <button type="button" @click="showDeleteModal = true" 
                                                class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded transition-colors" 
                                                title="Xóa khóa học">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>

                                        <!-- Delete Confirmation Modal (SaaS Clean Dialog) -->
                                        <template x-teleport="body">
                                            <div x-show="showDeleteModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                                                <div class="fixed inset-0 bg-slate-900/40 transition-opacity" @click="showDeleteModal = false"></div>
                                                <div class="flex min-h-full items-center justify-center p-4">
                                                    <div class="relative bg-white rounded-lg p-6 max-w-sm w-full border border-slate-200 shadow-lg text-left">
                                                        <h3 class="text-base font-bold text-slate-900 mb-1.5">Xác nhận xóa khóa học</h3>
                                                        <p class="text-sm text-slate-600 mb-5">
                                                            Bạn có chắc muốn xóa <strong class="text-slate-900">{{ $course->name }}</strong>? Thao tác này không thể hoàn tác.
                                                        </p>
                                                        
                                                        <form method="POST" action="{{ route('admin.khoa-hoc.destroy', $course) }}" class="flex items-center justify-end gap-2.5">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" @click="showDeleteModal = false" 
                                                                    class="h-9 px-3.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 rounded-md font-medium text-sm transition-colors">
                                                                Hủy bỏ
                                                            </button>
                                                            <button type="submit" 
                                                                    class="h-9 px-3.5 bg-rose-600 hover:bg-rose-700 text-white rounded-md font-medium text-sm transition-colors">
                                                                Xóa khóa học
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($courses->hasPages())
                <div class="p-4 border-t border-slate-200 bg-white">
                    {{ $courses->links() }}
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="p-12 text-center">
                <p class="text-sm font-medium text-slate-900">Không tìm thấy khóa học nào</p>
                <p class="text-xs text-slate-500 mt-1">Thử thay đổi bộ lọc tìm kiếm hoặc tạo khóa học mới.</p>
                <div class="mt-4">
                    <a href="{{ route('admin.khoa-hoc.create') }}" 
                       class="inline-flex items-center h-9 px-3.5 bg-blue-600 hover:bg-blue-700 text-white font-medium text-sm rounded-md transition-colors">
                        Tạo khóa học mới
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
