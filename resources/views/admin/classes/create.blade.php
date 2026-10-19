@extends('layouts.admin')

@section('title', 'Mở Lớp học phần mới')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Mở Lớp học phần mới</h1>
            <p class="text-sm text-slate-500 mt-0.5">Khởi tạo lớp môn học theo học kỳ và phân công giảng viên đủ điều kiện</p>
        </div>
        <a href="{{ route('admin.classes.index') }}" class="text-sm text-slate-600 hover:text-slate-900">Quay lại</a>
    </div>

    @if($errors->any())
        <div class="p-3.5 bg-rose-50 border border-rose-200 rounded-md text-rose-800 text-sm">
            <ul class="list-disc pl-5 space-y-1">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.classes.store') }}" method="POST" class="bg-white rounded-lg border border-slate-200 p-6 space-y-5">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Mã Lớp học phần <span class="text-rose-500">*</span></label>
                <input type="text" name="code" value="{{ old('code') }}" required placeholder="VD: LHP_CS101_01..." 
                    class="w-full h-10 px-3 border border-slate-200 rounded-md text-sm uppercase font-mono focus:border-blue-600 focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Tên Lớp học phần <span class="text-rose-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="VD: Lập trình Web - Nhóm 01..." 
                    class="w-full h-10 px-3 border border-slate-200 rounded-md text-sm focus:border-blue-600 focus:outline-none">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Học kỳ đào tạo <span class="text-rose-500">*</span></label>
                <select name="semester_id" required class="w-full h-10 px-3 border border-slate-200 rounded-md text-sm bg-white focus:border-blue-600 focus:outline-none">
                    <option value="">-- Chọn Học kỳ --</option>
                    @foreach($semesters as $sem)
                        <option value="{{ $sem->id }}" {{ (old('semester_id') == $sem->id || (!old('semester_id') && $sem->is_current)) ? 'selected' : '' }}>
                            {{ $sem->name }} ({{ $sem->academic_year }}) {{ $sem->is_current ? '★ Hiện tại' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Môn học (Học phần) <span class="text-rose-500">*</span></label>
                <select name="subject_id" id="subject_id" required class="w-full h-10 px-3 border border-slate-200 rounded-md text-sm bg-white focus:border-blue-600 focus:outline-none" onchange="loadTeachersForSubject(this.value)">
                    <option value="">-- Chọn Môn học --</option>
                    @foreach($subjects as $sub)
                        <option value="{{ $sub->id }}" {{ old('subject_id') == $sub->id ? 'selected' : '' }}>
                            {{ $sub->code }} - {{ $sub->name }} ({{ $sub->credits }} TC)
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Giảng viên đứng lớp: Load động theo môn học -->
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">
                Giảng viên đứng lớp <span class="text-rose-500">*</span>
            </label>
            <p class="text-xs text-slate-500 mb-1.5">Chỉ hiển thị các giảng viên đã được phân công/đủ điều kiện giảng dạy môn học được chọn.</p>
            <select name="teacher_id" id="teacher_id" required class="w-full h-10 px-3 border border-slate-200 rounded-md text-sm bg-white focus:border-blue-600 focus:outline-none">
                <option value="">-- Vui lòng chọn Môn học trước --</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">Sĩ số tối đa <span class="text-rose-500">*</span></label>
            <input type="number" name="max_students" value="{{ old('max_students', 60) }}" min="1" max="300" required 
                class="w-full h-10 px-3 border border-slate-200 rounded-md text-sm focus:border-blue-600 focus:outline-none">
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">Ghi chú / Mô tả</label>
            <textarea name="description" rows="3" placeholder="Lịch học, ghi chú thêm cho lớp học phần..." 
                class="w-full p-3 border border-slate-200 rounded-md text-sm focus:border-blue-600 focus:outline-none">{{ old('description') }}</textarea>
        </div>

        <div class="pt-4 flex justify-end gap-3 border-t border-slate-100">
            <a href="{{ route('admin.classes.index') }}" class="px-4 py-2 border border-slate-200 text-slate-700 text-sm rounded-md hover:bg-slate-50">Hủy</a>
            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md shadow-xs">Mở Lớp học phần</button>
        </div>
    </form>
</div>

<script>
function loadTeachersForSubject(subjectId, selectedTeacherId = null) {
    const teacherSelect = document.getElementById('teacher_id');
    if (!subjectId) {
        teacherSelect.innerHTML = '<option value="">-- Vui lòng chọn Môn học trước --</option>';
        return;
    }

    teacherSelect.innerHTML = '<option value="">Đang tải danh sách giảng viên...</option>';

    fetch(`/admin/api/mon-hoc/${subjectId}/giang-vien`)
        .then(res => res.json())
        .then(teachers => {
            if (teachers.length === 0) {
                teacherSelect.innerHTML = '<option value="">-- Môn học này chưa có giảng viên nào được phân công --</option>';
                return;
            }

            let html = '<option value="">-- Chọn Giảng viên đứng lớp --</option>';
            teachers.forEach(t => {
                const isSelected = selectedTeacherId == t.id ? 'selected' : '';
                html += `<option value="${t.id}" ${isSelected}>${t.name} (${t.code || t.email})</option>`;
            });
            teacherSelect.innerHTML = html;
        })
        .catch(err => {
            console.error(err);
            teacherSelect.innerHTML = '<option value="">Lỗi tải danh sách giảng viên</option>';
        });
}

document.addEventListener('DOMContentLoaded', () => {
    const initialSubjectId = document.getElementById('subject_id').value;
    const initialTeacherId = "{{ old('teacher_id') }}";
    if (initialSubjectId) {
        loadTeachersForSubject(initialSubjectId, initialTeacherId);
    }
});
</script>
@endsection
