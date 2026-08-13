<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eye Exam - Nền tảng thi trực tuyến CTUT</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
        }

        @keyframes fade-in-up {
            from {
                opacity: 0;
                transform: translateY(24px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate-fade-in-up {
            animation: fade-in-up 1s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
        }

        /* Subtle grid background */
        .bg-grid-pattern {
            background-image: linear-gradient(to right, #cbd5e1 1px, transparent 1px),
                              linear-gradient(to bottom, #cbd5e1 1px, transparent 1px);
            background-size: 40px 40px;
            mask-image: radial-gradient(circle at center, black, transparent 80%);
            -webkit-mask-image: radial-gradient(circle at center, black, transparent 80%);
        }
        
        .glass-panel {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.8);
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-800 antialiased selection:bg-blue-200 selection:text-blue-900 flex flex-col min-h-screen">

    <!-- NAVBAR -->
    <nav class="fixed top-0 w-full z-50 glass-panel border-b border-slate-200/50 shadow-sm transition-all">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <!-- LOGO -->
            <a href="/" class="flex items-center gap-3 group">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-blue-600 to-cyan-500 flex items-center justify-center shadow-lg shadow-blue-500/30 group-hover:shadow-blue-500/50 transition-all duration-300">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 14l9-5-9-5-9 5 9 5z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                    </svg>
                </div>
                <div>
                    <span class="block font-extrabold text-xl text-slate-900 tracking-tight">Eye Exam</span>
                    <span class="block text-[10px] font-bold text-blue-600 uppercase tracking-wider">CTUT Portal</span>
                </div>
            </a>

            <!-- BUTTON -->
            <div class="flex items-center gap-4">
                <a href="/login" class="px-7 py-2.5 rounded-full bg-slate-900 text-white font-medium hover:bg-blue-600 hover:shadow-xl hover:shadow-blue-600/25 transition-all duration-300 active:scale-95 flex items-center gap-2">
                    <span>Đăng nhập</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>
        </div>
    </nav>

    <!-- HERO -->
    <main class="flex-grow flex flex-col justify-center relative overflow-hidden bg-slate-50 pt-20">
        <!-- Grid Background -->
        <div class="absolute inset-0 bg-grid-pattern opacity-40 pointer-events-none"></div>
        
        <!-- Abstract Orbs -->
        <div class="absolute inset-0 pointer-events-none overflow-hidden flex justify-center items-center">
            <div class="absolute w-[800px] h-[800px] rounded-full bg-gradient-to-tr from-blue-300/30 to-cyan-300/30 blur-[100px] translate-x-1/4 -translate-y-1/4"></div>
            <div class="absolute w-[600px] h-[600px] rounded-full bg-gradient-to-bl from-indigo-300/20 to-blue-200/20 blur-[80px] -translate-x-1/3 translate-y-1/3"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-6 py-20 md:py-28 flex flex-col items-center text-center z-10">
            
            <div class="inline-flex items-center gap-2 px-5 py-2 rounded-full glass-panel border border-blue-200/50 text-blue-700 text-sm font-semibold mb-8 animate-fade-in-up shadow-sm">
                <span class="relative flex h-2 w-2">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-500 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-600"></span>
                </span>
                Hệ thống thi trực tuyến thông minh
            </div>

            <h1 class="text-5xl md:text-6xl lg:text-[5rem] font-extrabold text-slate-900 tracking-tight leading-[1.1] max-w-5xl animate-fade-in-up" style="animation-delay: 100ms;">
                Nền tảng đánh giá năng lực <br />
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 via-indigo-600 to-cyan-500">công bằng & minh bạch</span>
            </h1>

            <p class="mt-8 text-lg md:text-xl text-slate-600 max-w-2xl leading-relaxed animate-fade-in-up font-medium" style="animation-delay: 200ms;">
                Dành riêng cho sinh viên trường <br class="hidden md:block" />
                <span class="text-blue-700 font-bold text-xl md:text-2xl mt-1 block">Đại học Kỹ thuật - Công nghệ Cần Thơ</span>
            </p>

            <div class="mt-12 flex flex-col sm:flex-row gap-5 animate-fade-in-up" style="animation-delay: 300ms;">
                <a href="/login" class="px-8 py-4 rounded-full bg-blue-600 text-white font-semibold text-lg hover:bg-blue-700 hover:shadow-2xl hover:shadow-blue-600/30 transition-all duration-300 active:scale-95 flex items-center justify-center gap-3">
                    Tham gia kỳ thi ngay
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
            
            <!-- CTUT Highlight Card -->
            <div class="mt-20 animate-fade-in-up w-full max-w-4xl" style="animation-delay: 400ms;">
                <div class="glass-panel rounded-[2rem] p-2 shadow-xl shadow-slate-200/50">
                    <div class="bg-white/80 backdrop-blur-md rounded-[1.5rem] p-8 md:p-10 flex flex-col lg:flex-row items-center justify-between gap-10">
                        <div class="flex-1 text-left">
                            <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-blue-50 text-blue-600 mb-4">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            </div>
                            <h3 class="text-2xl font-bold text-slate-900 mb-3">Tự hào đồng hành cùng CTUT</h3>
                            <p class="text-slate-600 leading-relaxed text-sm md:text-base">Môi trường thi trực tuyến an toàn, áp dụng công nghệ nhận diện khuôn mặt (AI Proctoring) tiên tiến giúp duy trì sự nghiêm túc và công bằng tuyệt đối cho mọi sinh viên.</p>
                        </div>
                        <div class="flex flex-wrap lg:flex-nowrap gap-4 justify-center">
                            <!-- Feature mini badges -->
                            <div class="flex flex-col items-center justify-center p-4 rounded-2xl bg-blue-50/80 border border-blue-100 min-w-[110px] hover:-translate-y-1 transition-transform">
                                <span class="text-3xl mb-2">📸</span>
                                <span class="text-xs font-bold text-blue-700 text-center uppercase tracking-wide">Face ID</span>
                            </div>
                            <div class="flex flex-col items-center justify-center p-4 rounded-2xl bg-indigo-50/80 border border-indigo-100 min-w-[110px] hover:-translate-y-1 transition-transform">
                                <span class="text-3xl mb-2">👁️</span>
                                <span class="text-xs font-bold text-indigo-700 text-center uppercase tracking-wide">Giám sát 24/7</span>
                            </div>
                            <div class="flex flex-col items-center justify-center p-4 rounded-2xl bg-cyan-50/80 border border-cyan-100 min-w-[110px] hover:-translate-y-1 transition-transform">
                                <span class="text-3xl mb-2">📊</span>
                                <span class="text-xs font-bold text-cyan-700 text-center uppercase tracking-wide">Minh bạch</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- FOOTER -->
    <footer class="bg-white border-t border-slate-200/60 py-8 relative z-10">
        <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <span class="font-bold text-slate-800 text-lg">Eye Exam <span class="text-blue-600">CTUT</span></span>
            </div>
            <p class="text-sm text-slate-500 font-medium">© {{ date('Y') }} Trường Đại học Kỹ thuật - Công nghệ Cần Thơ. Phát triển nội bộ.</p>
        </div>
    </footer>

</body>
</html>