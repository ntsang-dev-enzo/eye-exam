@extends('layouts.student')

@section('title', 'Lớp học của tôi')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">Lớp học của tôi</h1>
            <p class="text-sm text-gray-500 font-medium">Danh sách các lớp học bạn đang tham gia trong học kỳ</p>
        </div>
    </div>

    <!-- Classes Grid -->
    @if($classes->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($classes as $class)
                <div class="bg-white rounded-3xl border border-gray-100 p-6 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between group">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="px-3 py-1 bg-indigo-50 text-indigo-700 font-bold text-xs rounded-full border border-indigo-100">
                                {{ $class->code }}
                            </span>
                            @if($class->course)
                                <span class="text-xs font-bold text-indigo-600">
                                    {{ $class->course->semester }}
                                </span>
                            @endif
                        </div>

                        <div>
                            <h2 class="text-xl font-black text-gray-900 group-hover:text-indigo-600 transition-colors leading-snug">
                                {{ $class->name }}
                            </h2>
                            @if($class->course)
                                <p class="text-xs text-gray-500 mt-1">Khóa: {{ $class->course->name }} ({{ $class->course->academic_year }})</p>
                            @endif
                            @if($class->description)
                                <p class="text-xs text-gray-400 mt-2 line-clamp-2">{{ $class->description }}</p>
                            @endif
                        </div>

                        <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100 space-y-2">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-bold text-gray-500">Giảng viên CN:</span>
                                <span class="font-black text-gray-800">{{ $class->teacher->name ?? 'N/A' }}</span>
                            </div>
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-bold text-gray-500">Sĩ số lớp:</span>
                                <span class="font-black text-indigo-600">{{ $class->students_count }} sinh viên</span>
                            </div>
                        </div>
                    </div>

                    <div class="pt-6">
                        <a href="{{ route('student.classes.show', $class) }}" 
                            class="w-full py-3 px-4 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white font-bold text-sm rounded-2xl shadow-lg shadow-indigo-500/20 transition-all flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            Xem chi tiết Lớp
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $classes->links() }}
        </div>
    @else
        <div class="bg-white rounded-3xl border border-gray-100 p-12 text-center">
            <div class="w-16 h-16 bg-indigo-50 text-indigo-600 rounded-3xl flex items-center justify-center mx-auto mb-4 border border-indigo-100">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 v5m-4 0h4"></path></svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-1">Chưa tham gia lớp học nào</h3>
            <p class="text-sm text-gray-500">Bạn hiện tại chưa được xếp vào lớp học nào.</p>
        </div>
    @endif
</div>
@endsection
