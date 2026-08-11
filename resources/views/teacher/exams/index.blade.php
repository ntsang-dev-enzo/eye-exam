@extends('layouts.teacher')

@section('title', 'Quản lý Đề thi')

@section('content')
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <!-- Header & Filters -->
        <div class="px-6 py-5 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <h3 class="font-semibold text-gray-800">Danh sách Đề thi</h3>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                    {{ $exams->total() }} đề thi
                </span>
            </div>
            
            <div class="flex flex-wrap items-center gap-3">
                <form action="{{ route('teacher.exams.index') }}" method="GET" class="flex flex-wrap items-center gap-2">
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm mã, tên đề..." class="pl-8 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 w-48 shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                    </div>

                    <select name="subject_id" class="text-sm rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" onchange="this.form.submit()">
                        <option value="">-- Tất cả môn học --</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                                {{ $subject->name }}
                            </option>
                        @endforeach
                    </select>

                    <select name="status" class="text-sm rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" onchange="this.form.submit()">
                        <option value="">-- Tất cả trạng thái --</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Bản nháp</option>
                        <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Đã xuất bản</option>
                        <option value="ongoing" {{ request('status') === 'ongoing' ? 'selected' : '' }}>Đang diễn ra</option>
                        <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Đã đóng</option>
                    </select>
                </form>
                
                <a href="{{ route('teacher.exams.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tạo đề thi mới
                </a>
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
                    <tr class="bg-gray-50/50 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <th class="px-6 py-4 border-b border-gray-100">Mã đề / Tên đề thi</th>
                        <th class="px-6 py-4 border-b border-gray-100">Cấu trúc</th>
                        <th class="px-6 py-4 border-b border-gray-100">Thời gian biểu</th>
                        <th class="px-6 py-4 border-b border-gray-100 text-center">Trạng thái</th>
                        <th class="px-6 py-4 border-b border-gray-100 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($exams as $exam)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-gray-200 text-gray-700">{{ $exam->code }}</span>
                                    <p class="text-sm font-bold text-gray-900">{{ $exam->title }}</p>
                                </div>
                                <p class="text-xs text-gray-500">Môn: <span class="font-medium">{{ $exam->subject->name ?? 'N/A' }}</span></p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm text-gray-700 font-medium">{{ $exam->total_questions }} câu hỏi</p>
                                <p class="text-xs text-gray-500 mt-1">Làm bài: <span class="font-medium text-blue-600">{{ $exam->duration_minutes }} phút</span></p>
                            </td>
                            <td class="px-6 py-4">
                                @if($exam->start_at || $exam->end_at)
                                    <p class="text-xs text-gray-600"><span class="font-medium text-emerald-600">Mở:</span> {{ $exam->start_at ? $exam->start_at->format('d/m/Y H:i') : 'Không giới hạn' }}</p>
                                    <p class="text-xs text-gray-600 mt-1"><span class="font-medium text-rose-600">Đóng:</span> {{ $exam->end_at ? $exam->end_at->format('d/m/Y H:i') : 'Không giới hạn' }}</p>
                                @else
                                    <span class="text-xs text-gray-500 italic">Luôn mở</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <form action="{{ route('teacher.exams.update-status', $exam) }}" method="POST" class="inline-block m-0">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" onchange="this.form.submit()" class="text-xs font-semibold rounded-full px-2.5 py-1 border-0 ring-1 ring-inset shadow-sm cursor-pointer appearance-none text-center
                                        @if($exam->status == 'draft') bg-gray-50 text-gray-700 ring-gray-200 focus:ring-gray-300
                                        @elseif($exam->status == 'published') bg-blue-50 text-blue-700 ring-blue-200 focus:ring-blue-300
                                        @elseif($exam->status == 'ongoing') bg-emerald-50 text-emerald-700 ring-emerald-200 focus:ring-emerald-300
                                        @else bg-red-50 text-red-700 ring-red-200 focus:ring-red-300
                                        @endif
                                    ">
                                        <option value="draft" {{ $exam->status == 'draft' ? 'selected' : '' }}>Nháp</option>
                                        <option value="published" {{ $exam->status == 'published' ? 'selected' : '' }}>Xuất bản</option>
                                        <option value="ongoing" {{ $exam->status == 'ongoing' ? 'selected' : '' }}>Đang diễn ra</option>
                                        <option value="closed" {{ $exam->status == 'closed' ? 'selected' : '' }}>Đã đóng</option>
                                    </select>
                                </form>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if($exam->status === 'draft')
                                        <a href="{{ route('teacher.exams.edit', $exam) }}" class="p-1.5 text-gray-400 hover:text-indigo-600 rounded-lg hover:bg-indigo-50 transition-colors" title="Sửa đề thi">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                        </a>
                                    @endif
                                    <button class="p-1.5 text-gray-400 hover:text-blue-600 rounded-lg hover:bg-blue-50 transition-colors" title="Xem chi tiết">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                Bạn chưa tạo kỳ thi nào. <a href="{{ route('teacher.exams.create') }}" class="text-blue-600 hover:underline">Tạo mới ngay</a>.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $exams->links() }}
        </div>
    </div>
@endsection
