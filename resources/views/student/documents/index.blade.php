@extends('layouts.student')

@section('title', 'Tài liệu học tập - ' . $subject->name)

@section('content')
<div class="space-y-6">
    <!-- Header & Breadcrumbs -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-medium text-slate-500 mb-1">
                <a href="{{ route('student.classes.index') }}" class="hover:text-blue-600 transition-colors">Lớp học của tôi</a>
                <span class="text-slate-300">/</span>
                <a href="{{ route('student.classes.show', $class) }}" class="hover:text-blue-600 transition-colors">{{ $class->code }}</a>
                <span class="text-slate-300">/</span>
                <span class="text-slate-800 font-semibold">{{ $subject->code }}</span>
            </div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Tài liệu học tập: {{ $subject->name }}</h1>
                <span class="px-2.5 py-0.5 bg-blue-50 text-blue-700 font-bold text-xs rounded-full border border-blue-200">
                    Lớp {{ $class->code }}
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-0.5">
                Giảng viên phụ trách: <strong class="text-slate-700">{{ $teacher->name ?? 'N/A' }}</strong>
            </p>
        </div>

        <a href="{{ route('student.classes.show', $class) }}" class="px-3.5 py-2 bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 font-semibold text-xs rounded-xl transition-colors inline-flex items-center gap-1.5 self-start sm:self-auto">
            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Quay lại lớp
        </a>
    </div>

    <!-- Quick Overview Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200 p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
            </div>
            <div>
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Danh mục tài liệu</span>
                <span class="text-lg font-black text-slate-900 font-mono">{{ $categories->count() }} mục</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-100 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div>
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Tổng số tệp tài liệu</span>
                <span class="text-lg font-black text-slate-900 font-mono">{{ $totalDocuments }} tệp khả dụng</span>
            </div>
        </div>
    </div>

    <!-- Category & Documents List -->
    @if($categories->isEmpty())
        <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center">
            <div class="w-14 h-14 bg-slate-50 text-slate-400 rounded-2xl flex items-center justify-center mx-auto mb-3 border border-slate-200">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
            </div>
            <h3 class="text-base font-bold text-slate-900 mb-1">Chưa có tài liệu học tập</h3>
            <p class="text-xs text-slate-500">Giảng viên phụ trách chưa đăng tài liệu cho môn học này.</p>
        </div>
    @else
        <div class="space-y-5">
            @foreach($categories as $category)
                <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-2xs" x-data="{ expanded: true }">
                    <!-- Category Header -->
                    <div class="p-4 sm:px-6 bg-slate-50/75 border-b border-slate-200 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <button type="button" @click="expanded = !expanded" class="p-1 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-200/60 transition-colors">
                                <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-90': expanded }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </button>

                            <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center border border-amber-200/60 shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                            </div>

                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="font-bold text-slate-900 text-sm sm:text-base">{{ $category->name }}</h3>
                                </div>
                                @if($category->description)
                                    <p class="text-xs text-slate-500 mt-0.5">{{ $category->description }}</p>
                                @endif
                            </div>
                        </div>

                        <span class="text-xs font-semibold text-slate-500 font-mono bg-white px-2.5 py-1 rounded-lg border border-slate-200">
                            {{ $category->documents->count() }} tài liệu
                        </span>
                    </div>

                    <!-- Documents List inside Category -->
                    <div x-show="expanded" class="divide-y divide-slate-100">
                        @if($category->documents->isEmpty())
                            <div class="p-6 text-center text-xs text-slate-400 italic">
                                Chưa có tài liệu nào trong danh mục này.
                            </div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse">
                                    <thead>
                                        <tr class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider bg-white">
                                            <th class="py-2.5 px-6">Tên tài liệu</th>
                                            <th class="py-2.5 px-4">Loại & Dung lượng</th>
                                            <th class="py-2.5 px-4">Ngày đăng</th>
                                            <th class="py-2.5 px-6 text-right">Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 text-xs">
                                        @foreach($category->documents as $doc)
                                            <tr class="hover:bg-slate-50/75 transition-colors group">
                                                <td class="py-3 px-6">
                                                    <div class="flex items-center gap-3">
                                                        <!-- Icon based on type -->
                                                        @if($doc->file_type === 'pdf')
                                                            <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center border border-rose-200/60 shrink-0 font-mono font-bold text-[10px]">
                                                                PDF
                                                            </div>
                                                        @elseif(in_array($doc->file_type, ['doc', 'docx']))
                                                            <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-200/60 shrink-0 font-mono font-bold text-[10px]">
                                                                DOC
                                                            </div>
                                                        @elseif($doc->file_type === 'zip')
                                                            <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center border border-amber-200/60 shrink-0 font-mono font-bold text-[10px]">
                                                                ZIP
                                                            </div>
                                                        @else
                                                            <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center border border-slate-200 shrink-0 font-mono font-bold text-[10px]">
                                                                FILE
                                                            </div>
                                                        @endif

                                                        <div class="min-w-0">
                                                            <h4 class="font-bold text-slate-900 text-xs group-hover:text-blue-600 transition-colors truncate">
                                                                {{ $doc->title }}
                                                            </h4>
                                                            <p class="text-[11px] text-slate-400 font-mono truncate">
                                                                {{ $doc->original_filename }}
                                                            </p>
                                                            @if($doc->description)
                                                                <p class="text-[11px] text-slate-500 mt-0.5 line-clamp-1">{{ $doc->description }}</p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>

                                                <td class="py-3 px-4 text-slate-600">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase font-mono {{ $doc->file_type === 'pdf' ? 'bg-rose-50 text-rose-700 border border-rose-200/60' : ($doc->file_type === 'zip' ? 'bg-amber-50 text-amber-800 border border-amber-200/60' : 'bg-blue-50 text-blue-700 border border-blue-200/60') }}">
                                                        {{ $doc->file_type }}
                                                    </span>
                                                    <span class="text-slate-400 text-[11px] font-mono ml-1.5">{{ $doc->formatted_file_size }}</span>
                                                </td>

                                                <td class="py-3 px-4 text-slate-500 text-[11px]">
                                                    {{ $doc->created_at->format('d/m/Y') }}
                                                </td>

                                                <td class="py-3 px-6 text-right">
                                                    <div class="flex items-center justify-end gap-2">
                                                        @if($doc->file_type === 'pdf')
                                                            <a href="{{ route('student.documents.view', $doc) }}" target="_blank" class="px-3 py-1 bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 font-bold text-[11px] rounded-lg transition-colors inline-flex items-center gap-1" title="Xem trực tiếp trên trình duyệt">
                                                                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                                Xem
                                                            </a>
                                                        @endif

                                                        <a href="{{ route('student.documents.download', $doc) }}" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white font-bold text-[11px] rounded-lg transition-colors inline-flex items-center gap-1" title="Tải xuống tệp">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                            Tải xuống
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
