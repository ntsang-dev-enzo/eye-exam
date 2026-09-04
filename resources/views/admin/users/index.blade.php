@extends('layouts.admin')

@section('title', 'Quản lý tài khoản')

@section('content')
<div class="space-y-5">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Quản lý tài khoản</h1>
            <p class="text-xs text-slate-500 mt-0.5">Danh sách tài khoản giảng viên và sinh viên trong toàn hệ thống</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                Tổng cộng: <strong class="ml-1 text-slate-900">{{ $users->total() }}</strong>
            </span>
        </div>
    </div>

    <!-- Filter Toolbar -->
    <div class="bg-white border border-slate-200 rounded-lg p-3.5">
        <form action="{{ route('admin.users.index') }}" method="GET" class="flex flex-wrap items-center gap-2.5">
            <!-- Search Input -->
            <div class="relative min-w-[240px] flex-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}" 
                       placeholder="Tìm theo tên, email, mã số..." 
                       class="w-full h-9 pl-9 pr-3 text-sm bg-white border border-slate-200 rounded-md placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-colors">
            </div>

            <!-- Role Filter -->
            <select name="role" class="h-9 px-3 text-sm bg-white border border-slate-200 rounded-md text-slate-700 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-colors">
                <option value="">Tất cả vai trò</option>
                <option value="teacher" {{ request('role') === 'teacher' ? 'selected' : '' }}>Giảng viên</option>
                <option value="student" {{ request('role') === 'student' ? 'selected' : '' }}>Sinh viên</option>
            </select>

            <!-- Status Filter -->
            <select name="status" class="h-9 px-3 text-sm bg-white border border-slate-200 rounded-md text-slate-700 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-colors">
                <option value="">Tất cả trạng thái</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Hoạt động</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Tạm khóa</option>
                <option value="locked" {{ request('status') === 'locked' ? 'selected' : '' }}>Đã khóa</option>
            </select>

            <!-- Action Buttons -->
            <button type="submit" class="h-9 px-3.5 inline-flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-md transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                Lọc
            </button>

            @if(request()->hasAny(['search', 'role', 'status']) && (request('search') || request('role') || request('status')))
                <a href="{{ route('admin.users.index') }}" class="h-9 px-3 inline-flex items-center text-xs font-medium text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 rounded-md transition-colors">
                    Đặt lại
                </a>
            @endif
        </form>
    </div>

    <!-- Success Alert -->
    @if(session('success'))
        <div class="p-3.5 rounded-md bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

    <!-- Users Table -->
    <div class="bg-white border border-slate-200 rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/75 border-b border-slate-200 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                        <th class="py-3 px-4">Mã số</th>
                        <th class="py-3 px-4">Họ và tên / Email</th>
                        <th class="py-3 px-4">Vai trò</th>
                        <th class="py-3 px-4">Khoa / Phòng ban</th>
                        <th class="py-3 px-4">Face ID (AI)</th>
                        <th class="py-3 px-4">Trạng thái</th>
                        <th class="py-3 px-4">Ngày tạo</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-50/75 transition-colors">
                            <!-- Code -->
                            <td class="py-3 px-4 font-mono text-xs text-slate-700">
                                @if($user->code)
                                    <span class="inline-block px-2 py-0.5 bg-slate-100 border border-slate-200 rounded text-slate-800 font-semibold">
                                        {{ $user->code }}
                                    </span>
                                @else
                                    <span class="text-slate-400">-</span>
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
                            <td class="py-3 px-4">
                                @if($user->role === 'teacher')
                                    <span class="text-xs font-medium text-slate-700">Giảng viên</span>
                                @elseif($user->role === 'student')
                                    <span class="text-xs font-medium text-slate-700">Sinh viên</span>
                                @else
                                    <span class="text-xs font-medium text-slate-500">{{ $user->role }}</span>
                                @endif
                            </td>

                            <!-- Department -->
                            <td class="py-3 px-4 text-slate-600 text-xs">
                                {{ $user->department ?? '-' }}
                            </td>

                            <!-- Face ID Status & Reset -->
                            <td class="py-3 px-4">
                                @if($user->role === 'student')
                                    @if($user->face_registered)
                                        <div class="flex items-center gap-2.5">
                                            @if($user->frontal_face_url)
                                                <img src="{{ $user->frontal_face_url }}" class="w-7 h-7 rounded object-cover border border-slate-200 shrink-0" alt="Face">
                                            @endif
                                            <div class="space-y-1">
                                                <span class="inline-flex items-center gap-1 text-[11px] font-medium text-emerald-600">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                    Đã đăng ký
                                                </span>
                                                <form action="{{ route('admin.users.reset-face', $user->id) }}" method="POST" onsubmit="return confirm('Đặt lại Face ID cho sinh viên {{ addslashes($user->name) }}? Sinh viên sẽ được phép quét lại khuôn mặt mới.')">
                                                    @csrf
                                                    <button type="submit" class="inline-flex items-center gap-1 text-[11px] text-slate-500 hover:text-rose-600 font-medium transition-colors" title="Đặt lại khuôn mặt nếu sinh viên bị lỗi">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                                        Đặt lại
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-xs font-normal text-slate-400">Chưa đăng ký</span>
                                    @endif
                                @else
                                    <span class="text-slate-300 text-xs">-</span>
                                @endif
                            </td>

                            <!-- Status -->
                            <td class="py-3 px-4">
                                @if($user->status === 'active')
                                    <span class="inline-flex items-center gap-1.5 text-xs font-medium text-emerald-600">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                                        Hoạt động
                                    </span>
                                @elseif($user->status === 'inactive')
                                    <span class="inline-flex items-center gap-1.5 text-xs font-medium text-amber-600">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 shrink-0"></span>
                                        Tạm khóa
                                    </span>
                                @elseif($user->status === 'locked')
                                    <span class="inline-flex items-center gap-1.5 text-xs font-medium text-rose-600">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500 shrink-0"></span>
                                        Đã khóa
                                    </span>
                                @else
                                    <span class="text-xs font-medium text-slate-500">{{ $user->status }}</span>
                                @endif
                            </td>

                            <!-- Created At -->
                            <td class="py-3 px-4 text-slate-500 text-xs font-mono">
                                {{ $user->created_at ? $user->created_at->format('d/m/Y H:i') : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 px-4 text-center">
                                <div class="max-w-xs mx-auto text-center">
                                    <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    </div>
                                    <div class="text-sm font-medium text-slate-900">Không tìm thấy tài khoản</div>
                                    <p class="text-xs text-slate-500 mt-1">Không có tài khoản nào khớp với điều kiện lọc hiện tại.</p>
                                    @if(request()->hasAny(['search', 'role', 'status']))
                                        <a href="{{ route('admin.users.index') }}" class="inline-block mt-3 text-xs font-medium text-blue-600 hover:text-blue-700">
                                            Xóa toàn bộ bộ lọc
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
            <div class="px-4 py-3 border-t border-slate-200 bg-slate-50/50">
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
