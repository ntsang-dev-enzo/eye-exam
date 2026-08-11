<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đăng nhập hệ thống</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            background-color: #f3f4f6;
            background-image: radial-gradient(circle at 100% 100%, #eff6ff 0, #eff6ff 3px, transparent 3px), radial-gradient(circle at 0 0, #eff6ff 0, #eff6ff 3px, transparent 3px), radial-gradient(circle at 0 100%, #eff6ff 0, #eff6ff 3px, transparent 3px), radial-gradient(circle at 100% 0, #eff6ff 0, #eff6ff 3px, transparent 3px), radial-gradient(circle at 50% 50%, #eff6ff 0, #eff6ff 3px, transparent 3px);
            background-size: 60px 60px;
        }
    </style>
</head>
<body class="antialiased min-h-screen flex items-center justify-center font-sans">

    <div class="max-w-md w-full bg-white/80 backdrop-blur-md rounded-2xl shadow-xl overflow-hidden ring-1 ring-gray-900/5">
        <div class="p-8">
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-blue-100 text-blue-600 mb-4 shadow-inner">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                </div>
                <h2 class="text-3xl font-bold text-gray-800 tracking-tight">Đăng nhập</h2>
                <p class="text-gray-500 mt-2 text-sm">Hệ thống quản lý kỳ thi</p>
            </div>

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                @if ($errors->any())
                    <div class="bg-red-50 text-red-500 p-4 rounded-xl text-sm ring-1 ring-red-100 mb-6">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                        class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-4 py-3 bg-gray-50/50 border outline-none transition duration-200">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Mật khẩu</label>
                    <input id="password" type="password" name="password" required autocomplete="current-password"
                        class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-4 py-3 bg-gray-50/50 border outline-none transition duration-200">
                </div>

                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Vai trò</label>
                    <div class="relative">
                        <select id="role" name="role" required
                            class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-4 py-3 bg-gray-50/50 border outline-none transition duration-200 appearance-none text-gray-700">
                            <option value="student" {{ old('role') == 'student' ? 'selected' : '' }}>Sinh viên</option>
                            <option value="teacher" {{ old('role') == 'teacher' ? 'selected' : '' }}>Giảng viên</option>
                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Quản lý</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between mt-2">
                    <div class="flex items-center">
                        <input id="remember_me" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded cursor-pointer" name="remember">
                        <label for="remember_me" class="ml-2 block text-sm text-gray-600 cursor-pointer">
                            Ghi nhớ đăng nhập
                        </label>
                    </div>
                    
                    <div class="text-sm">
                        <a href="#" class="font-medium text-blue-600 hover:text-blue-500 hover:underline transition duration-150">
                            Quên mật khẩu?
                        </a>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-md text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200 ease-in-out transform hover:-translate-y-0.5">
                        Đăng nhập
                    </button>
                </div>
            </form>
        </div>
        
        <div class="px-8 py-5 bg-gray-50/80 border-t border-gray-100 text-center">
            <p class="text-xs text-gray-500">
                &copy; {{ date('Y') }} Eye Exam System. All rights reserved.
            </p>
        </div>
    </div>

</body>
</html>
