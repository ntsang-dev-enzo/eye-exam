@extends('layouts.teacher')

@section('title', 'Quản lý Lớp học')

@section('content')
<div class="max-w-6xl mx-auto space-y-8" x-data="{ activeTab: 'homeroom' }">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-black text-gray-900 tracking-tight">Quản lý Lớp học</h2>
            <p class="text-sm text-gray-500 font-medium">Danh sách các lớp học bạn làm chủ nhiệm và các lớp được phân công dạy bộ môn</p>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <div class="flex border-b border-gray-200 gap-6">
        <button @click="activeTab = 'homeroom'" 
            :class="{ 'border-indigo-600 text-indigo-600 font-black': activeTab === 'homeroom', 'border-transparent text-gray-500 font-medium hover:text-gray-700': activeTab !== 'homeroom' }" 
            class="py-3 px-1 border-b-2 text-sm transition-all flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            Lớp học làm Chủ nhiệm ({{ $homeroomClasses->count() }})
        </button>

        <button @click="activeTab = 'teaching'" 
            :class="{ 'border-indigo-600 text-indigo-600 font-black': activeTab === 'teaching', 'border-transparent text-gray-500 font-medium hover:text-gray-700': activeTab !== 'teaching' }" 
            class="py-3 px-1 border-b-2 text-sm transition-all flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
            Lớp Phân công Giảng dạy ({{ $teachingClasses->count() }})
        </button>
    </div>

    <!-- Tab 1: Homeroom Classes -->
    <div x-show="activeTab === 'homeroom'">
        @if($homeroomClasses->isEmpty())
            <div class="bg-white p-12 rounded-3xl border border-gray-100 shadow-sm text-center">
                <div class="w-16 h-16 bg-indigo-50 text-indigo-600 rounded-3xl flex items-center justify-center mx-auto mb-4 border border-indigo-100">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-1">Không làm chủ nhiệm lớp nào</h3>
                <p class="text-sm text-gray-500">Bạn hiện tại chưa làm giảng viên chủ nhiệm cho lớp học nào.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($homeroomClasses as $class)
                    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm hover:shadow-xl transition-all duration-300 p-6 flex flex-col justify-between group">
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="px-3 py-1 bg-indigo-50 text-indigo-700 font-bold text-xs rounded-full border border-indigo-100">
                                    {{ $class->code }}
                                </span>
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $class->status == 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                    {{ $class->status == 'active' ? 'Hoạt động' : 'Khóa' }}
                                </span>
                            </div>
                            
                            <div>
                                <h3 class="text-xl font-black text-gray-900 group-hover:text-indigo-600 transition-colors mb-1">{{ $class->name }}</h3>
                                @if($class->course)
                                    <p class="text-xs text-indigo-600 font-semibold mb-2">Khóa: {{ $class->course->name }} ({{ $class->course->semester }})</p>
                                @endif
                                <p class="text-xs text-gray-500 line-clamp-2">{{ $class->description ?? 'Không có mô tả' }}</p>
                            </div>
                        </div>

                        <div class="mt-6 pt-4 border-t border-gray-100 flex items-center justify-between">
                            <div class="flex items-center text-xs font-bold text-gray-600">
                                <svg class="w-4 h-4 mr-1.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                {{ $class->students_count }} sinh viên
                            </div>
                            <a href="{{ route('teacher.classes.show', $class) }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-sm transition-colors">
                                Quản lý Lớp
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Tab 2: Teaching Classes -->
    <div x-show="activeTab === 'teaching'" style="display: none;">
        @if($teachingClasses->isEmpty())
            <div class="bg-white p-12 rounded-3xl border border-gray-100 shadow-sm text-center">
                <div class="w-16 h-16 bg-indigo-50 text-indigo-600 rounded-3xl flex items-center justify-center mx-auto mb-4 border border-indigo-100">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-1">Chưa có phân công bộ môn</h3>
                <p class="text-sm text-gray-500">Bạn chưa được phân công dạy môn học nào cho các lớp khác.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($teachingClasses as $class)
                    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm hover:shadow-xl transition-all duration-300 p-6 flex flex-col justify-between group">
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="px-3 py-1 bg-indigo-50 text-indigo-700 font-bold text-xs rounded-full border border-indigo-100">
                                    {{ $class->code }}
                                </span>
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                    Giảng dạy
                                </span>
                            </div>
                            
                            <div>
                                <h3 class="text-xl font-black text-gray-900 group-hover:text-indigo-600 transition-colors mb-1">{{ $class->name }}</h3>
                                @if($class->course)
                                    <p class="text-xs text-indigo-600 font-semibold mb-2">Khóa: {{ $class->course->name }} ({{ $class->course->semester }})</p>
                                @endif
                                <p class="text-xs text-gray-500 line-clamp-2">{{ $class->description ?? 'Không có mô tả' }}</p>
                            </div>
                        </div>

                        <div class="mt-6 pt-4 border-t border-gray-100 flex items-center justify-between">
                            <div class="flex items-center text-xs font-bold text-gray-600">
                                <svg class="w-4 h-4 mr-1.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                {{ $class->students_count }} sinh viên
                            </div>
                            <a href="{{ route('teacher.classes.show', $class) }}" class="px-4 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-xs rounded-xl transition-colors">
                                Xem Danh sách
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
