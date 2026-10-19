@extends('layouts.admin')

@section('title', 'Chỉnh sửa Lớp học phần')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Chỉnh sửa Lớp học phần</h1>
            <p class="text-sm text-slate-500 mt-0.5">Cập nhật: {{ $class->name }} ({{ $class->code }})</p>
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

    <form action="{{ route('admin.classes.update', $class) }}" method="POST" class="bg-white rounded-lg border border-slate-200 p-6 space-y-5">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Mã Lớp học phần <span class="text-rose-500">*</span></label>
                <input type="text" name="code" value="{{ old('code', $class->code) }}" required 
                    class="w-full h-10 px-3 border border-slate-200 rounded-md text-sm uppercase font-mono focus:border-blue-600 focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Tên Lớp học phần <span class="text-rose-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $class->name) }}" required 
                    class="w-full h-10 px-3 border border-slate-200 rounded-md text-sm focus:border-blue-600 focus:outline-none">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Học kỳ đào tạo <span class="text-rose-500">*</span></label>
                <select name="semester_id" required class="w-full h-10 px-3 border border-slate-200 rounded-md text-sm bg-white focus:border-blue-600 focus:outline-none">
                    <option value="">-- Chọn Học kỳ --</option>
                    @foreach($semesters as $sem)
                        <option value="{{ $sem->id }}" {{ old('semester_id', $class->semester_id) == $sem->id ? 'selected' : '' }}>
                            {{ $sem->name }} ({{ $sem->academic_year }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Môn học (Học phần) <span class="text-rose-500">*</span></label>
                <select name="subject_id" id="subject_id" required class="w-full h-10 px-3 border border-slate-200 rounded-md text-sm bg-white focus:border-blue-600 focus:outline-none" onchange="loadTeachersForSubject(this.value)">
                    <option value="">-- Chọn Môn học --</option>
                    @foreach($subjects as $sub)
                        <option value="{{ $sub->id }}" {{ old('subject_id', $class->subject_id) == $sub->id ? 'selected' : '' }}>
                            {{ $sub->code }} - {{ $sub->name }} ({{ $sub->credits }} TC)
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">Giảng viên đứng lớp <span class="text-rose-500">*</span></label>
            <p class="text-xs text-slate-500 mb-1.5">Chỉ hiển thị các giảng viên đủ điều kiện giảng dạy môn học này.</p>
            <select name="teacher_id" id="teacher_id" required class="w-full h-10 px-3 border border-slate-200 rounded-md text-sm bg-white focus:border-blue-600 focus:outline-none">
                <option value="">-- Chọn Giảng viên đứng lớp --</option>
                @foreach($qualifiedTeachers as $t)
                    <option value="{{ $t->id }}" {{ old('teacher_id', $class->teacher_id) == $t->id ? 'selected' : '' }}>
                        {{ $t->name }} ({{ $t->code ?? $t->email }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Sĩ số tối đa <span class="text-rose-500">*</span></label>
                <input type="number" name="max_students" value="{{ old('max_students', $class->max_students) }}" min="1" max="300" required class="w-full h-10 px-3 border border-slate-200 rounded-md text-sm focus:border-blue-600 focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Trạng thái lớp <span class="text-rose-500">*</span></label>
                <select name="status" class="w-full h-10 px-3 border border-slate-200 rounded-md text-sm bg-white focus:border-blue-600 focus:outline-none">
                    <option value="open" {{ old('status', $class->status) === 'open' ? 'selected' : '' }}>Đang mở đăng ký</option>
                    <option value="ongoing" {{ old('status', $class->status) === 'ongoing' ? 'selected' : '' }}>Đang diễn ra</option>
                    <option value="closed" {{ old('status', $class->status) === 'closed' ? 'selected' : '' }}>Đã kết thúc / Đóng</option>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">Ghi chú / Mô tả</label>
            <textarea name="description" rows="3" class="w-full p-3 border border-slate-200 rounded-md text-sm focus:border-blue-600 focus:outline-none">{{ old('description', $class->description) }}</textarea>
        </div>

        <div class="pt-4 flex justify-end gap-3 border-t border-slate-100">
            <a href="{{ route('admin.classes.index') }}" class="px-4 py-2 border border-slate-200 text-slate-700 text-sm rounded-md hover:bg-slate-50">Hủy</a>
            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md shadow-xs">Cập nhật Lớp học phần</button>
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
</script>
@endsection
