<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đăng nhập - Eye Exam</title>

    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f0f7ff;
            background-image: 
                radial-gradient(at 0% 0%, rgba(219, 234, 254, 0.7) 0px, transparent 50%),
                radial-gradient(at 100% 0%, rgba(191, 219, 254, 0.5) 0px, transparent 50%),
                radial-gradient(at 50% 100%, rgba(224, 242, 254, 0.6) 0px, transparent 50%);
        }

        /* Modern subtle geometric pattern */
        .pattern-bg {
            background-image: radial-gradient(rgba(59, 130, 246, 0.12) 1px, transparent 1px);
            background-size: 24px 24px;
        }

        /* Pure CSS role card active styling */
        .role-card:has(input:checked) {
            background-color: #2563eb !important;
            color: #ffffff !important;
            border-color: #2563eb !important;
            box-shadow: 0 10px 25px -5px rgba(37, 99, 235, 0.35);
            transform: scale(1.02);
        }

        .role-card:has(input:checked) .check-badge {
            opacity: 1 !important;
            transform: scale(1) !important;
        }

        .role-card:not(:has(input:checked)) .check-badge {
            opacity: 0 !important;
            transform: scale(0.5) !important;
        }

        .role-card:not(:has(input:checked)) {
            background-color: #ffffff;
            color: #475569;
            border-color: #e2e8f0;
        }
        
        .role-card:not(:has(input:checked)):hover {
            border-color: #93c5fd;
            background-color: #f8faff;
        }
    </style>
