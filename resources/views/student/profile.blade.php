@extends('layouts.student')

@section('title', 'Thông tin sinh viên')

@section('content')
<div class="max-w-4xl mx-auto space-y-8 pb-12">
    <!-- Profile Header -->
    <div class="bg-white rounded-3xl p-8 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-gray-100 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 rounded-full bg-gradient-to-br from-indigo-50 to-blue-50 opacity-50 blur-3xl pointer-events-none"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row items-center gap-8">
            <div class="w-32 h-32 rounded-full bg-gradient-to-br from-indigo-100 to-blue-100 flex items-center justify-center text-indigo-700 font-bold text-5xl shadow-inner shrink-0 border-4 border-white shadow-lg overflow-hidden">
                @if($user->avatar_url)
                    <img src="{{ $user->avatar_url }}" class="w-full h-full object-cover" alt="{{ $user->name }}">
                @else
                    {{ substr($user->name, 0, 1) }}
                @endif
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
</div>
@endsection
