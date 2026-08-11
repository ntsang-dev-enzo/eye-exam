@extends('layouts.teacher')

@section('title', 'Dashboard - Thống kê')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Widget 1 -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition-shadow">
            <div class="w-14 h-14 rounded-full bg-blue-50 flex items-center justify-center flex-shrink-0 text-blue-600">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Lớp học phụ trách</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">12</p>
            </div>
        </div>

        <!-- Widget 2 -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition-shadow">
            <div class="w-14 h-14 rounded-full bg-indigo-50 flex items-center justify-center flex-shrink-0 text-indigo-600">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Kỳ thi đang diễn ra</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">3</p>
            </div>
        </div>

        <!-- Widget 3 -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition-shadow">
            <div class="w-14 h-14 rounded-full bg-emerald-50 flex items-center justify-center flex-shrink-0 text-emerald-600">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Câu hỏi đã tạo</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">245</p>
            </div>
        </div>

        <!-- Widget 4 -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition-shadow">
            <div class="w-14 h-14 rounded-full bg-amber-50 flex items-center justify-center flex-shrink-0 text-amber-600">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Bài thi chờ chấm</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">86</p>
            </div>
        </div>
    </div>

    <!-- Recent Activities & Exams -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Exams table -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-semibold text-gray-800">Kỳ thi sắp tới</h3>
                <a href="#" class="text-sm font-medium text-blue-600 hover:text-blue-700">Xem tất cả</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/50 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-4 border-b border-gray-100">Tên kỳ thi</th>
                            <th class="px-6 py-4 border-b border-gray-100">Môn học</th>
                            <th class="px-6 py-4 border-b border-gray-100">Lớp</th>
                            <th class="px-6 py-4 border-b border-gray-100">Thời gian</th>
                            <th class="px-6 py-4 border-b border-gray-100 text-right">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">Kiểm tra giữa kỳ C++</td>
                            <td class="px-6 py-4 text-sm text-gray-600">Lập trình cơ bản</td>
                            <td class="px-6 py-4 text-sm text-gray-600">CNTT-K62</td>
                            <td class="px-6 py-4 text-sm text-gray-500">10:00 - 15/08/2026</td>
                            <td class="px-6 py-4 text-right">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                                    Sắp diễn ra
                                </span>
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">Thi cuối kỳ Web</td>
                            <td class="px-6 py-4 text-sm text-gray-600">Phát triển ứng dụng Web</td>
                            <td class="px-6 py-4 text-sm text-gray-600">CNTT-K61</td>
                            <td class="px-6 py-4 text-sm text-gray-500">14:00 - 20/08/2026</td>
                            <td class="px-6 py-4 text-right">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                    Chưa bắt đầu
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800">Hoạt động gần đây</h3>
            </div>
            <div class="p-6">
                <div class="relative pl-4 space-y-6 before:absolute before:inset-y-0 before:left-5 before:w-px before:bg-gray-200">
                    <div class="relative flex gap-4">
                        <div class="w-2 h-2 mt-1.5 rounded-full bg-blue-500 ring-4 ring-white relative z-10"></div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Thêm 15 câu hỏi mới</p>
                            <p class="text-xs text-gray-500 mt-0.5">Môn: Cấu trúc dữ liệu và giải thuật</p>
                            <p class="text-xs text-gray-400 mt-1">2 giờ trước</p>
                        </div>
                    </div>
                    <div class="relative flex gap-4">
                        <div class="w-2 h-2 mt-1.5 rounded-full bg-emerald-500 ring-4 ring-white relative z-10"></div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Hoàn thành chấm điểm thi</p>
                            <p class="text-xs text-gray-500 mt-0.5">Lớp: Toán rời rạc K62</p>
                            <p class="text-xs text-gray-400 mt-1">Hôm qua</p>
                        </div>
                    </div>
                    <div class="relative flex gap-4">
                        <div class="w-2 h-2 mt-1.5 rounded-full bg-amber-500 ring-4 ring-white relative z-10"></div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Tạo kỳ thi mới</p>
                            <p class="text-xs text-gray-500 mt-0.5">Kiểm tra 15 phút - Lập trình C</p>
                            <p class="text-xs text-gray-400 mt-1">3 ngày trước</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
