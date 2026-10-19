@extends('layouts.admin')

@section('title', 'Chỉnh sửa Học kỳ')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Chỉnh sửa Học kỳ</h1>
            <p class="text-sm text-slate-500 mt-0.5">Cập nhật: {{ $semester->name }} ({{ $semester->academic_year }})</p>
        </div>
        <a href="{{ route('admin.semesters.index') }}" class="text-sm text-slate-600 hover:text-slate-900">Quay lại</a>
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

    <form action="{{ route('admin.semesters.update', $semester) }}" method="POST" class="bg-white rounded-lg border border-slate-200 p-6 space-y-4">
        @csrf
        @method('PUT')
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">Mã Học kỳ <span class="text-rose-500">*</span></label>
            <input type="text" name="code" value="{{ old('code', $semester->code) }}" required 
                class="w-full h-10 px-3 border border-slate-200 rounded-md text-sm uppercase font-mono focus:border-blue-600 focus:outline-none">
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Tên Học kỳ <span class="text-rose-500">*</span></label>
                <select name="name" required class="w-full h-10 px-3 border border-slate-200 rounded-md text-sm bg-white focus:border-blue-600 focus:outline-none">
                    <option value="Học kỳ 1" {{ old('name', $semester->name) === 'Học kỳ 1' ? 'selected' : '' }}>Học kỳ 1</option>
                    <option value="Học kỳ 2" {{ old('name', $semester->name) === 'Học kỳ 2' ? 'selected' : '' }}>Học kỳ 2</option>
                    <option value="Học kỳ 3" {{ old('name', $semester->name) === 'Học kỳ 3' ? 'selected' : '' }}>Học kỳ 3 (Hè)</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Năm học <span class="text-rose-500">*</span></label>
                <input type="text" name="academic_year" value="{{ old('academic_year', $semester->academic_year) }}" required 
                    class="w-full h-10 px-3 border border-slate-200 rounded-md text-sm focus:border-blue-600 focus:outline-none">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Ngày bắt đầu</label>
                <input type="date" name="start_date" value="{{ old('start_date', $semester->start_date ? $semester->start_date->format('Y-m-d') : '') }}" class="w-full h-10 px-3 border border-slate-200 rounded-md text-sm focus:border-blue-600 focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Ngày kết thúc</label>
                <input type="date" name="end_date" value="{{ old('end_date', $semester->end_date ? $semester->end_date->format('Y-m-d') : '') }}" class="w-full h-10 px-3 border border-slate-200 rounded-md text-sm focus:border-blue-600 focus:outline-none">
            </div>
        </div>

        <div class="pt-2">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_current" value="1" {{ old('is_current', $semester->is_current) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 rounded border-slate-300">
                <span class="text-sm font-medium text-slate-700">Đặt làm Học kỳ hiện tại</span>
            </label>
        </div>

        <div class="pt-4 flex justify-end gap-3 border-t border-slate-100">
            <a href="{{ route('admin.semesters.index') }}" class="px-4 py-2 border border-slate-200 text-slate-700 text-sm rounded-md hover:bg-slate-50">Hủy</a>
            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md shadow-xs">Cập nhật</button>
        </div>
    </form>
</div>
@endsection
