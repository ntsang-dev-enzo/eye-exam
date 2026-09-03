@extends('layouts.admin')

@section('title', 'Quản lý Tài khoản')

@section('content')
<div class="space-y-6">
    <!-- Main Card -->
    <div class="bg-white rounded-2xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-gray-100 overflow-hidden">
        <!-- Header & Filters -->
        <div class="p-6 border-b border-gray-100 bg-white">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <!-- Left Title & Description -->
                <div>
                    <h3 class="text-xl font-bold text-slate-800 tracking-tight">Quản lý Tài khoản</h3>
                    <p class="text-xs text-slate-500 mt-1">Danh sách tài khoản Giảng viên và Sinh viên trong hệ thống</p>
                </div>

                <!-- Right Search & Filters -->
                <form action="{{ route('admin.users.index') }}" method="GET" class="flex flex-wrap items-center gap-3">
                    <!-- Search Input -->
                    <div class="relative min-w-[240px] flex-1 sm:flex-initial">
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}" 
                               placeholder="Tìm tên, email, mã số..." 
                               class="w-full pl-10 pr-4 py-2 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 shadow-sm transition-all">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>

                    <!-- Role Filter -->
                    <select name="role" class="py-2 px-3 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 shadow-sm bg-white text-slate-700">
                        <option value="">Tất cả vai trò</option>
                        <option value="teacher" {{ request('role') === 'teacher' ? 'selected' : '' }}>Giảng viên</option>
                        <option value="student" {{ request('role') === 'student' ? 'selected' : '' }}>Sinh viên</option>
                    </select>

                    <!-- Status Filter -->
                    <select name="status" class="py-2 px-3 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 shadow-sm bg-white text-slate-700">
                        <option value="">Tất cả trạng thái</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Hoạt động</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Tạm khóa</option>
                        <option value="locked" {{ request('status') === 'locked' ? 'selected' : '' }}>Đã khóa</option>
                    </select>

                    <!-- Action Buttons -->
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-xl shadow-sm shadow-indigo-500/20 transition-all">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                        Lọc
                    </button>

                    @if(request()->hasAny(['search', 'role', 'status']) && (request('search') || request('role') || request('status')))
                        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-medium rounded-xl transition-all">
                            Xóa lọc
                        </a>
                    @endif
                </form>
            </div>
        </div>

        <!-- Notification Alerts -->
        @if(session('success'))
            <div class="m-6 p-4 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200/60 flex items-center gap-3">
                <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/70 text-xs font-bold text-slate-500 uppercase tracking-wider border-b border-gray-100">
                        <th class="px-6 py-4">Mã số</th>
                        <th class="px-6 py-4">Họ và Tên / Email</th>
                        <th class="px-6 py-4">Vai trò</th>
                        <th class="px-6 py-4">Khoa / Phòng ban</th>
                        <th class="px-6 py-4">Face ID (AI)</th>
                        <th class="px-6 py-4">Trạng thái</th>
                        <th class="px-6 py-4">Ngày tạo</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <!-- Code -->
                            <td class="px-6 py-4 font-semibold text-slate-700">
                                @if($user->code)
                                    <span class="inline-block px-2.5 py-1 rounded-md bg-slate-100 text-slate-700 font-mono text-xs border border-slate-200">
                                        {{ $user->code }}
                                    </span>
                                @else
                                    <span class="text-slate-400 font-normal italic">-</span>
                                @endif
                            </td>

                            <!-- User Info & Avatar -->
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="relative group shrink-0">
                                        @if($user->avatar_url)
                                            <img src="{{ $user->avatar_url }}" class="w-10 h-10 rounded-full object-cover border-2 border-indigo-200 shadow-sm" alt="Avatar">
                                        @else
                                            <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-indigo-500 to-purple-600 text-white font-bold text-xs flex items-center justify-center shadow-sm">
                                                {{ mb_substr($user->name, 0, 1, 'UTF-8') }}
                                            </div>
                                        @endif
                                        <button type="button" 
                                                onclick="openAvatarModal({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ $user->avatar_url ?? '' }}')" 
                                                class="absolute inset-0 bg-slate-900/60 rounded-full opacity-0 group-hover:opacity-100 flex items-center justify-center text-white transition-opacity" 
                                                title="Cập nhật ảnh đại diện">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        </button>
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900 flex items-center gap-2">
                                            <span>{{ $user->name }}</span>
                                            <button type="button" 
                                                    onclick="openAvatarModal({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ $user->avatar_url ?? '' }}')" 
                                                    class="text-[11px] text-indigo-600 hover:text-indigo-800 font-semibold hover:underline" 
                                                    title="Đổi ảnh đại diện">
                                                [Đổi Avatar]
                                            </button>
                                        </div>
                                        <div class="text-xs text-slate-500">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>

                            <!-- Role -->
                            <td class="px-6 py-4">
                                @if($user->role === 'teacher')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-purple-50 text-purple-700 border border-purple-200/60">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                                        Giảng viên
                                    </span>
                                @elseif($user->role === 'student')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200/60">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path></svg>
                                        Sinh viên
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ $user->role }}
                                    </span>
                                @endif
                            </td>

                            <!-- Department -->
                            <td class="px-6 py-4 text-slate-600">
                                {{ $user->department ?? '-' }}
                            </td>

                            <!-- Face ID Status & Admin Reset Action -->
                            <td class="px-6 py-4">
                                @if($user->role === 'student')
                                    @if($user->face_registered)
                                        <div class="flex items-center gap-2.5">
                                            @if($user->frontal_face_url)
                                                <img src="{{ $user->frontal_face_url }}" class="w-8 h-8 rounded-full object-cover border-2 border-emerald-300 shadow-xs shrink-0" alt="Face">
                                            @endif
                                            <div>
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                                                    <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                    Đã kích hoạt
                                                </span>
                                                <form action="{{ route('admin.users.reset-face', $user->id) }}" method="POST" onsubmit="return confirm('Đặt lại Face ID cho sinh viên {{ addslashes($user->name) }}? Sinh viên sẽ được phép quét lại khuôn mặt mới.')" class="mt-1">
                                                    @csrf
                                                    <button type="submit" class="text-[11px] text-rose-600 hover:text-rose-800 font-semibold hover:underline flex items-center gap-1" title="Đặt lại khuôn mặt nếu sinh viên bị lỗi">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                                        Đặt lại Face ID
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-slate-100 text-slate-500 border border-slate-200">
                                            Chưa đăng ký
                                        </span>
                                    @endif
                                @else
                                    <span class="text-slate-300 text-xs">-</span>
                                @endif
                            </td>

                            <!-- Status -->
                            <td class="px-6 py-4">
                                @if($user->status === 'active')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Hoạt động
                                    </span>
                                @elseif($user->status === 'inactive')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200/60">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        Tạm khóa
                                    </span>
                                @elseif($user->status === 'locked')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-rose-50 text-rose-700 border border-rose-200/60">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                        Đã khóa
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700">
                                        {{ $user->status }}
                                    </span>
                                @endif
                            </td>

                            <!-- Created At -->
                            <td class="px-6 py-4 text-slate-500 text-xs">
                                {{ $user->created_at ? $user->created_at->format('d/m/Y H:i') : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                                <div class="flex flex-col items-center justify-center gap-3">
                                    <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center text-slate-400">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                    </div>
                                    <p class="font-medium text-slate-600">Không tìm thấy tài khoản nào phù hợp.</p>
                                    @if(request()->hasAny(['search', 'role', 'status']))
                                        <a href="{{ route('admin.users.index') }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-semibold underline">
                                            Bấm vào đây để xóa bộ lọc
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-slate-50/50">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Cập nhật Avatar (Riêng biệt với Face ID) -->
<div id="avatarModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full p-6 space-y-6 border border-slate-100 transform transition-all">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div>
                <h3 class="text-lg font-black text-slate-900">Cập nhật Ảnh đại diện (Avatar)</h3>
                <p id="modalUserName" class="text-xs text-slate-500 font-medium mt-0.5"></p>
            </div>
            <button type="button" onclick="closeAvatarModal()" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-xl transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <form id="avatarUploadForm" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf
            <!-- Preview Box -->
            <div class="flex flex-col items-center justify-center gap-3 py-2">
                <div class="relative w-28 h-28 rounded-full overflow-hidden border-4 border-indigo-100 shadow-md bg-slate-100 flex items-center justify-center">
                    <img id="avatarPreviewImg" src="" class="w-full h-full object-cover hidden" alt="Preview">
                    <span id="avatarPlaceholder" class="text-slate-400 font-bold text-3xl">?</span>
                </div>
                <p class="text-xs text-slate-400 italic">Ảnh hiển thị riêng (Không dùng cho đối soát Face ID)</p>
            </div>

            <!-- File Input -->
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Chọn ảnh mới từ máy tính</label>
                <input type="file" 
                       id="avatarFileInput" 
                       name="avatar" 
                       accept="image/jpeg,image/png,image/webp" 
                       required
                       onchange="previewSelectedAvatar(this)"
                       class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer border border-slate-200 rounded-xl p-1.5">
                <p class="text-[11px] text-slate-400">Hỗ trợ JPG, PNG, WEBP (Tối đa 3MB).</p>
            </div>

            <div class="flex items-center justify-between pt-3 border-t border-slate-100 gap-3">
                <button type="button" 
                        id="btnDeleteAvatar" 
                        onclick="confirmDeleteAvatar()" 
                        class="px-3.5 py-2 text-xs font-bold text-rose-600 hover:bg-rose-50 rounded-xl transition-colors hidden">
                    Xóa Avatar
                </button>
                <div class="flex items-center gap-2 ml-auto">
                    <button type="button" onclick="closeAvatarModal()" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl transition-colors">
                        Hủy
                    </button>
                    <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-md shadow-indigo-200 transition-colors">
                        Lưu Thay Đổi
                    </button>
                </div>
            </div>
        </form>

        <!-- Hidden form for deleting avatar -->
        <form id="avatarDeleteForm" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    </div>
</div>

<script>
    let currentUserId = null;

    function openAvatarModal(userId, userName, currentAvatarUrl) {
        currentUserId = userId;
        document.getElementById('modalUserName').textContent = 'Tài khoản: ' + userName;
        
        const form = document.getElementById('avatarUploadForm');
        form.action = `/admin/users/${userId}/avatar`;

        const deleteForm = document.getElementById('avatarDeleteForm');
        deleteForm.action = `/admin/users/${userId}/avatar`;

        const previewImg = document.getElementById('avatarPreviewImg');
        const placeholder = document.getElementById('avatarPlaceholder');
        const btnDelete = document.getElementById('btnDeleteAvatar');
        const fileInput = document.getElementById('avatarFileInput');
        fileInput.value = '';

        if (currentAvatarUrl && currentAvatarUrl.trim() !== '') {
            previewImg.src = currentAvatarUrl;
            previewImg.classList.remove('hidden');
            placeholder.classList.add('hidden');
            btnDelete.classList.remove('hidden');
        } else {
            previewImg.src = '';
            previewImg.classList.add('hidden');
            placeholder.classList.remove('hidden');
            placeholder.textContent = userName.charAt(0).toUpperCase();
            btnDelete.classList.add('hidden');
        }

        document.getElementById('avatarModal').classList.remove('hidden');
    }

    function closeAvatarModal() {
        document.getElementById('avatarModal').classList.add('hidden');
    }

    function previewSelectedAvatar(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const previewImg = document.getElementById('avatarPreviewImg');
                const placeholder = document.getElementById('avatarPlaceholder');
                previewImg.src = e.target.result;
                previewImg.classList.remove('hidden');
                placeholder.classList.add('hidden');
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function confirmDeleteAvatar() {
        if (confirm('Bạn có chắc chắn muốn xóa avatar của người dùng này và dùng biểu tượng mặc định?')) {
            document.getElementById('avatarDeleteForm').submit();
        }
    }
</script>
@endsection
