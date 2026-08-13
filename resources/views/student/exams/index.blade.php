@extends('layouts.student')

@section('title', 'Kỳ thi của tôi')

@section('content')
<div class="max-w-6xl mx-auto space-y-8 pb-12">
    <!-- Header -->
    <div class="mb-6 flex justify-between items-end">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Kỳ thi được giao</h2>
            <p class="text-gray-500 mt-1">Danh sách các bài thi mà giảng viên đã giao cho bạn.</p>
        </div>
    </div>

    @if($exams->isEmpty())
        <div class="bg-white rounded-3xl p-12 text-center border border-gray-100 shadow-sm">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-1">Chưa có bài thi nào</h3>
            <p class="text-gray-500">Hiện tại bạn chưa được giao bài thi nào.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($exams as $exam)
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden flex flex-col h-full">
                    <!-- Color strip based on status -->
                    <div class="absolute top-0 left-0 right-0 h-1.5 bg-{{ $exam->status_color }}-500"></div>
                    
                    <div class="p-6 flex-1 flex flex-col">
                        <div class="flex justify-between items-start mb-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-{{ $exam->status_color }}-100 text-{{ $exam->status_color }}-800">
                                {{ $exam->calculated_status }}
                            </span>
                            <span class="text-xs font-bold text-gray-400 bg-gray-100 px-2 py-1 rounded">Mã: {{ $exam->code }}</span>
                        </div>
                        
                        <h3 class="text-lg font-bold text-gray-900 mb-1 line-clamp-2">{{ $exam->title }}</h3>
                        <p class="text-sm font-semibold text-gray-500 mb-4">{{ $exam->subject->name ?? 'Không rõ môn học' }}</p>
                        
                        <div class="grid grid-cols-2 gap-3 mb-5 mt-auto">
                            <div class="bg-gray-50 p-2.5 rounded-lg">
                                <p class="text-[10px] text-gray-500 font-bold uppercase tracking-wide">Thời gian</p>
                                <p class="text-sm font-bold text-gray-700">{{ $exam->duration_minutes }} phút</p>
                            </div>
                            <div class="bg-gray-50 p-2.5 rounded-lg">
                                <p class="text-[10px] text-gray-500 font-bold uppercase tracking-wide">Số câu hỏi</p>
                                <p class="text-sm font-bold text-gray-700">{{ $exam->total_questions }} câu</p>
                            </div>
                        </div>
                        
                        <div class="space-y-2 mb-6">
                            @if($exam->start_at || $exam->end_at)
                                @if($exam->start_at)
                                <div class="flex justify-between text-xs">
                                    <span class="text-gray-500">Mở:</span>
                                    <span class="font-medium text-gray-700">{{ $exam->start_at->format('d/m/Y H:i') }}</span>
                                </div>
                                @endif
                                @if($exam->end_at)
                                <div class="flex justify-between text-xs">
                                    <span class="text-gray-500">Đóng:</span>
                                    <span class="font-medium text-gray-700">{{ $exam->end_at->format('d/m/Y H:i') }}</span>
                                </div>
                                @endif
                            @else
                                <div class="text-xs text-gray-500 italic">Không giới hạn thời gian mở/đóng.</div>
                            @endif
                        </div>
                        
                        @if($exam->calculated_status === 'Đã nộp bài')
                            <div class="flex flex-col gap-2">
                                <div class="text-center p-3 bg-emerald-50 rounded-xl text-emerald-700 font-bold">
                                    Điểm của bạn: {{ $exam->score !== null ? $exam->score . ' đ' : 'Đang chấm' }}
                                </div>
                                @if($exam->allow_review)
                                    <a href="{{ route('student.exams.review', $exam) }}" class="w-full py-2.5 px-4 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-sm font-bold rounded-xl border border-indigo-200 transition-colors flex items-center justify-center gap-2">
                                        Xem lại bài
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </a>
                                @endif
                            </div>
                        @elseif($exam->calculated_status === 'Đang thi')
                            <form action="{{ route('student.exams.join') }}" method="POST">
                                @csrf
                                <input type="hidden" name="code" value="{{ $exam->code }}">
                                <button type="submit" class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl shadow-sm transition-colors flex items-center justify-center">
                                    Vào thi ngay
                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </button>
                            </form>
                        @elseif($exam->calculated_status === 'Sắp thi')
                            <button disabled class="w-full py-3 px-4 bg-gray-100 text-gray-400 text-sm font-bold rounded-xl cursor-not-allowed text-center border border-gray-200">
                                Chưa đến giờ thi
                            </button>
                        @else
                            <button disabled class="w-full py-3 px-4 bg-gray-100 text-gray-400 text-sm font-bold rounded-xl cursor-not-allowed text-center border border-gray-200">
                                Không thể làm bài
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
