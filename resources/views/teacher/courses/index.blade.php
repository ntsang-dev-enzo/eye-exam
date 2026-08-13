@extends('layouts.teacher')

@section('title', 'Khóa học của tôi')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">Khóa học của tôi</h1>
            <p class="text-sm text-gray-500 font-medium">Danh sách các khóa học và môn học được phân công giảng dạy</p>
        </div>
    </div>

    <!-- Search & Filter Card -->
    <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
        <form method="GET" action="{{ route('teacher.khoa-hoc.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="sm:col-span-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="🔍 Tìm kiếm khóa học theo tên..." 
                    class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium text-gray-800 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all">
            </div>
            <div>
                <select name="semester" onchange="this.form.submit()" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium text-gray-800 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all">
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
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($courses as $course)
                <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm hover:shadow-md transition-all space-y-4">
                    <div class="flex items-start justify-between gap-3 border-b border-gray-50 pb-4">
                        <div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold bg-blue-50 text-blue-700 border border-blue-100 mb-2">
                                {{ $course->semester }} ({{ $course->academic_year }})
                            </span>
                            <h2 class="text-lg font-bold text-gray-900 leading-tight">{{ $course->name }}</h2>
                        </div>
                        <span class="px-3 py-1 bg-emerald-50 text-emerald-700 text-xs font-bold rounded-full border border-emerald-100">
                            Đang mở
                        </span>
                    </div>

                    @if($course->description)
                        <p class="text-xs text-gray-500 line-clamp-2">{{ $course->description }}</p>
                    @endif

                    <!-- Subjects in this course -->
                    <div class="space-y-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-gray-400 block">Danh sách Môn Học ({{ $course->subjects->count() }})</span>
                        <div class="space-y-2 max-h-48 overflow-y-auto pr-1">
                            @foreach($course->subjects as $subject)
                                @php $isMySubject = in_array($subject->id, $teacherSubjectIds); @endphp
                                <div class="flex items-center justify-between p-3 rounded-xl border {{ $isMySubject ? 'bg-blue-50/70 border-blue-200' : 'bg-gray-50 border-gray-100' }}">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-lg {{ $isMySubject ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-600' }} flex items-center justify-center text-xs font-black">
                                            {{ substr($subject->code, 0, 3) }}
                                        </div>
                                        <div>
                                            <span class="text-xs font-bold text-gray-900 block">{{ $subject->name }}</span>
                                            <span class="text-[11px] font-mono text-gray-400">{{ $subject->code }}</span>
                                        </div>
                                    </div>

                                    @if($isMySubject)
                                        <span class="px-2.5 py-1 bg-blue-600 text-white text-[11px] font-bold rounded-lg shadow-sm">
                                            Phụ trách
                                        </span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white rounded-2xl border border-gray-100 p-12 text-center">
            <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-3xl flex items-center justify-center mx-auto mb-4 border border-blue-100">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-1">Chưa có khóa học nào</h3>
            <p class="text-sm text-gray-500">Hiện tại chưa có khóa học nào đang hoạt động trong hệ thống.</p>
        </div>
    @endif
</div>
@endsection
