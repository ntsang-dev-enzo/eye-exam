@extends('layouts.admin')

@section('title', 'Quản lý Lớp học')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="px-6 py-5 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <h3 class="font-semibold text-slate-800">Danh sách Lớp học</h3>
        
        <div class="flex items-center gap-3">
            <form action="{{ route('admin.classes.index') }}" method="GET">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm kiếm lớp..." class="px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500 w-64 shadow-sm">
            </form>
            
            <a href="{{ route('admin.classes.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors">
                Thêm mới
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="m-6 p-4 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-100">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    <th class="px-6 py-4 border-b border-slate-100">Mã Lớp</th>
                    <th class="px-6 py-4 border-b border-slate-100">Tên Lớp</th>
                    <th class="px-6 py-4 border-b border-slate-100">Giảng viên</th>
                    <th class="px-6 py-4 border-b border-slate-100 text-center">Sĩ số</th>
                    <th class="px-6 py-4 border-b border-slate-100 text-center">Trạng thái</th>
                    <th class="px-6 py-4 border-b border-slate-100 text-right">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($classes as $class)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4 text-sm font-bold text-slate-900">{{ $class->code }}</td>
                        <td class="px-6 py-4 text-sm text-slate-700 font-medium">{{ $class->name }}</td>
                        <td class="px-6 py-4 text-sm text-slate-600">{{ $class->teacher->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm text-center text-slate-600 font-bold">{{ $class->students_count }}</td>
                        <td class="px-6 py-4 text-center">
                            @if($class->status == 'active')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">Hoạt động</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-rose-100 text-rose-800">Đã khóa</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.classes.show', $class) }}" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-lg transition-colors font-medium text-sm">Phân công</a>
                            <a href="{{ route('admin.classes.edit', $class) }}" class="p-1.5 text-slate-400 hover:text-indigo-600 rounded-lg hover:bg-indigo-50 transition-colors inline-block" title="Sửa">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                            Không tìm thấy lớp học nào.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="px-6 py-4 border-t border-slate-100">
        {{ $classes->links() }}
    </div>
</div>
@endsection
