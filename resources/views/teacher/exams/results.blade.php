@extends('layouts.teacher')

@section('title', 'Kết quả Đề thi')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex items-center gap-2 mb-2">
        <a href="{{ route('teacher.exams.index') }}" class="text-sm font-medium text-gray-500 hover:text-blue-600 transition-colors">Quản lý kỳ thi</a>
        <span class="text-gray-400">/</span>
        <span class="text-sm font-medium text-gray-900">Kết quả Đề thi</span>
    </div>
    
    <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Kết quả: {{ $exam->title }}</h2>
            <p class="text-sm text-gray-500 mt-1">Mã đề: <span class="font-bold text-gray-800">{{ $exam->code }}</span> - Môn: <span class="font-bold text-gray-800">{{ $exam->subject->name ?? 'N/A' }}</span></p>
        </div>
        <div class="text-right">
            <p class="text-sm text-gray-600">Tổng quan: <span class="font-bold text-blue-600">{{ $stats['total'] }}</span> lượt nộp bài</p>
            <p class="text-xs text-gray-500 mt-1">Trạng thái đề: <span class="font-medium {{ $exam->status === 'published' ? 'text-blue-600' : 'text-rose-600' }}">{{ $exam->status === 'published' ? 'Mở' : 'Đóng' }}</span></p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm text-center">
            <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-1">Điểm trung bình</p>
            <p class="text-3xl font-black text-blue-600">{{ number_format($stats['average'], 1) }}</p>
        </div>
        <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm text-center">
            <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-1">Cao nhất</p>
            <p class="text-3xl font-black text-emerald-600">{{ $stats['max'] }}</p>
        </div>
        <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm text-center">
            <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-1">Thấp nhất</p>
            <p class="text-3xl font-black text-rose-600">{{ $stats['min'] }}</p>
        </div>
        <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm text-center">
            <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-1">Tỉ lệ đạt (>= 5)</p>
            <p class="text-3xl font-black text-indigo-600">{{ $stats['pass_rate'] }}%</p>
        </div>
    </div>

    <!-- Bảng điểm -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <th class="px-6 py-4 border-b border-gray-100 w-16 text-center">STT</th>
                        <th class="px-6 py-4 border-b border-gray-100">Sinh viên</th>
                        <th class="px-6 py-4 border-b border-gray-100 text-center">Tình trạng</th>
                        <th class="px-6 py-4 border-b border-gray-100 text-center">Điểm số</th>
                        <th class="px-6 py-4 border-b border-gray-100 text-center">Chi tiết (Đ/S/B)</th>
                        <th class="px-6 py-4 border-b border-gray-100 text-center">Vi phạm</th>
                        <th class="px-6 py-4 border-b border-gray-100 text-right">Ngày nộp</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($attempts as $index => $attempt)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-center text-sm text-gray-500">{{ $index + 1 }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold shrink-0">
                                        {{ substr($attempt->student->name ?? '?', 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-900">{{ $attempt->student->name ?? 'Sinh viên vô danh' }}</p>
                                        <p class="text-xs text-gray-500">{{ $attempt->student->email ?? '' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($attempt->status === 'submitted')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700">Đã nộp bài</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-700">Đang thi</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($attempt->status === 'submitted')
                                    <span class="text-lg font-black text-blue-600">{{ $attempt->score_value }}</span>
                                @else
                                    <span class="text-sm text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($attempt->status === 'submitted')
                                    <div class="flex items-center justify-center gap-2 text-xs font-medium">
                                        <span class="text-emerald-600" title="Câu đúng">{{ $attempt->correct_answers }} Đ</span>
                                        <span class="text-gray-300">|</span>
                                        <span class="text-rose-600" title="Câu sai">{{ $attempt->wrong_answers }} S</span>
                                        <span class="text-gray-300">|</span>
                                        <span class="text-amber-600" title="Chưa làm">{{ $attempt->unanswered }} B</span>
                                    </div>
                                @else
                                    <span class="text-sm text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($attempt->cheat_warnings > 0)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800">
                                        {{ $attempt->cheat_warnings }} lần
                                    </span>
                                @else
                                    <span class="text-sm text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right text-sm text-gray-500">
                                @if($attempt->status === 'submitted' && $attempt->submitted_at)
                                    {{ $attempt->submitted_at->format('H:i d/m/Y') }}
                                @else
                                    <span class="italic text-gray-400">Chưa nộp</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                Chưa có sinh viên nào tham gia hoặc nộp bài thi này.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
