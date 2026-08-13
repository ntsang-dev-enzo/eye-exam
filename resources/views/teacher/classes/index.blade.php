@extends('layouts.teacher')

@section('title', 'Quản lý Lớp học')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex justify-between items-center">
        <h2 class="text-xl font-bold text-gray-800">Danh sách các lớp giảng dạy</h2>
    </div>

    @if($classes->isEmpty())
        <div class="bg-white p-8 rounded-xl border border-gray-100 shadow-sm text-center">
            <p class="text-gray-500">Bạn chưa được phân công giảng dạy lớp nào.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($classes as $class)
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $class->status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $class->status ? 'Đang hoạt động' : 'Đã đóng' }}
                            </span>
                        </div>
                        <span class="text-sm font-semibold text-gray-500">{{ $class->code }}</span>
                    </div>
                    
                    <h3 class="text-lg font-bold text-gray-900 mb-2">{{ $class->name }}</h3>
                    <p class="text-sm text-gray-500 mb-4 line-clamp-2">{{ $class->description ?? 'Không có mô tả' }}</p>
                    
                    <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-50">
                        <div class="flex items-center text-sm text-gray-500">
                            <svg class="w-5 h-5 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            {{ $class->students_count }} sinh viên
                        </div>
                        <a href="{{ route('teacher.classes.show', $class) }}" class="text-blue-600 hover:text-blue-700 text-sm font-semibold flex items-center">
                            Xem chi tiết
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="mt-6">
            {{ $classes->links() }}
        </div>
    @endif
</div>
@endsection
