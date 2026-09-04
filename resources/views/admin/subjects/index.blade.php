@extends('layouts.admin')

@section('title', 'Quản lý Môn học')

@section('content')
    <div class="bg-white rounded-2xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-gray-100 overflow-hidden">
        <!-- Header -->
        <div class="px-6 py-5 border-b border-gray-100">
            <div class="flex justify-between items-center">
                <h3 class="font-semibold text-gray-800">Danh sách Môn học</h3>
                <div class="flex items-center gap-4">
                    <form action="{{ route('admin.subjects.index') }}" method="GET" class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm mã, tên môn..." class="pl-10 pr-4 py-2 border border-slate-300 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500 w-64 shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                    </form>
                    <a href="{{ route('admin.subjects.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Thêm môn học
                    </a>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="m-6 p-4 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-100">
                {{ session('success') }}
            </div>
        @endif

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 text-xs font-semibold text-slate-500 uppercase tracking-wider border-b border-gray-100">
                        <th class="px-6 py-4">Mã Môn</th>
                        <th class="px-6 py-4">Tên Môn Học</th>
                        <th class="px-6 py-4 text-center">Tín chỉ</th>
                        <th class="px-6 py-4">Mô tả</th>
                        <th class="px-6 py-4">Trạng thái</th>
                        <th class="px-6 py-4 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($subjects as $subject)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 text-sm font-bold text-slate-700">{{ $subject->code }}</td>
                            <td class="px-6 py-4 text-sm font-medium text-slate-900">{{ $subject->name }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2.5 py-1 bg-indigo-50 text-indigo-700 font-bold text-xs rounded-lg border border-indigo-100 font-mono">
                                    {{ $subject->credits ?? 3 }} TC
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-500 line-clamp-1">{{ $subject->description ?? '-' }}</td>
                            <td class="px-6 py-4">
                                @if($subject->status)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">Hoạt động</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-rose-100 text-rose-800">Tạm khóa</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.subjects.edit', $subject) }}" class="p-1.5 text-slate-400 hover:text-indigo-600 rounded-lg hover:bg-indigo-50 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                                Chưa có môn học nào được tạo.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $subjects->links() }}
        </div>
    </div>
@endsection
