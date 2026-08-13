@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-800">Quản lý Tất cả Đề thi</h1>
    </div>

    <!-- Search & Filter -->
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-200">
        <form action="{{ route('admin.exams.index') }}" method="GET" class="flex items-center gap-4">
            <div class="flex-1 relative">
                <svg class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm kiếm theo mã hoặc tên đề thi..." class="w-full pl-10 pr-4 py-2 border-slate-200 rounded-xl focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-xl hover:bg-indigo-700 transition-colors">
                Tìm kiếm
            </button>
        </form>
    </div>

    <!-- Exams List -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200">
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600 w-16">Mã</th>
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600">Tên Đề thi</th>
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600">Môn học</th>
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600">Giảng viên tạo</th>
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600">Trạng thái</th>
                    <th class="px-6 py-4 text-sm font-semibold text-slate-600 text-right">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($exams as $exam)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4 font-medium text-slate-900">{{ $exam->code }}</td>
                        <td class="px-6 py-4">
                            <div class="font-bold text-slate-800">{{ $exam->title }}</div>
                            <div class="text-xs text-slate-500">{{ $exam->duration }} phút | {{ $exam->questions->count() }} câu hỏi</div>
                        </td>
                        <td class="px-6 py-4 text-slate-600">{{ $exam->subject->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ $exam->creator->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4">
                            @if($exam->status === 'published')
                                <span class="px-3 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">Đã mở</span>
                            @else
                                <span class="px-3 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-700">Đóng</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.exams.monitor', $exam) }}" class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors" title="Giám sát">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                            Không tìm thấy đề thi nào.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        @if($exams->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $exams->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
