@extends('layouts.student')

@section('title', 'Tổng quan Sinh viên')

@section('content')
<div class="max-w-6xl mx-auto space-y-8 pb-12">

    <!-- Header Section -->
    <div class="bg-white rounded-3xl p-8 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-gray-100 relative overflow-hidden">
        <!-- Decorative background elements -->
        <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 rounded-full bg-gradient-to-br from-indigo-50 to-blue-50 opacity-50 blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 -ml-16 -mb-16 w-64 h-64 rounded-full bg-gradient-to-tr from-emerald-50 to-teal-50 opacity-50 blur-3xl pointer-events-none"></div>

        <div class="relative z-10 grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight mb-2">Chào mừng, {{ auth()->user()->name }}!</h1>
                <p class="text-gray-500 mb-6">Mã SV: {{ auth()->user()->code }}</p>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-700">Sinh viên</span>
                </div>
            </div>
            
            <div class="bg-gray-50/80 backdrop-blur border border-gray-100 p-6 rounded-2xl shadow-inner">
                <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider mb-4">Tham gia kỳ thi</h3>
                <form action="{{ route('student.exams.join') }}" method="POST" class="flex gap-3">
                    @csrf
                    <input type="text" name="code" required placeholder="Nhập mã đề thi (VD: X8F9A)" class="flex-1 rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 px-4 py-3 text-sm font-medium uppercase placeholder:normal-case shadow-sm" autocomplete="off">
                    <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl shadow-sm transition-colors whitespace-nowrap">
                        Vào thi
                    </button>
                </form>
                @error('code')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div>
        <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            Lịch sử thi & Kết quả
        </h2>

        @if($attempts->isEmpty())
            <div class="bg-white rounded-3xl p-12 text-center border border-gray-100 shadow-sm">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-1">Chưa có bài thi nào</h3>
                <p class="text-gray-500">Bạn chưa tham gia kỳ thi nào. Hãy nhập mã đề thi ở phía trên để bắt đầu.</p>
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                @foreach($attempts as $attempt)
                    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
                        <!-- Status indicator strip -->
                        <div class="absolute left-0 top-0 bottom-0 w-1.5 
                            {{ $attempt->status === 'submitted' ? 'bg-emerald-500' : ($attempt->status === 'in_progress' ? 'bg-amber-500' : 'bg-rose-500') }}
                        "></div>
                        
                        <div class="pl-4">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1 block">{{ $attempt->exam->subject->name }}</span>
                                    <h3 class="text-lg font-bold text-gray-900 leading-tight group-hover:text-indigo-600 transition-colors">{{ $attempt->exam->title }}</h3>
                                </div>
                                
                                @if($attempt->status === 'submitted')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                        Đã nộp bài
                                    </span>
                                @elseif($attempt->status === 'in_progress')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                                        Đang thi
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800">
                                        Vi phạm / Lỗi
                                    </span>
                                @endif
                            </div>

                            <div class="grid grid-cols-2 gap-4 mb-5">
                                <div class="bg-gray-50 rounded-xl p-3">
                                    <p class="text-xs text-gray-500 mb-1">Thời gian nộp</p>
                                    <p class="text-sm font-semibold text-gray-900">
                                        {{ $attempt->submitted_at ? $attempt->submitted_at->format('d/m/Y H:i') : '--' }}
                                    </p>
                                </div>
                                <div class="bg-gray-50 rounded-xl p-3 text-right">
                                    <p class="text-xs text-gray-500 mb-1">Điểm số</p>
                                    <p class="text-lg font-extrabold {{ $attempt->score_value >= 5 ? 'text-emerald-600' : 'text-rose-600' }}">
                                        {{ $attempt->score_value !== null ? $attempt->score_value . ' đ' : 'Chưa chấm' }}
                                    </p>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-100">
                                <span class="text-xs text-gray-400">Mã bài thi: #{{ $attempt->id }}</span>
                                @if($attempt->status === 'in_progress')
                                    <a href="{{ route('student.exams.take', $attempt->exam) }}" class="text-sm font-bold text-amber-600 hover:text-amber-700 flex items-center gap-1">
                                        Tiếp tục làm bài
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                    </a>
                                @else
                                    <a href="#" class="text-sm font-bold text-indigo-600 hover:text-indigo-700 flex items-center gap-1">
                                        Xem chi tiết
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
