@extends('layouts.student')

@section('title', 'Thông tin sinh viên')

@section('content')
<div class="max-w-4xl mx-auto space-y-8 pb-12">
    <!-- Profile Header -->
    <div class="bg-white rounded-3xl p-8 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-gray-100 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 rounded-full bg-gradient-to-br from-indigo-50 to-blue-50 opacity-50 blur-3xl pointer-events-none"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row items-center gap-8">
            <div class="flex flex-col items-center gap-1.5 shrink-0">
                <div class="w-32 h-32 rounded-full bg-gradient-to-br from-indigo-100 to-blue-100 flex items-center justify-center text-indigo-700 font-bold text-5xl shadow-inner border-4 border-white shadow-lg overflow-hidden">
                    @if($user->avatar_url)
                        <img src="{{ $user->avatar_url }}" class="w-full h-full object-cover" alt="{{ $user->name }}">
                    @else
                        {{ substr($user->name, 0, 1) }}
                    @endif
                </div>
                <span class="text-[11px] font-medium text-slate-400 text-center">
                    Ảnh đại diện (Do Quản trị viên cập nhật)
                </span>
            </div>
            <div class="text-center md:text-left">
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight mb-1">{{ $user->name }}</h1>
                <p class="text-base text-gray-500 mb-3">{{ $user->email }}</p>
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-indigo-100 text-indigo-700">
                        Sinh viên
                    </span>
                    @if($user->face_registered)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200" title="Khuôn mặt đã được mã hóa AES-256">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            Face ID Đã mã hóa (AES-256)
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                            Chưa đăng ký Face ID
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Personal Information Card -->
    <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm relative overflow-hidden">
        <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2 border-b border-gray-100 pb-4">
            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
            Chi tiết cá nhân
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
            <!-- Personal Info -->
            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Mã sinh viên</p>
                <p class="text-lg font-semibold text-gray-900">{{ $user->code ?? 'Chưa cập nhật' }}</p>
            </div>
            
            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Họ và tên</p>
                <p class="text-lg font-semibold text-gray-900">{{ $user->name }}</p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Giới tính</p>
                <p class="text-lg font-semibold text-gray-900">{{ $user->gender ?? 'Chưa cập nhật' }}</p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Ngày tham gia</p>
                <p class="text-lg font-semibold text-gray-900">{{ $user->created_at ? $user->created_at->format('d/m/Y') : 'Chưa rõ' }}</p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Email</p>
                <p class="text-lg font-semibold text-gray-900">{{ $user->email }}</p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Số điện thoại</p>
                <p class="text-lg font-semibold text-gray-900">{{ $user->phone ?? 'Chưa cập nhật' }}</p>
            </div>

            <!-- Education Info -->
            <div class="md:col-span-2 pt-4 mt-2 border-t border-gray-100">
                <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4">Thông tin đào tạo</h3>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Lớp</p>
                <p class="text-lg font-semibold text-gray-900">{{ $user->class_name ?? 'Chưa cập nhật' }}</p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Khoa / Ban</p>
                <p class="text-lg font-semibold text-gray-900">{{ $user->department ?? 'Chưa cập nhật' }}</p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Chuyên ngành</p>
                <p class="text-lg font-semibold text-gray-900">{{ $user->major ?? 'Chưa cập nhật' }}</p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Bậc đào tạo</p>
                <p class="text-lg font-semibold text-gray-900">{{ $user->education_level ?? 'Chưa cập nhật' }}</p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Trạng thái</p>
                @if(($user->status ?? 'active') === 'active')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        Đang hoạt động
                    </span>
                @elseif(($user->status ?? '') === 'inactive')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        Tạm khóa
                    </span>
                @elseif(($user->status ?? '') === 'locked')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800 border border-rose-200">
                        <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                        Đã khóa
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-800">
                        {{ $user->status }}
                    </span>
                @endif
            </div>

            <!-- Address Info -->
            <div class="md:col-span-2 pt-4 mt-2 border-t border-gray-100">
                <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4">Thông tin địa chỉ</h3>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Nơi sinh</p>
                <p class="text-lg font-semibold text-gray-900">{{ $user->birth_place ?? 'Chưa cập nhật' }}</p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Địa chỉ thường trú</p>
                <p class="text-lg font-semibold text-gray-900">{{ $user->permanent_address ?? 'Chưa cập nhật' }}</p>
            </div>

            <div class="md:col-span-2">
                <p class="text-sm font-medium text-gray-500 mb-1">Địa chỉ liên lạc</p>
                <p class="text-lg font-semibold text-gray-900">{{ $user->contact_address ?? 'Chưa cập nhật' }}</p>
            </div>
        </div>
    </div>

    <!-- Biometric Face ID Profile Card -->
    <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm relative overflow-hidden">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gray-100 pb-4 mb-6">
            <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                Hồ sơ Sinh trắc học Face ID (Dùng cho Giám sát thi)
            </h2>
            <span class="text-xs text-slate-500 bg-slate-50 border border-slate-200 px-3 py-1 rounded-full font-medium">
                Độc lập với Ảnh đại diện
            </span>
        </div>

        <p class="text-xs text-slate-600 mb-6 leading-relaxed">
            Ảnh nhận diện khuôn mặt bên dưới được hệ thống AI sử dụng để đối sánh sinh trắc học khi vào phòng thi và giám sát chống gian lận trong suốt quá trình làm bài. Dữ liệu này được mã hóa bảo mật chuẩn <strong>AES-256-CBC</strong> và <strong>không phải là ảnh đại diện (avatar)</strong> công khai của bạn.
        </p>

        @if($user->face_registered)
            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6 bg-slate-50/80 rounded-2xl p-5 border border-slate-200/80">
                <div class="flex flex-col items-center gap-2 shrink-0">
                    <div class="w-28 h-28 rounded-2xl bg-white border-2 border-emerald-500/40 p-1 shadow-sm overflow-hidden relative">
                        @if($user->frontal_face_url)
                            <img src="{{ $user->frontal_face_url }}" class="w-full h-full object-cover rounded-xl" alt="Face ID">
                        @else
                            <div class="w-full h-full rounded-xl bg-slate-100 flex items-center justify-center text-slate-400">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </div>
                        @endif
                        <span class="absolute bottom-1 right-1 bg-emerald-600 text-white rounded-full p-1 shadow">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </span>
                    </div>
                    <span class="text-[11px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2.5 py-0.5 rounded-full">
                        Ảnh quét chính diện
                    </span>
                </div>

                <div class="flex-1 space-y-3 text-center sm:text-left">
                    <div>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            Đã đăng ký Face ID thành công
                        </span>
                        <p class="text-xs text-slate-500 mt-1.5">
                            Thời gian đăng ký: <strong class="text-slate-800">{{ $user->face_registered_at ? $user->face_registered_at->format('H:i - d/m/Y') : 'Đã ghi nhận' }}</strong>
                        </p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                        <div class="bg-white p-2.5 rounded-xl border border-slate-200/80">
                            <span class="text-slate-400 block text-[11px]">Thuật toán đối sánh</span>
                            <span class="font-semibold text-slate-800">InsightFace ArcFace 512D</span>
                        </div>
                        <div class="bg-white p-2.5 rounded-xl border border-slate-200/80">
                            <span class="text-slate-400 block text-[11px]">Bảo mật dữ liệu</span>
                            <span class="font-semibold text-emerald-700">AES-256 Private Storage</span>
                        </div>
                    </div>

                    <div class="pt-2">
                        <a href="{{ route('student.face.register') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-blue-600 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 px-3.5 py-2 rounded-xl border border-blue-200 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            Quét lại khuôn mặt (Cập nhật góc mặt)
                        </a>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-amber-900">Chưa hoàn tất hồ sơ nhận diện khuôn mặt</p>
                        <p class="text-xs text-amber-700 mt-0.5">Bạn cần hoàn tất đăng ký 3 góc khuôn mặt để hệ thống AI xác minh khi bạn làm bài thi.</p>
                    </div>
                </div>
                <a href="{{ route('student.face.register') }}" class="shrink-0 inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-sm transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path></svg>
                    Quét khuôn mặt ngay
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
