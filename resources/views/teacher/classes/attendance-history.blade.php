@extends('layouts.teacher')

@section('title', 'Lịch sử Điểm danh')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('teacher.classes.index') }}" class="text-sm font-medium text-gray-500 hover:text-indigo-600 transition-colors">Quản lý lớp</a>
                <span class="text-gray-400">/</span>
                <a href="{{ route('teacher.classes.show', $class) }}" class="text-sm font-medium text-gray-500 hover:text-indigo-600 transition-colors">{{ $class->code }}</a>
                <span class="text-gray-400">/</span>
                <span class="text-sm font-medium text-gray-900">Lịch sử điểm danh</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900">Lịch sử Điểm danh Lớp: {{ $class->name }}</h1>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('teacher.classes.attendance', $class) }}" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm rounded-xl transition-colors shadow-sm">
                + Điểm danh mới
            </a>
            <a href="{{ route('teacher.classes.show', $class) }}" class="px-4 py-2.5 bg-white border border-gray-200 text-gray-700 font-bold text-sm rounded-xl transition-colors">
                Quay lại
            </a>
        </div>
    </div>

    <!-- History Table -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-bold text-gray-800 text-lg">Các buổi học đã điểm danh</h3>
            <span class="text-xs text-gray-500 font-medium">Tổng số: {{ $history->count() }} buổi</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 border-b border-gray-100 text-xs uppercase text-gray-500 font-semibold">
                    <tr>
                        <th class="px-6 py-4 w-16 text-center">STT</th>
                        <th class="px-6 py-4">Ngày điểm danh</th>
                        <th class="px-6 py-4 text-center">Có mặt</th>
                        <th class="px-6 py-4 text-center">Vắng mặt</th>
                        <th class="px-6 py-4 text-center">Đi muộn</th>
                        <th class="px-6 py-4 text-center">Có phép</th>
                        <th class="px-6 py-4 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($history as $index => $session)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 text-center text-gray-400 font-medium">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 font-bold text-gray-900">
                                📅 {{ date('d/m/Y', strtotime($session->attendance_date)) }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 font-bold text-xs rounded-full border border-emerald-100">
                                    {{ $session->present_count }} SV
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2.5 py-1 bg-rose-50 text-rose-700 font-bold text-xs rounded-full border border-rose-100">
                                    {{ $session->absent_count }} SV
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2.5 py-1 bg-amber-50 text-amber-700 font-bold text-xs rounded-full border border-amber-100">
                                    {{ $session->late_count }} SV
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2.5 py-1 bg-sky-50 text-sky-700 font-bold text-xs rounded-full border border-sky-100">
                                    {{ $session->excused_count }} SV
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('teacher.classes.attendance', [$class, 'date' => date('Y-m-d', strtotime($session->attendance_date))]) }}" 
                                    class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-xs rounded-xl transition-colors inline-flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    Xem / Sửa
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                Chưa có buổi điểm danh nào được ghi nhận cho lớp này.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
