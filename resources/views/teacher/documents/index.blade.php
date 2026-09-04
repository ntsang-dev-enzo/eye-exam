@extends('layouts.teacher')

@section('title', 'Tài liệu học tập - ' . $subject->name)

@section('content')
<div class="max-w-6xl mx-auto space-y-6" x-data="{
    showCreateCategoryModal: false,
    showEditCategoryModal: false,
    showCreateDocumentModal: false,
    showEditDocumentModal: false,
    editCategoryData: { id: null, name: '', description: '', sort_order: 0 },
    editDocumentData: { id: null, title: '', description: '', category_id: null },
    selectedCategoryIdForDoc: null,
    
    openEditCategory(cat) {
        this.editCategoryData = {
            id: cat.id,
            name: cat.name,
            description: cat.description || '',
            sort_order: cat.sort_order || 0
        };
        this.showEditCategoryModal = true;
    },
    
    openCreateDocument(categoryId = null) {
        this.selectedCategoryIdForDoc = categoryId;
        this.showCreateDocumentModal = true;
    },
    
    openEditDocument(doc) {
        this.editDocumentData = {
            id: doc.id,
            title: doc.title,
            description: doc.description || '',
            category_id: doc.category_id
        };
        this.showEditDocumentModal = true;
    }
}">
    <!-- Header & Breadcrumbs -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-medium text-slate-500 mb-1">
                <a href="{{ route('teacher.classes.index') }}" class="hover:text-blue-600 transition-colors">Quản lý lớp</a>
                <span class="text-slate-300">/</span>
                <a href="{{ route('teacher.classes.show', $class) }}" class="hover:text-blue-600 transition-colors">{{ $class->code }}</a>
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
                Quản lý các danh mục bài giảng, slide và tài liệu đính kèm cho sinh viên
            </p>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('teacher.classes.show', $class) }}" class="px-3.5 py-2 bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 font-semibold text-xs rounded-xl transition-colors inline-flex items-center gap-1.5">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Quay lại lớp
            </a>
            <button type="button" @click="showCreateCategoryModal = true" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition-colors inline-flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tạo danh mục
            </button>
        </div>
    </div>

    <!-- Feedback Alerts -->
    @if(session('success'))
        <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

    @if($errors->any())
        <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm space-y-1">
            @foreach($errors->all() as $error)
                <p class="flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                    {{ $error }}
                </p>
            @endforeach
        </div>
    @endif

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200 p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
            </div>
            <div>
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Danh mục</span>
                <span class="text-lg font-black text-slate-900 font-mono">{{ $categories->count() }} mục</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div>
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Tổng tài liệu</span>
                <span class="text-lg font-black text-slate-900 font-mono">{{ $totalDocuments }} tệp</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-slate-50 text-slate-600 flex items-center justify-center border border-slate-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Định dạng hỗ trợ</span>
                <span class="text-xs font-bold text-slate-700">PDF, DOC, DOCX, ZIP</span>
            </div>
        </div>
    </div>

    <!-- Category List -->
    @if($categories->isEmpty())
        <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center">
            <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-3 border border-blue-100">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
            </div>
            <h3 class="text-base font-bold text-slate-900 mb-1">Chưa có danh mục tài liệu nào</h3>
            <p class="text-xs text-slate-500 mb-4">Bắt đầu bằng cách tạo các danh mục như "Chương 1", "Đề cương", "Bài tập thực hành"...</p>
            <button type="button" @click="showCreateCategoryModal = true" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition-colors inline-flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tạo danh mục đầu tiên
            </button>
        </div>
    @else
        <div class="space-y-5">
            @foreach($categories as $category)
                <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-2xs" x-data="{ expanded: true }">
                    <!-- Category Header -->
                    <div class="p-4 sm:px-6 bg-slate-50/75 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
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
                                    @if($category->sort_order > 0)
                                        <span class="px-2 py-0.5 bg-slate-200/70 text-slate-600 text-[10px] font-mono font-bold rounded">
                                            #{{ $category->sort_order }}
                                        </span>
                                    @endif
                                </div>
                                @if($category->description)
                                    <p class="text-xs text-slate-500 mt-0.5">{{ $category->description }}</p>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center gap-2 self-end sm:self-center">
                            <span class="text-xs font-semibold text-slate-500 font-mono bg-white px-2.5 py-1 rounded-lg border border-slate-200 mr-1">
                                {{ $category->documents->count() }} tài liệu
                            </span>

                            <button type="button" @click="openCreateDocument({{ $category->id }})" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-lg transition-colors inline-flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Thêm tài liệu
                            </button>

                            <button type="button" @click="openEditCategory({{ json_encode($category) }})" class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-white rounded-lg transition-colors" title="Sửa danh mục">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>

                            <form action="{{ route('teacher.document-categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa danh mục này? (Lưu ý: Chỉ xóa được khi danh mục không còn tài liệu)');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-white rounded-lg transition-colors" title="Xóa danh mục">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Document Table inside Category -->
                    <div x-show="expanded" class="divide-y divide-slate-100">
                        @if($category->documents->isEmpty())
                            <div class="p-8 text-center">
                                <p class="text-xs text-slate-400 italic mb-2">Chưa có tài liệu nào trong danh mục này.</p>
                                <button type="button" @click="openCreateDocument({{ $category->id }})" class="text-xs text-blue-600 hover:text-blue-700 font-bold inline-flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    Tải lên tài liệu ngay
                                </button>
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
                                                    {{ $doc->created_at->format('d/m/Y H:i') }}
                                                </td>

                                                <td class="py-3 px-6 text-right">
                                                    <div class="flex items-center justify-end gap-1.5">
                                                        @if($doc->file_type === 'pdf')
                                                            <a href="{{ route('teacher.documents.view', $doc) }}" target="_blank" class="px-2.5 py-1 bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 font-bold text-[11px] rounded-lg transition-colors inline-flex items-center gap-1" title="Xem trực tiếp trên trình duyệt">
                                                                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                                Xem
                                                            </a>
                                                        @endif

                                                        <a href="{{ route('teacher.documents.download', $doc) }}" class="px-2.5 py-1 bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold text-[11px] rounded-lg border border-blue-200/60 transition-colors inline-flex items-center gap-1" title="Tải xuống tệp">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                            Tải
                                                        </a>

                                                        <button type="button" @click="openEditDocument({{ json_encode($doc) }})" class="p-1 text-slate-400 hover:text-blue-600 rounded transition-colors" title="Sửa thông tin tài liệu">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                        </button>

                                                        <form action="{{ route('teacher.documents.destroy', $doc) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa tài liệu này?');" class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="p-1 text-slate-400 hover:text-rose-600 rounded transition-colors" title="Xóa tài liệu">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                            </button>
                                                        </form>
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

    <!-- MODAL 1: Create Category -->
    <template x-teleport="body">
        <div x-show="showCreateCategoryModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" @click="showCreateCategoryModal = false"></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative bg-white rounded-2xl p-6 max-w-md w-full shadow-xl border border-slate-200">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                        <h3 class="text-base font-bold text-slate-900">Tạo danh mục tài liệu mới</h3>
                        <button type="button" @click="showCreateCategoryModal = false" class="text-slate-400 hover:text-slate-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <form action="{{ route('teacher.classes.subjects.categories.store', [$class, $subject]) }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Tên danh mục <span class="text-rose-500">*</span></label>
                            <input type="text" name="name" required placeholder="Ví dụ: Chương 1: Giới thiệu chung" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Mô tả (tùy chọn)</label>
                            <textarea name="description" rows="2" placeholder="Ghi chú thêm về nội dung danh mục..." class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600"></textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Thứ tự sắp xếp (số nhỏ xếp trước)</label>
                            <input type="number" name="sort_order" value="0" min="0" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600">
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                            <button type="button" @click="showCreateCategoryModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition-colors">
                                Hủy
                            </button>
                            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl transition-colors">
                                Tạo danh mục
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </template>

    <!-- MODAL 2: Edit Category -->
    <template x-teleport="body">
        <div x-show="showEditCategoryModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" @click="showEditCategoryModal = false"></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative bg-white rounded-2xl p-6 max-w-md w-full shadow-xl border border-slate-200">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                        <h3 class="text-base font-bold text-slate-900">Chỉnh sửa danh mục</h3>
                        <button type="button" @click="showEditCategoryModal = false" class="text-slate-400 hover:text-slate-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <form :action="'/giang-vien/danh-muc-tai-lieu/' + editCategoryData.id" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Tên danh mục <span class="text-rose-500">*</span></label>
                            <input type="text" name="name" x-model="editCategoryData.name" required class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Mô tả (tùy chọn)</label>
                            <textarea name="description" x-model="editCategoryData.description" rows="2" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600"></textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Thứ tự sắp xếp</label>
                            <input type="number" name="sort_order" x-model="editCategoryData.sort_order" min="0" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600">
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                            <button type="button" @click="showEditCategoryModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition-colors">
                                Hủy
                            </button>
                            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl transition-colors">
                                Lưu thay đổi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </template>

    <!-- MODAL 3: Create Document -->
    <template x-teleport="body">
        <div x-show="showCreateDocumentModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" @click="showCreateDocumentModal = false"></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative bg-white rounded-2xl p-6 max-w-lg w-full shadow-xl border border-slate-200">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                        <h3 class="text-base font-bold text-slate-900">Tải lên tài liệu học tập</h3>
                        <button type="button" @click="showCreateDocumentModal = false" class="text-slate-400 hover:text-slate-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <form action="{{ route('teacher.classes.subjects.documents.store', [$class, $subject]) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Danh mục tài liệu <span class="text-rose-500">*</span></label>
                            <select name="category_id" required class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600">
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" :selected="selectedCategoryIdForDoc == {{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Tiêu đề tài liệu <span class="text-rose-500">*</span></label>
                            <input type="text" name="title" required placeholder="Ví dụ: Slide bài giảng Chương 1 - Phần 1" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Mô tả (tùy chọn)</label>
                            <textarea name="description" rows="2" placeholder="Ghi chú tóm tắt nội dung tài liệu..." class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600"></textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Tệp đính kèm <span class="text-rose-500">*</span></label>
                            <input type="file" name="file" required accept=".pdf,.doc,.docx,.zip" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            <p class="text-[11px] text-slate-400 mt-1">Định dạng hỗ trợ: <strong>PDF, DOC, DOCX</strong> (Tối đa 20MB), <strong>ZIP</strong> (Tối đa 100MB)</p>
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                            <button type="button" @click="showCreateDocumentModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition-colors">
                                Hủy
                            </button>
                            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl transition-colors">
                                Tải lên
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </template>

    <!-- MODAL 4: Edit Document -->
    <template x-teleport="body">
        <div x-show="showEditDocumentModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" @click="showEditDocumentModal = false"></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative bg-white rounded-2xl p-6 max-w-lg w-full shadow-xl border border-slate-200">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                        <h3 class="text-base font-bold text-slate-900">Chỉnh sửa tài liệu</h3>
                        <button type="button" @click="showEditDocumentModal = false" class="text-slate-400 hover:text-slate-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <form :action="'/giang-vien/tai-lieu/' + editDocumentData.id" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        @method('PUT')
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Danh mục tài liệu <span class="text-rose-500">*</span></label>
                            <select name="category_id" x-model="editDocumentData.category_id" required class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600">
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Tiêu đề tài liệu <span class="text-rose-500">*</span></label>
                            <input type="text" name="title" x-model="editDocumentData.title" required class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Mô tả (tùy chọn)</label>
                            <textarea name="description" x-model="editDocumentData.description" rows="2" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600"></textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Thay đổi tệp đính kèm (để trống nếu giữ nguyên tệp cũ)</label>
                            <input type="file" name="file" accept=".pdf,.doc,.docx,.zip" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200">
                            <p class="text-[11px] text-slate-400 mt-1">Nếu tải lên tệp mới, tệp cũ sẽ tự động được xóa khỏi hệ thống.</p>
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                            <button type="button" @click="showEditDocumentModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition-colors">
                                Hủy
                            </button>
                            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl transition-colors">
                                Cập nhật tài liệu
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </template>
</div>
@endsection
