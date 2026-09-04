<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Quản trị') - EyeExam</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/alpinejs" defer></script>
    <style>
        body {
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        }
    </style>
</head>
<body class="h-full antialiased text-slate-900 bg-slate-50 flex overflow-hidden" x-data="{ sidebarOpen: false }">

    <!-- Mobile Sidebar Backdrop -->
    <div x-show="sidebarOpen" 
         x-transition:enter="transition-opacity ease-linear duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-40 bg-slate-900/40 lg:hidden" 
         @click="sidebarOpen = false" 
         style="display: none;"></div>

    <!-- Sidebar (Width cố định 240px) -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
           class="fixed inset-y-0 left-0 z-50 w-[240px] bg-white border-r border-slate-200 flex flex-col transition-transform duration-200 ease-in-out lg:static lg:translate-x-0 shrink-0">
        
        <!-- App Brand / Logo -->
        <div class="h-14 flex items-center justify-between px-4 border-b border-slate-200 shrink-0">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-md bg-blue-600 flex items-center justify-center text-white shrink-0">
                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="text-sm font-bold text-slate-900 tracking-tight">EyeExam</span>
                    <span class="text-[10px] font-medium text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded border border-slate-200">Admin</span>
                </div>
            </a>
            <button @click="sidebarOpen = false" class="p-1 rounded text-slate-400 hover:text-slate-600 lg:hidden">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
            <div class="px-2 pb-1 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Tổng quan</div>
            
            <a href="{{ route('admin.dashboard') }}" 
               class="group flex items-center gap-2.5 px-2.5 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70' }}">
                <svg class="w-4.5 h-4.5 shrink-0 {{ request()->routeIs('admin.dashboard') ? 'text-blue-600' : 'text-slate-400 group-hover:text-slate-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Dashboard
            </a>

            <div class="pt-4 px-2 pb-1 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Đào tạo & Khóa học</div>

            <a href="{{ route('admin.khoa-hoc.index') }}" 
               class="group flex items-center gap-2.5 px-2.5 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('admin.khoa-hoc.*') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70' }}">
                <svg class="w-4.5 h-4.5 shrink-0 {{ request()->routeIs('admin.khoa-hoc.*') ? 'text-blue-600' : 'text-slate-400 group-hover:text-slate-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477-4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                Quản lý Khóa học
            </a>

            <a href="{{ route('admin.subjects.index') }}" 
               class="group flex items-center gap-2.5 px-2.5 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('admin.subjects.*') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70' }}">
                <svg class="w-4.5 h-4.5 shrink-0 {{ request()->routeIs('admin.subjects.*') ? 'text-blue-600' : 'text-slate-400 group-hover:text-slate-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Quản lý Môn học
            </a>

            <a href="{{ route('admin.classes.index') }}" 
               class="group flex items-center gap-2.5 px-2.5 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('admin.classes.*') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70' }}">
                <svg class="w-4.5 h-4.5 shrink-0 {{ request()->routeIs('admin.classes.*') ? 'text-blue-600' : 'text-slate-400 group-hover:text-slate-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                Quản lý Lớp học
            </a>

            <div class="pt-4 px-2 pb-1 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Khảo thí & Tài khoản</div>

            <a href="{{ route('admin.exams.index') }}" 
               class="group flex items-center gap-2.5 px-2.5 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('admin.exams.*') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70' }}">
                <svg class="w-4.5 h-4.5 shrink-0 {{ request()->routeIs('admin.exams.*') ? 'text-blue-600' : 'text-slate-400 group-hover:text-slate-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
                Quản lý Đề thi
            </a>

            <a href="{{ route('admin.users.index') }}" 
               class="group flex items-center gap-2.5 px-2.5 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('admin.users.*') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70' }}">
                <svg class="w-4.5 h-4.5 shrink-0 {{ request()->routeIs('admin.users.*') ? 'text-blue-600' : 'text-slate-400 group-hover:text-slate-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                Quản lý Người dùng
            </a>
        </nav>
        <!-- User profile in sidebar -->
        <div class="p-4 border-t border-slate-800 bg-slate-950 shrink-0">
            <div class="flex items-center gap-3 px-4 py-3 bg-slate-900 rounded-xl border border-slate-800">
                <div class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-white font-bold border border-slate-700 shrink-0 overflow-hidden">
                    @if(auth()->user()->avatar_url)
                        <img src="{{ auth()->user()->avatar_url }}" class="w-full h-full object-cover" alt="{{ auth()->user()->name }}">
                    @else
                        {{ substr(auth()->user()->name, 0, 1) }}
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-white truncate leading-tight">{{ auth()->user()->name }}</p>
                    <p class="text-[11px] text-slate-400 truncate leading-tight">{{ auth()->user()->email }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="shrink-0">
                    @csrf
                    <button type="submit" class="p-1.5 text-slate-400 hover:text-white hover:bg-slate-800 rounded transition-colors" title="Đăng xuất">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden bg-slate-50">
        <!-- Top Header (Height 56px, Simple, Clean) -->
        <header class="h-14 bg-white border-b border-slate-200 sticky top-0 z-30 px-4 sm:px-6 lg:px-8 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-3">
                <button @click="sidebarOpen = true" class="p-1.5 -ml-1 text-slate-600 rounded-md hover:bg-slate-100 lg:hidden">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <div class="flex items-center gap-2 text-sm">
                    <span class="text-slate-500 font-medium">Admin</span>
                    <span class="text-slate-300">/</span>
                    <span class="text-slate-900 font-semibold">@yield('title', 'Tổng quan')</span>
                </div>
            </div>

            <div class="flex items-center gap-4 text-xs font-medium text-slate-500">
                <span>{{ now()->locale('vi')->isoFormat('dddd, DD/MM/YYYY') }}</span>
            </div>
        </header>

        <!-- Main Scrollable Content Container -->
        <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
            <div class="max-w-7xl mx-auto space-y-6">
                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>
