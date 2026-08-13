@extends('layouts.teacher')

@section('title', 'Chi tiết Lớp học')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('teacher.classes.index') }}" class="text-sm font-medium text-gray-500 hover:text-blue-600 transition-colors">Quản lý lớp</a>
                <span class="text-gray-400">/</span>
                <span class="text-sm font-medium text-gray-900">{{ $class->code }}</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $class->name }}</h1>
        </div>
        <div>
            <a href="{{ route('teacher.assignments.create', ['class_id' => $class->id]) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                Giao đề cho lớp này
            </a>
        </div>
    </div>

    <!-- Stats or Info -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
        <div>
            <p class="text-sm text-gray-500 mb-1">Mã lớp</p>
            <p class="font-semibold text-gray-900">{{ $class->code }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500 mb-1">Sĩ số</p>
            <p class="font-semibold text-gray-900">{{ $students->count() }} sinh viên</p>
        </div>
        <div>
            <p class="text-sm text-gray-500 mb-1">Trạng thái</p>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $class->status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                {{ $class->status ? 'Đang hoạt động' : 'Đã đóng' }}
            </span>
        </div>
    </div>

    <!-- Student List -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row justify-between items-center gap-4">
            <h3 class="text-lg font-bold text-gray-800">Danh sách sinh viên</h3>
            
            <form action="{{ route('teacher.classes.show', $class) }}" method="GET" class="flex items-center w-full sm:w-80">
                <div class="relative w-full">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm theo tên, mã SV, email..." class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                </div>
                @if(request('search'))
                    <a href="{{ route('teacher.classes.show', $class) }}" class="ml-2 px-3 py-2 text-sm text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg whitespace-nowrap transition-colors">Xóa lọc</a>
                @endif
            </form>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 border-b border-gray-100 text-xs uppercase text-gray-500 font-semibold">
                    <tr>
                        <th class="px-6 py-3 w-16 text-center">STT</th>
                        <th class="px-6 py-3">Mã SV</th>
                        <th class="px-6 py-3">Họ và tên</th>
                        <th class="px-6 py-3">Email</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($students as $index => $student)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 text-center text-gray-400">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $student->code ?? 'N/A' }}</td>
                            <td class="px-6 py-4 font-medium text-gray-900">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold text-xs">
                                        {{ substr($student->name, 0, 1) }}
                                    </div>
                                    {{ $student->name }}
                                </div>
                            </td>
                            <td class="px-6 py-4">{{ $student->email }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                Không tìm thấy sinh viên nào.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
