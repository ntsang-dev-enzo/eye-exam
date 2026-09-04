@extends('layouts.admin')

@section('title', 'Quản lý Môn học')

@section('content')
<div class="space-y-6">
    <!-- Top Bar: Title & Primary Action -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Danh sách Môn học</h1>
            <p class="text-sm text-slate-500 mt-0.5">Quản lý danh mục học phần, số tín chỉ và mã môn trong toàn hệ thống</p>
        </div>
        <div>
            <a href="{{ route('admin.subjects.create') }}" 
               class="inline-flex items-center h-9 px-3.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md shadow-xs transition-colors">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Thêm môn học
            </a>
        </div>
    </div>

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
        <form action="{{ route('admin.subjects.index') }}" method="GET" class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="relative flex-1 max-w-md">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm theo mã môn hoặc tên môn học..." 
                    class="w-full h-10 pl-9 pr-3 bg-white border border-slate-200 rounded-md text-sm font-medium text-slate-900 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-colors">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            @if(request('search'))
                <a href="{{ route('admin.subjects.index') }}" 
                   class="inline-flex items-center h-10 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-medium rounded-md transition-colors">
                    Xóa tìm kiếm
                </a>
            @endif
        </form>
    </div>

    <!-- Table Container -->
    <div class="bg-white rounded-lg border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                        <th class="py-3 px-5">Mã Môn</th>
                        <th class="py-3 px-5">Tên Môn Học</th>
                        <th class="py-3 px-5 text-center">Tín chỉ</th>
                        <th class="py-3 px-5">Mô tả</th>
                        <th class="py-3 px-5 text-center">Trạng thái</th>
                        <th class="py-3 px-5 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-sm">
                    @forelse($subjects as $subject)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3.5 px-5 font-mono text-xs font-semibold text-slate-900">
                                {{ $subject->code }}
                            </td>
                            <td class="py-3.5 px-5 font-semibold text-slate-900">
                                {{ $subject->name }}
                            </td>
                            <td class="py-3.5 px-5 text-center">
                                <span class="px-2 py-0.5 bg-blue-50 text-blue-700 font-mono font-semibold text-xs rounded border border-blue-200">
                                    {{ $subject->credits ?? 3 }} TC
                                </span>
                            </td>
                            <td class="py-3.5 px-5 text-slate-500 text-xs max-w-xs truncate">
                                {{ $subject->description ?? '—' }}
                            </td>
                            <td class="py-3.5 px-5 text-center">
                                @if($subject->status)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Hoạt động
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                        Tạm khóa
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-5 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.subjects.edit', $subject) }}" 
                                       class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-slate-100 rounded transition-colors" 
                                       title="Chỉnh sửa">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                Chưa có môn học nào được tạo
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($subjects->hasPages())
            <div class="px-5 py-3 border-t border-slate-200 bg-white">
                {{ $subjects->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
