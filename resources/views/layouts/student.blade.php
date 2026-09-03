<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Cổng Sinh viên') - Eye Exam</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/alpinejs" defer></script>
</head>
<body class="antialiased font-sans text-slate-800 bg-[#F8FAFC] flex h-screen overflow-hidden" x-data="{ sidebarOpen: false }">

    <!-- Mobile Sidebar Backdrop -->
    <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-40 bg-slate-900/40 backdrop-blur-xs lg:hidden" @click="sidebarOpen = false" style="display: none;"></div>

    <!-- Sidebar (w-64) -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-slate-200/80 flex flex-col transition-transform duration-300 lg:static lg:translate-x-0 shadow-xs -translate-x-full">
        <!-- Logo -->
        <div class="h-16 flex items-center px-6 border-b border-slate-100 shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 bg-blue-600 rounded-xl flex items-center justify-center text-white shadow-sm shadow-blue-500/30 font-bold shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path></svg>
                </div>
                <span class="text-xl font-extrabold text-slate-900 tracking-tight">Eye<span class="text-blue-600">Exam</span></span>
            </div>
            <!-- Close button for mobile -->
            <button @click="sidebarOpen = false" class="ml-auto lg:hidden text-slate-400 hover:text-slate-600 p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Nav Links -->
        <nav class="flex-1 px-3 py-5 space-y-1.5 overflow-y-auto">
            <div class="px-3 mb-2 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Menu Chính</div>

            <a href="{{ route('student.dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all duration-150 {{ request()->routeIs('student.dashboard') ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/25' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                <svg class="w-4 h-4 {{ request()->routeIs('student.dashboard') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                Tổng quan
            </a>
            <a href="{{ route('student.classes.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all duration-150 {{ request()->routeIs('student.classes.*') ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/25' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                <svg class="w-4 h-4 {{ request()->routeIs('student.classes.*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 v5m-4 0h4"></path></svg>
                Lớp học của tôi
            </a>
            <a href="{{ route('student.khoa-hoc.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all duration-150 {{ request()->routeIs('student.khoa-hoc.*') ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/25' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                <svg class="w-4 h-4 {{ request()->routeIs('student.khoa-hoc.*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477-4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                Khóa học & Môn học
            </a>
            <a href="{{ route('student.exams.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all duration-150 {{ request()->routeIs('student.exams.*') ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/25' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                <svg class="w-4 h-4 {{ request()->routeIs('student.exams.*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                Kỳ thi của tôi
            </a>
            @if(auth()->user() && !auth()->user()->face_registered)
            <a href="{{ route('student.face.register') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all duration-150 {{ request()->routeIs('student.face.*') ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/25' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                <div class="flex items-center gap-3">
                    <svg class="w-4 h-4 {{ request()->routeIs('student.face.*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>Đăng ký Face ID</span>
                </div>
                <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping" title="Cần đăng ký"></span>
            </a>
            @endif
            <a href="{{ route('student.profile') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all duration-150 {{ request()->routeIs('student.profile') ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/25' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                <svg class="w-4 h-4 {{ request()->routeIs('student.profile') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                Thông tin sinh viên
            </a>
        </nav>
        <div class="p-3 border-t border-slate-100 shrink-0">
            <div class="flex items-center gap-2.5 p-2 bg-slate-50 hover:bg-slate-100 rounded-xl border border-slate-200/60 transition-colors">
                <a href="{{ route('student.profile') }}" class="flex items-center gap-2.5 flex-1 min-w-0">
                    <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold shrink-0 overflow-hidden border border-blue-200 text-xs">
                        @if(auth()->user()->frontal_face_url)
                            <img src="{{ auth()->user()->frontal_face_url }}" class="w-full h-full object-cover" alt="{{ auth()->user()->name }}">
                        @else
                            {{ substr(auth()->user()->name, 0, 1) }}
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-slate-900 truncate hover:text-blue-600 transition-colors">{{ auth()->user()->name }}</p>
                        <p class="text-[10px] text-slate-500 truncate">{{ auth()->user()->code ?? 'Sinh viên' }}</p>
                    </div>
                </a>
                <form method="POST" action="{{ route('logout') }}" class="flex-shrink-0">
                    @csrf
                    <button type="submit" class="p-1 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-rose-50 transition-colors" title="Đăng xuất">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content Wrapper -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden bg-[#F8FAFC]">
        <!-- Top Header Bar -->
        <header class="h-16 bg-white border-b border-slate-200/80 sticky top-0 z-30 px-4 sm:px-6 lg:px-8 flex items-center justify-between shrink-0 shadow-2xs gap-4">
            <!-- Left: Mobile Toggle & Global Search -->
            <div class="flex items-center gap-3 flex-1 max-w-xl">
                <button @click="sidebarOpen = true" class="p-2 -ml-2 text-slate-600 rounded-xl hover:bg-slate-100 lg:hidden transition-colors shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                
                <!-- Search Input -->
                <div class="relative w-full max-w-md hidden sm:block">
                    <input type="text" placeholder="Tìm kiếm kỳ thi, môn học..." class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200/80 rounded-xl text-xs font-medium text-slate-700 placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
            </div>

            <!-- Right side: Semester, Status, Notifications & Avatar -->
            <div class="flex items-center gap-3">
                <!-- Semester Selector -->
                <select class="hidden md:block text-xs font-semibold bg-slate-50 text-slate-700 border border-slate-200/80 px-3 py-1.5 rounded-xl focus:outline-none focus:border-blue-500">
                    <option value="hk1">Học kỳ 1 - 2026-2027</option>
                    <option value="hk2">Học kỳ 2 - 2026-2027</option>
                </select>

                <!-- System Ready Status Badge -->
                <span class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Hệ thống sẵn sàng
                </span>

                <!-- Notification Icon -->
                <button type="button" class="p-2 text-slate-400 hover:text-blue-600 hover:bg-slate-50 rounded-xl transition-colors relative" title="Thông báo">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-blue-600 rounded-full border border-white"></span>
                </button>

                <div class="w-px h-6 bg-slate-200 hidden sm:block"></div>

                <!-- Avatar on Right -->
                <a href="{{ route('student.profile') }}" class="w-8 h-8 rounded-full bg-blue-600 text-white font-bold flex items-center justify-center text-xs overflow-hidden border border-blue-500 shadow-xs shrink-0" title="Trang cá nhân">
                    @if(auth()->user()->frontal_face_url)
                        <img src="{{ auth()->user()->frontal_face_url }}" class="w-full h-full object-cover" alt="{{ auth()->user()->name }}">
                    @else
                        {{ substr(auth()->user()->name, 0, 1) }}
                    @endif
                </a>
            </div>
        </header>

        <!-- Main Scrollable Content -->
        <main class="flex-1 overflow-y-auto p-6 lg:p-8">
            <div class="max-w-6xl mx-auto">
                @yield('content')
            </div>

            <!-- Footer -->
            <footer class="mt-12 mb-6">
                <div class="max-w-6xl mx-auto text-center">
                    <p class="text-xs font-medium text-slate-400">
                        &copy; {{ date('Y') }} Eye Exam System. All rights reserved.
                    </p>
                </div>
            </footer>
        </main>
    </div>
    @stack('scripts')
</body>
</html>