@extends('layouts.admin')

@section('title', 'Quản lý đề thi')

@section('content')
<div class="space-y-5">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Quản lý đề thi</h1>
            <p class="text-xs text-slate-500 mt-0.5">Theo dõi danh sách các bài thi trắc nghiệm và phòng thi trực tuyến</p>
        </div>
        <div>
            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                Tổng cộng: <strong class="ml-1 text-slate-900">{{ $exams->total() }}</strong>
            </span>
        </div>
    </div>

    <!-- Search Toolbar -->
    <div class="bg-white border border-slate-200 rounded-lg p-3.5">
        <form action="{{ route('admin.exams.index') }}" method="GET" class="flex flex-wrap items-center gap-2.5">
            <div class="relative min-w-[260px] flex-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}" 
                       placeholder="Tìm kiếm theo mã hoặc tiêu đề đề thi..." 
                       class="w-full h-9 pl-9 pr-3 text-sm bg-white border border-slate-200 rounded-md placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-colors">
            </div>

            <button type="submit" class="h-9 px-3.5 inline-flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-md transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                Tìm kiếm
            </button>

            @if(request('search'))
                <a href="{{ route('admin.exams.index') }}" class="h-9 px-3 inline-flex items-center text-xs font-medium text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 rounded-md transition-colors">
                    Xóa tìm kiếm
                </a>
            @endif
        </form>
    </div>

    <!-- Exams Table -->
    <div class="bg-white border border-slate-200 rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/75 border-b border-slate-200 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                        <th class="py-3 px-4">Mã đề</th>
                        <th class="py-3 px-4">Tiêu đề đề thi</th>
                        <th class="py-3 px-4">Môn học</th>
                        <th class="py-3 px-4">Giảng viên phụ trách</th>
                        <th class="py-3 px-4 text-center">Trạng thái</th>
                        <th class="py-3 px-4 text-right">Giám sát</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($exams as $exam)
                        <tr class="hover:bg-slate-50/75 transition-colors">
                            <!-- Code -->
                            <td class="py-3 px-4 font-mono text-xs font-semibold text-slate-800">
                                <span class="px-2 py-0.5 bg-slate-100 border border-slate-200 rounded">
                                    {{ $exam->code }}
                                </span>
                            </td>

                            <!-- Title & Specs -->
                            <td class="py-3 px-4">
                                <div class="font-medium text-slate-900">{{ $exam->title }}</div>
                                <div class="text-xs text-slate-500 font-mono mt-0.5 flex items-center gap-2">
                                    <span>{{ $exam->duration }} phút</span>
                                    <span>•</span>
                                    <span>{{ $exam->questions->count() }} câu hỏi</span>
                                </div>
                            </td>

                            <!-- Subject -->
                            <td class="py-3 px-4 text-slate-700 text-xs">
                                {{ $exam->subject->name ?? 'N/A' }}
                            </td>

                            <!-- Teacher -->
                            <td class="py-3 px-4 text-slate-600 text-xs">
                                {{ $exam->creator->name ?? 'N/A' }}
                            </td>

                            <!-- Status -->
                            <td class="py-3 px-4 text-center">
                                @if($exam->status === 'published')
                                    <span class="inline-flex items-center gap-1 text-xs font-medium text-emerald-600">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                                        Đang mở
                                    </span>
                                @else
                                    <span class="text-xs font-medium text-slate-500">Đã đóng</span>
                                @endif
                            </td>

                            <!-- Monitor Action -->
                            <td class="py-3 px-4 text-right">
                                <a href="{{ route('admin.exams.monitor', $exam) }}" class="inline-flex items-center gap-1.5 text-xs font-medium text-blue-600 hover:text-blue-700 transition-colors" title="Giám sát phòng thi thời gian thực">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Giám sát
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 px-4 text-center">
                                <div class="max-w-xs mx-auto text-center">
                                    <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </div>
                                    <div class="text-sm font-medium text-slate-900">Không tìm thấy đề thi</div>
                                    <p class="text-xs text-slate-500 mt-1">Chưa có đề thi nào được tạo hoặc không khớp với kết quả tìm kiếm.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($exams->hasPages())
            <div class="px-4 py-3 border-t border-slate-200 bg-slate-50/50">
                {{ $exams->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
