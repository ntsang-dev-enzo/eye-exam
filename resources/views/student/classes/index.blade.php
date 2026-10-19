@extends('layouts.student')

@section('title', 'Lớp học của tôi')

@section('content')
<div class="space-y-6 sm:space-y-8">

    <!-- Top Header Bar -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 pb-2">
        <!-- Left: Title & Description -->
        <div class="space-y-1.5 max-w-xl">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 border border-blue-100 text-blue-700 text-xs font-bold tracking-wide uppercase">
                <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
                Học kỳ hiện tại
            </div>
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black text-[#0a2540] tracking-tight">
                Lớp học của tôi
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 font-medium leading-relaxed">
                Danh sách các lớp học bạn đang tham gia trong học kỳ hiện tại.
            </p>
        </div>

        <!-- Right: Stat Card -->
        <div class="flex items-center gap-3 shrink-0">
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl sm:rounded-3xl p-4 border border-blue-100 shadow-[0_4px_20px_-4px_rgba(59,130,246,0.1)] hover:shadow-md hover:border-blue-300 transition-all duration-200 flex items-center gap-3.5 min-w-[175px] group">
                <div class="w-11 h-11 rounded-2xl bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center shrink-0 shadow-xs group-hover:scale-105 group-hover:bg-blue-600 group-hover:text-white transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
                <div>
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-0.5">Tổng số lớp</span>
                    <span class="text-xl font-black text-slate-900 font-mono">{{ $classes->count() }}</span>
                    <span class="text-xs font-bold text-slate-400 font-mono ml-1">lớp</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Classes Grid -->
    @if($classes->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($classes as $class)
                <div class="bg-white/80 backdrop-blur-sm rounded-2xl sm:rounded-3xl border border-blue-100 p-5 sm:p-6 shadow-[0_4px_20px_-4px_rgba(59,130,246,0.07)] hover:shadow-xl hover:shadow-blue-500/10 hover:border-blue-300 transition-all duration-300 flex flex-col justify-between group hover:-translate-y-1">

                    <div class="space-y-4">
                        <!-- Top Badges -->
                        <div class="flex items-center justify-between gap-2">
                            <span class="px-3 py-1 bg-blue-50 text-blue-700 font-bold text-xs rounded-xl border border-blue-100 font-mono flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                                </svg>
                                {{ $class->code }}
                            </span>
                            @if($class->semester)
                                <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-blue-600">
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500 shrink-0"></span>
                                    {{ $class->semester->name }}
                                </span>
                            @elseif($class->course)
                                <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-600">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                                    {{ $class->course->semester }}
                                </span>
                            @endif
                        </div>

                        <!-- Card Header & Icon -->
                        <div class="flex items-start gap-3.5">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 border border-blue-200/60 flex items-center justify-center text-white shrink-0 shadow-md shadow-blue-500/20 group-hover:scale-105 transition-transform duration-200">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path></svg>
                            </div>
                            <div class="min-w-0">
                                <h2 class="text-base font-black text-[#0a2540] group-hover:text-blue-600 transition-colors leading-snug line-clamp-2">
                                    {{ $class->name }}
                                </h2>
                                @if($class->subject)
                                    <p class="text-xs text-blue-600 font-semibold mt-0.5">
                                        {{ $class->subject->name }} ({{ $class->subject->credits }} TC)
                                    </p>
                                @elseif($class->course)
                                    <p class="text-xs text-slate-500 mt-0.5 truncate font-medium">
                                        Khóa: {{ $class->course->name }} ({{ $class->course->academic_year }})
                                    </p>
                                @endif
                            </div>
                        </div>

                        @if($class->description)
                            <p class="text-xs text-slate-400 line-clamp-2 leading-relaxed">{{ $class->description }}</p>
                        @endif

                        <!-- Info Panel -->
                        <div class="p-3.5 bg-blue-50/50 rounded-2xl border border-blue-100/80 space-y-2.5">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-semibold text-slate-400 text-[11px] uppercase tracking-wide flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    Giảng viên
                                </span>
                                <span class="font-bold text-slate-700 truncate max-w-[140px]">{{ $class->teacher->name ?? 'N/A' }}</span>
                            </div>
                            <div class="w-full h-px bg-blue-100"></div>
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-semibold text-slate-400 text-[11px] uppercase tracking-wide flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    Sĩ số lớp
                                </span>
                                <span class="font-mono font-bold text-slate-700 text-xs">{{ $class->students_count }} SV</span>
                            </div>
                        </div>
                    </div>

                    <!-- Action Button -->
                    <div class="pt-5">
                        <a href="{{ route('student.classes.show', $class) }}"
                            class="w-full py-3 px-4 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 active:from-blue-800 active:to-indigo-800 text-white font-bold text-xs sm:text-sm rounded-xl sm:rounded-2xl shadow-md shadow-blue-500/20 hover:shadow-lg hover:shadow-blue-500/35 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200 flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            Xem chi tiết lớp học
                        </a>
                    </div>

                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $classes->links() }}
        </div>

    @else
        <!-- Empty State -->
        <div class="bg-white/80 backdrop-blur-sm rounded-3xl border border-blue-100 p-16 text-center shadow-[0_10px_35px_-10px_rgba(59,130,246,0.08)]">
            <div class="w-20 h-20 bg-gradient-to-tr from-blue-50 to-indigo-50 text-blue-600 rounded-3xl flex items-center justify-center mx-auto mb-4 border border-blue-100 shadow-sm transition-transform duration-300 hover:scale-105">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            </div>
            <h3 class="text-lg sm:text-xl font-extrabold text-[#0a2540] mb-2">Chưa tham gia lớp học nào</h3>
            <p class="text-xs sm:text-sm text-slate-500 max-w-md mx-auto leading-relaxed">
                Bạn hiện tại chưa được xếp vào lớp học nào trong học kỳ này.
            </p>
        </div>
    @endif

</div>
@endsection
