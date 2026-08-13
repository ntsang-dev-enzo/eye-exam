@extends('layouts.student')

@section('title', 'Khóa học của tôi')

@section('content')
<div class="space-y-6" x-data="{ selectedCourse: null }">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">Khóa học & Môn học</h1>
            <p class="text-sm text-gray-500 font-medium">Danh sách các chương trình đào tạo và môn học trong học kỳ</p>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
        <form method="GET" action="{{ route('student.khoa-hoc.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="sm:col-span-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="🔍 Tìm kiếm khóa học..." 
                    class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium text-gray-800 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all">
            </div>
            <div>
                <select name="semester" onchange="this.form.submit()" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium text-gray-800 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all">
                    <option value="">-- Tất cả học kỳ --</option>
                    <option value="Học kỳ 1" {{ request('semester') == 'Học kỳ 1' ? 'selected' : '' }}>Học kỳ 1</option>
                    <option value="Học kỳ 2" {{ request('semester') == 'Học kỳ 2' ? 'selected' : '' }}>Học kỳ 2</option>
                    <option value="Học kỳ 3" {{ request('semester') == 'Học kỳ 3' ? 'selected' : '' }}>Học kỳ 3</option>
                </select>
            </div>
        </form>
    </div>

    <!-- Courses Grid -->
    @if($courses->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($courses as $course)
                <div class="bg-white rounded-3xl border border-gray-100 p-6 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between group">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="px-3 py-1 bg-indigo-50 text-indigo-700 font-bold text-xs rounded-full border border-indigo-100">
                                {{ $course->semester }} - {{ $course->academic_year }}
                            </span>
                            <span class="flex items-center gap-1.5 text-xs font-bold text-emerald-600">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                Hoạt động
                            </span>
                        </div>

                        <div>
                            <h2 class="text-xl font-black text-gray-900 group-hover:text-indigo-600 transition-colors leading-snug">
                                {{ $course->name }}
                            </h2>
                            @if($course->description)
                                <p class="text-xs text-gray-500 mt-2 line-clamp-2">{{ $course->description }}</p>
                            @endif
                        </div>

                        <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100 flex items-center justify-between">
                            <span class="text-xs font-bold text-gray-600">Số môn học</span>
                            <span class="text-sm font-black text-indigo-600">{{ $course->subjects->count() }} môn</span>
                        </div>
                    </div>

                    <div class="pt-6">
                        <button type="button" @click="selectedCourse = {{ json_encode($course) }}" 
                            class="w-full py-3 px-4 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white font-bold text-sm rounded-2xl shadow-lg shadow-indigo-500/20 transition-all flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            Xem môn học
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white rounded-3xl border border-gray-100 p-12 text-center">
            <div class="w-16 h-16 bg-indigo-50 text-indigo-600 rounded-3xl flex items-center justify-center mx-auto mb-4 border border-indigo-100">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-1">Chưa có khóa học nào</h3>
            <p class="text-sm text-gray-500">Hiện tại chưa có thông tin khóa học mới.</p>
        </div>
    @endif

    <!-- Subjects Modal -->
    <template x-teleport="body">
        <div x-show="selectedCourse !== null" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="selectedCourse = null"></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative bg-white rounded-3xl p-8 max-w-xl w-full shadow-2xl text-left border border-slate-100">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-4 mb-6">
                        <div>
                            <span class="text-xs font-bold text-indigo-600 uppercase tracking-wider block" x-text="selectedCourse?.semester + ' (' + selectedCourse?.academic_year + ')'"></span>
                            <h3 class="text-xl font-black text-gray-900" x-text="selectedCourse?.name"></h3>
                        </div>
                        <button type="button" @click="selectedCourse = null" class="p-2 text-gray-400 hover:text-gray-600 rounded-full hover:bg-gray-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <div class="space-y-3 max-h-80 overflow-y-auto pr-1">
                        <template x-for="subject in selectedCourse?.subjects" :key="subject.id">
                            <div class="p-4 rounded-2xl border border-gray-100 bg-gray-50 flex items-center justify-between">
                                <div>
                                    <h4 class="font-bold text-gray-900 text-sm" x-text="subject.name"></h4>
                                    <span class="text-xs font-mono text-gray-400" x-text="'Mã môn: ' + subject.code"></span>
                                </div>
                                <span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full font-bold text-xs">
                                    Đang học
                                </span>
                            </div>
                        </template>
                    </div>

                    <div class="mt-6 pt-4 border-t border-gray-100 text-right">
                        <button type="button" @click="selectedCourse = null" class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-sm rounded-xl">
                            Đóng
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>
@endsection