</head>
<body class="antialiased min-h-screen flex items-center justify-center p-4 sm:p-6 lg:p-8 relative pattern-bg">

    <!-- Decorative subtle ambient glow in the background -->
    <div class="fixed top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[550px] h-[550px] bg-blue-400/15 rounded-full blur-3xl pointer-events-none -z-10"></div>
    <div class="fixed bottom-10 right-10 w-[300px] h-[300px] bg-sky-300/20 rounded-full blur-3xl pointer-events-none -z-10"></div>

    <!-- Main Login Card Container -->
    <div class="w-full max-w-[480px] bg-white rounded-3xl shadow-[0_20px_60px_-15px_rgba(30,58,138,0.15),0_0_0_1px_rgba(226,232,240,0.8)] overflow-hidden transition-all duration-300">
        <div class="p-7 sm:p-9 md:p-10">
            
            <!-- Top Section: Blue Lock Icon & Title -->
            <div class="text-center mb-8">
                <!-- Blue Lock Icon -->
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-blue-50 text-blue-600 ring-8 ring-blue-50/60 mb-5 shadow-inner border border-blue-100 transition-transform duration-300 hover:scale-105">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
                
                <!-- Title “ĐĂNG NHẬP” -->
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-wider uppercase">
                    ĐĂNG NHẬP
                </h1>
            </div>

            <!-- Login Form -->
            <form method="POST" action="{{ route('login') }}" class="space-y-5" id="loginForm">
                @csrf

                <!-- Error Messages Alert -->
                @if ($errors->any())
                    <div class="bg-red-50/90 border border-red-200 text-red-600 px-4 py-3.5 rounded-2xl text-sm shadow-xs flex items-start gap-3">
                        <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div class="flex-1">
                            <ul class="list-disc pl-4 space-y-1 font-medium">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <!-- Role Selection (Visual 3 Cards) -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2.5">
                        Vai trò đăng nhập
                    </label>
                    <div class="grid grid-cols-3 gap-2.5 sm:gap-3" id="roleContainer">
                        
                        <!-- Role: Sinh viên -->
                        <label class="role-card relative flex flex-col items-center justify-center py-3.5 px-2 rounded-2xl border-2 cursor-pointer transition-all duration-200 text-center select-none" data-role="student">
                            <input type="radio" name="role" value="student" class="sr-only" {{ old('role', 'student') === 'student' ? 'checked' : '' }} required>
                            
                            <!-- Checkmark Badge -->
                            <div class="check-badge absolute top-1.5 right-1.5 w-4 h-4 rounded-full bg-white text-blue-600 flex items-center justify-center shadow-xs transition-all duration-200">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>

                            <!-- Icon -->
                            <div class="mb-1.5 transition-transform duration-200">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                                </svg>
                            </div>

                            <!-- Label -->
                            <span class="text-xs sm:text-sm font-bold tracking-tight">Sinh viên</span>
                        </label>

                        <!-- Role: Giảng viên -->
                        <label class="role-card relative flex flex-col items-center justify-center py-3.5 px-2 rounded-2xl border-2 cursor-pointer transition-all duration-200 text-center select-none" data-role="teacher">
                            <input type="radio" name="role" value="teacher" class="sr-only" {{ old('role') === 'teacher' ? 'checked' : '' }} required>
                            
                            <!-- Checkmark Badge -->
                            <div class="check-badge absolute top-1.5 right-1.5 w-4 h-4 rounded-full bg-white text-blue-600 flex items-center justify-center shadow-xs transition-all duration-200">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>

                            <!-- Icon -->
                            <div class="mb-1.5 transition-transform duration-200">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                                </svg>
                            </div>

                            <!-- Label -->
                            <span class="text-xs sm:text-sm font-bold tracking-tight">Giảng viên</span>
                        </label>

                        <!-- Role: Quản lý -->
                        <label class="role-card relative flex flex-col items-center justify-center py-3.5 px-2 rounded-2xl border-2 cursor-pointer transition-all duration-200 text-center select-none" data-role="admin">
                            <input type="radio" name="role" value="admin" class="sr-only" {{ old('role') === 'admin' ? 'checked' : '' }} required>
                            
                            <!-- Checkmark Badge -->
                            <div class="check-badge absolute top-1.5 right-1.5 w-4 h-4 rounded-full bg-white text-blue-600 flex items-center justify-center shadow-xs transition-all duration-200">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>

                            <!-- Icon -->
                            <div class="mb-1.5 transition-transform duration-200">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                            </div>

                            <!-- Label -->
                            <span class="text-xs sm:text-sm font-bold tracking-tight">Quản lý</span>
                        </label>
                    </div>
                </div>

                <!-- Email Input -->
                <div>
                    <label for="email" class="block text-sm font-bold text-slate-700 mb-1.5">
                        Email
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206"></path>
                            </svg>
                        </div>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                            placeholder="Nhập địa chỉ email của bạn"
                            class="block w-full pl-11 pr-4 py-3 bg-slate-50/70 border border-slate-200 rounded-2xl text-slate-800 placeholder-slate-400 font-medium text-sm sm:text-base focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-100 outline-none transition-all duration-200">
                    </div>
                </div>

                <!-- Password Input -->
                <div>
                    <label for="password" class="block text-sm font-bold text-slate-700 mb-1.5">
                        Mật khẩu
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <input id="password" type="password" name="password" required autocomplete="current-password"
                            placeholder="Nhập mật khẩu"
                            class="block w-full pl-11 pr-11 py-3 bg-slate-50/70 border border-slate-200 rounded-2xl text-slate-800 placeholder-slate-400 font-medium text-sm sm:text-base focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-100 outline-none transition-all duration-200">
                        
                        <!-- Toggle Password Visibility -->
                        <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-blue-600 focus:outline-none transition-colors" aria-label="Hiện mật khẩu">
                            <svg id="eyeIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between pt-1">
                    <label for="remember_me" class="inline-flex items-center cursor-pointer select-none">
                        <input id="remember_me" type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }} class="w-4 h-4 rounded text-blue-600 border-slate-300 focus:ring-blue-500 cursor-pointer accent-blue-600">
                        <span class="ml-2 text-sm font-medium text-slate-600">Ghi nhớ đăng nhập</span>
                    </label>

                    <a href="#" class="text-sm font-bold text-blue-600 hover:text-blue-700 hover:underline transition-colors">
                        Quên mật khẩu?
                    </a>
                </div>

                <!-- Submit Button -->
                <div class="pt-3">
                    <button type="submit" class="w-full flex items-center justify-center gap-2 py-3.5 sm:py-4 px-6 rounded-2xl shadow-lg shadow-blue-600/25 hover:shadow-xl hover:shadow-blue-600/40 text-base font-bold text-white bg-blue-600 hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-500/25 transition-all duration-200 transform hover:-translate-y-0.5 active:translate-y-0 cursor-pointer">
                        <span>Đăng nhập</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </button>
                </div>

            </form>
        </div>
    </div>

    <!-- Script for Dynamic Role Card Selection, Remember Me & Password Toggle -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const roleCards = document.querySelectorAll('.role-card');
            const loginForm = document.getElementById('loginForm');
            const emailInput = document.getElementById('email');
            const rememberCheckbox = document.getElementById('remember_me');
            
            function syncRoleUI() {
                roleCards.forEach(card => {
                    const radio = card.querySelector('input[type="radio"]');
                    if (radio && radio.checked) {
                        card.classList.add('bg-blue-600', 'text-white', 'border-blue-600', 'shadow-lg', 'shadow-blue-500/30', 'scale-[1.02]');
                        card.classList.remove('bg-white', 'text-slate-600', 'border-slate-200');
                    } else if (radio) {
                        card.classList.remove('bg-blue-600', 'text-white', 'border-blue-600', 'shadow-lg', 'shadow-blue-500/30', 'scale-[1.02]');
                        card.classList.add('bg-white', 'text-slate-600', 'border-slate-200');
                    }
                });
            }

            roleCards.forEach(card => {
                card.addEventListener('click', (e) => {
                    const radio = card.querySelector('input[type="radio"]');
                    if (radio) {
                        radio.checked = true;
                        syncRoleUI();
                    }
                });
            });

            // Load remembered credentials from localStorage if available
            try {
                const isRemembered = localStorage.getItem('eye_exam_remember') === '1';
                if (isRemembered && rememberCheckbox) {
                    rememberCheckbox.checked = true;

                    if (emailInput && !emailInput.value) {
                        const savedEmail = localStorage.getItem('eye_exam_email');
                        if (savedEmail) emailInput.value = savedEmail;
                    }

                    @if(!old('role'))
                        const savedRole = localStorage.getItem('eye_exam_role');
                        if (savedRole) {
                            const targetRadio = document.querySelector(`input[name="role"][value="${savedRole}"]`);
                            if (targetRadio) {
                                targetRadio.checked = true;
                            }
                        }
                    @endif
                }
            } catch (err) {
                console.warn('Cannot access localStorage for remember login', err);
            }

            // Initial sync
            syncRoleUI();

            // Save or clear remembered credentials on submit
            if (loginForm) {
                loginForm.addEventListener('submit', () => {
                    try {
                        if (rememberCheckbox && rememberCheckbox.checked) {
                            localStorage.setItem('eye_exam_remember', '1');
                            if (emailInput) localStorage.setItem('eye_exam_email', emailInput.value.trim());
                            const checkedRadio = document.querySelector('input[name="role"]:checked');
                            if (checkedRadio) localStorage.setItem('eye_exam_role', checkedRadio.value);
                        } else {
                            localStorage.removeItem('eye_exam_remember');
                            localStorage.removeItem('eye_exam_email');
                            localStorage.removeItem('eye_exam_role');
                        }
                    } catch (err) {
                        console.warn('Cannot write localStorage for remember login', err);
                    }
                });
            }

            // Toggle Password Visibility
            const togglePasswordBtn = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');

            if (togglePasswordBtn && passwordInput) {
                togglePasswordBtn.addEventListener('click', () => {
                    const isPassword = passwordInput.type === 'password';
                    passwordInput.type = isPassword ? 'text' : 'password';
                    
                    if (isPassword) {
                        // Eye slash icon
                        eyeIcon.innerHTML = `
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                        `;
                    } else {
                        // Regular eye icon
                        eyeIcon.innerHTML = `
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        `;
                    }
                });
            }
        });
    </script>
</body>
</html>
