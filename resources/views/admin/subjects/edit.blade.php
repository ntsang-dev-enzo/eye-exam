@extends('layouts.admin')

@section('title', 'Cập nhật Môn Học')

@section('content')
    <div class="max-w-2xl mx-auto">
        <form action="{{ route('admin.subjects.update', $subject) }}" method="POST" class="bg-white rounded-2xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-gray-100 p-8 space-y-6">
            @csrf
            @method('PUT')

            @if($errors->any())
                <div class="bg-rose-50 text-rose-700 p-4 rounded-xl text-sm border border-rose-100">
                    <ul class="list-disc pl-5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Code -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Mã môn học <span class="text-rose-500">*</span></label>
                    <input type="text" name="code" value="{{ old('code', $subject->code) }}" required class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 px-4 py-2.5 bg-slate-50 text-sm uppercase">
                </div>
                
                <!-- Name -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Tên môn học <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $subject->name) }}" required class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 px-4 py-2.5 bg-slate-50 text-sm">
                </div>
            </div>

            <!-- Description -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Mô tả (Tùy chọn)</label>
                <textarea name="description" rows="3" class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 px-4 py-3 bg-slate-50 text-sm">{{ old('description', $subject->description) }}</textarea>
            </div>

            <!-- Teachers Assignment -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-sm font-medium text-slate-700">Phân công Giảng viên (Tùy chọn)</label>
                    <input type="text" id="teacher-search" placeholder="Tìm giảng viên, khoa..." class="text-sm rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 py-1.5 px-3 w-48">
                </div>
                <div class="bg-slate-50 border border-slate-300 rounded-xl max-h-64 overflow-y-auto p-3" id="teachers-list">
                    @php $assignedTeacherIds = old('teachers', $subject->teachers->pluck('id')->toArray()); @endphp
                    @forelse($departments as $department => $teachers)
                        <div class="department-group mb-4 last:mb-0">
                            <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 pb-1 border-b border-slate-200">
                                {{ $department ?: 'Chưa phân khoa' }}
                            </h4>
                            <div class="space-y-1">
                                @foreach($teachers as $teacher)
                                    <label class="teacher-item flex items-center gap-3 p-2 hover:bg-slate-100 rounded-lg cursor-pointer" data-search="{{ strtolower($teacher->name . ' ' . $teacher->email . ' ' . $department) }}">
                                        <input type="checkbox" name="teachers[]" value="{{ $teacher->id }}" class="w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500 cursor-pointer" {{ in_array($teacher->id, $assignedTeacherIds) ? 'checked' : '' }}>
                                        <div>
                                            <p class="text-sm font-medium text-slate-800">{{ $teacher->name }}</p>
                                            <p class="text-xs text-slate-500">{{ $teacher->email }}</p>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500 p-2 text-center">Chưa có tài khoản giảng viên nào trong hệ thống.</p>
                    @endforelse
                </div>
                <p class="text-xs text-slate-500 mt-2">* Các giảng viên được tick chọn sẽ có quyền quản lý ngân hàng câu hỏi và tạo đề thi cho môn học này.</p>
            </div>

            <!-- Status -->
            <div class="flex items-center gap-3 pt-2">
                <input type="checkbox" name="status" id="status" value="1" class="w-5 h-5 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500 cursor-pointer" {{ old('status', $subject->status) ? 'checked' : '' }}>
                <label for="status" class="text-sm font-medium text-slate-700 cursor-pointer">Kích hoạt (Cho phép sử dụng môn học này)</label>
            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-end gap-3">
                <a href="{{ route('admin.subjects.index') }}" class="px-5 py-2.5 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-xl hover:bg-slate-50 transition-colors">
                    Hủy bỏ
                </a>
                <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 transition-colors shadow-sm">
                    Cập nhật môn học
                </button>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('teacher-search')?.addEventListener('input', function(e) {
            const term = e.target.value.toLowerCase();
            const groups = document.querySelectorAll('.department-group');
            
            groups.forEach(group => {
                const items = group.querySelectorAll('.teacher-item');
                let hasVisibleItem = false;
                
                items.forEach(item => {
                    const searchData = item.getAttribute('data-search');
                    if (searchData.includes(term)) {
                        item.style.display = 'flex';
                        hasVisibleItem = true;
                    } else {
                        item.style.display = 'none';
                    }
                });
                
                group.style.display = hasVisibleItem ? 'block' : 'none';
            });
        });
    </script>
@endsection
