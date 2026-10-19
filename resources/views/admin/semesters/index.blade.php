@extends('layouts.admin')

@section('title', 'Quản lý Học kỳ')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Học kỳ</h1>
            <p class="text-sm text-slate-500 mt-0.5">Trục thời gian đào tạo theo niên khóa, quản lý học kỳ hiện tại</p>
        </div>
        <div>
            <a href="{{ route('admin.semesters.create') }}"
                class="inline-flex items-center h-9 px-3.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md shadow-xs transition-colors">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Thêm Học kỳ mới
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
        <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">&times;</button>
    </div>
    @endif

    @if(session('error'))
    <div class="p-3.5 bg-rose-50 border border-rose-200 rounded-md flex items-center justify-between text-rose-800 text-sm font-medium">
        <div class="flex items-center gap-2">
            <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ session('error') }}</span>
        </div>
        <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700">&times;</button>
    </div>
    @endif

    <!-- Filters -->
    <div class="bg-white rounded-lg border border-slate-200 p-4">
        <form action="{{ route('admin.semesters.index') }}" method="GET" class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="relative flex-1 max-w-md">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm theo mã hoặc tên học kỳ..."
                    class="w-full h-10 pl-9 pr-3 bg-white border border-slate-200 rounded-md text-sm font-medium text-slate-900 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-colors">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <div class="flex items-center gap-2">
                <select name="academic_year" onchange="this.form.submit()" class="h-10 px-3 border border-slate-200 rounded-md text-sm text-slate-700 bg-white">
                    <option value="">-- Lọc theo Năm học --</option>
                    @foreach($academicYears as $year)
                    <option value="{{ $year }}" {{ request('academic_year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                    @endforeach
                </select>
                @if(request()->anyFilled(['search', 'academic_year']))
                <a href="{{ route('admin.semesters.index') }}" class="h-10 px-3 inline-flex items-center text-xs bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-md">Xóa lọc</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                        <th class="py-3 px-4">Mã Học kỳ</th>
                        <th class="py-3 px-4">Tên Học kỳ</th>
                        <th class="py-3 px-4">Năm học</th>
                        <th class="py-3 px-4 text-center">Thời gian</th>
                        <th class="py-3 px-4 text-center">Lớp học phần</th>
                        <th class="py-3 px-4 text-center">Trạng thái hiện tại</th>
                        <th class="py-3 px-4 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($semesters as $sem)
                    <tr class="hover:bg-slate-50/70 transition-colors {{ $sem->is_current ? 'bg-blue-50/30' : '' }}">
                        <td class="py-3 px-4 font-mono font-semibold text-blue-700">{{ $sem->code }}</td>
                        <td class="py-3 px-4 font-medium text-slate-900">{{ $sem->name }}</td>
                        <td class="py-3 px-4 font-semibold text-slate-700">{{ $sem->academic_year }}</td>
                        <td class="py-3 px-4 text-center text-xs text-slate-500">
                            @if($sem->start_date && $sem->end_date)
                            {{ $sem->start_date->format('d/m/Y') }} - {{ $sem->end_date->format('d/m/Y') }}
                            @else
                            <span class="text-slate-400">Chưa đặt</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-center text-xs font-medium text-slate-700">
                            {{ $sem->classes_count }} lớp
                        </td>
                        <td class="py-3 px-4 text-center">
                            @if($sem->is_current)
                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-blue-600">
                                <span class="w-2 h-2 rounded-full bg-blue-600 shrink-0"></span>
                                Học kỳ hiện tại
                            </span>
                            @else
                            <form action="{{ route('admin.semesters.set-current', $sem) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-xs text-slate-500 hover:text-blue-600 transition-colors underline decoration-dotted">
                                    Đặt làm hiện tại
                                </button>
                            </form>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-right space-x-2">
                            <a href="{{ route('admin.semesters.edit', $sem) }}" class="text-blue-600 hover:text-blue-800 font-medium text-xs">Sửa</a>
                            <form action="{{ route('admin.semesters.destroy', $sem) }}" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc muốn xóa Học kỳ này?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-rose-600 hover:text-rose-800 font-medium text-xs">Xóa</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-slate-400">Chưa có Học kỳ nào được tạo.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($semesters->hasPages())
        <div class="p-4 border-t border-slate-200">
            {{ $semesters->links() }}
        </div>
        @endif
    </div>
</div>
@endsection