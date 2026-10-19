@extends('layouts.admin')

@section('title', 'Chỉnh sửa Ngành')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Chỉnh sửa Ngành</h1>
            <p class="text-sm text-slate-500 mt-0.5">Cập nhật thông tin: {{ $department->name }}</p>
        </div>
        <a href="{{ route('admin.departments.index') }}" class="text-sm text-slate-600 hover:text-slate-900">Quay lại</a>
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

    <form action="{{ route('admin.departments.update', $department) }}" method="POST" class="bg-white rounded-lg border border-slate-200 p-6 space-y-4">
        @csrf
        @method('PUT')
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">Thuộc Khoa <span class="text-rose-500">*</span></label>
            <select name="faculty_id" required class="w-full h-10 px-3 border border-slate-200 rounded-md text-sm bg-white focus:border-blue-600 focus:outline-none">
                @foreach($faculties as $f)
                    <option value="{{ $f->id }}" {{ old('faculty_id', $department->faculty_id) == $f->id ? 'selected' : '' }}>
                        {{ $f->name }} ({{ $f->code }})
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">Mã Ngành <span class="text-rose-500">*</span></label>
            <input type="text" name="code" value="{{ old('code', $department->code) }}" required 
                class="w-full h-10 px-3 border border-slate-200 rounded-md text-sm uppercase font-mono focus:border-blue-600 focus:outline-none">
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">Tên Ngành <span class="text-rose-500">*</span></label>
            <input type="text" name="name" value="{{ old('name', $department->name) }}" required 
                class="w-full h-10 px-3 border border-slate-200 rounded-md text-sm focus:border-blue-600 focus:outline-none">
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">Trạng thái <span class="text-rose-500">*</span></label>
            <select name="status" class="w-full h-10 px-3 border border-slate-200 rounded-md text-sm bg-white focus:border-blue-600 focus:outline-none">
                <option value="active" {{ old('status', $department->status) === 'active' ? 'selected' : '' }}>Đang hoạt động</option>
                <option value="inactive" {{ old('status', $department->status) === 'inactive' ? 'selected' : '' }}>Ngưng hoạt động</option>
            </select>
        </div>

        <div class="pt-4 flex justify-end gap-3 border-t border-slate-100">
            <a href="{{ route('admin.departments.index') }}" class="px-4 py-2 border border-slate-200 text-slate-700 text-sm rounded-md hover:bg-slate-50">Hủy</a>
            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md shadow-xs">Cập nhật</button>
        </div>
    </form>
</div>
@endsection
