@extends('layouts.teacher')

@section('title', 'Quản lý Danh mục Môn học')

@section('content')
    <div class="space-y-6">
        <!-- Header & Quick Actions -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <h3 class="text-xl font-bold text-gray-900">Danh mục & Chuyên đề Môn học</h3>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-purple-50 text-purple-700 border border-purple-100">
                        {{ $categories->count() }} danh mục
                    </span>
                </div>
                <p class="text-sm text-gray-500 mt-1">Phân loại câu hỏi và đề thi theo từng chương, chuyên đề của môn học để quản lý dễ dàng</p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <form action="{{ route('teacher.categories.index') }}" method="GET" class="flex flex-wrap items-center gap-2">
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm tên danh mục..." class="pl-8 pr-3 py-2 border border-gray-300 rounded-xl text-sm focus:ring-blue-500 focus:border-blue-500 w-48 shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                    </div>

                    <select name="subject_id" class="text-sm rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500" onchange="this.form.submit()">
                        <option value="">-- Tất cả môn học --</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                                {{ $subject->name }}
                            </option>
                        @endforeach
                    </select>
                </form>

                <button type="button" onclick="openCreateCategoryModal()" class="inline-flex items-center justify-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-xl shadow-sm shadow-blue-200 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tạo danh mục mới
                </button>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-100 flex items-center gap-2">
                <svg class="w-5 h-5 shrink-0 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Categories Grouped by Subject -->
        @if($categoriesBySubject->isEmpty())
            <div class="bg-white rounded-2xl p-12 text-center border border-gray-100 shadow-sm">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-purple-50 text-purple-600 mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-1">Chưa có danh mục môn học nào</h3>
                <p class="text-gray-500 mb-4">Hãy tạo danh mục đầu tiên để gom nhóm câu hỏi theo từng chương hoặc chủ đề.</p>
                <button type="button" onclick="openCreateCategoryModal()" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-medium text-sm rounded-xl shadow-sm hover:bg-blue-700">
                    + Tạo danh mục ngay
                </button>
            </div>
        @else
            <div class="space-y-6">
                @foreach($categoriesBySubject as $subjectName => $subjectCategories)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-6 py-4 bg-gray-50/70 border-b border-gray-100 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span class="w-3 h-3 rounded-full bg-blue-600"></span>
                                <h4 class="font-bold text-gray-800 text-base">Môn: {{ $subjectName }}</h4>
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-200 text-gray-700">{{ $subjectCategories->count() }} danh mục</span>
                            </div>
                            <button type="button" onclick="openCreateCategoryModal({{ $subjectCategories->first()->subject_id ?? '' }})" class="text-xs font-semibold text-blue-600 hover:text-blue-800 hover:underline flex items-center gap-1">
                                + Thêm danh mục cho môn này
                            </button>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-gray-50/30 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        <th class="px-6 py-3.5 border-b border-gray-100">Tên danh mục / Chuyên đề</th>
                                        <th class="px-6 py-3.5 border-b border-gray-100">Mô tả</th>
                                        <th class="px-6 py-3.5 border-b border-gray-100 text-center w-36">Số câu hỏi</th>
                                        <th class="px-6 py-3.5 border-b border-gray-100 text-center w-32">Số đề thi</th>
                                        <th class="px-6 py-3.5 border-b border-gray-100 text-right w-28">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($subjectCategories as $cat)
                                        <tr class="hover:bg-gray-50/60 transition-colors">
                                            <td class="px-6 py-4">
                                                <div class="flex items-center gap-2">
                                                    <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                                                    <span class="font-bold text-sm text-gray-900">{{ $cat->name }}</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-xs text-gray-500">
                                                {{ $cat->description ?: 'Chưa có mô tả' }}
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <a href="{{ route('teacher.questions.index', ['subject_id' => $cat->subject_id, 'category_id' => $cat->id]) }}" class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 hover:bg-blue-100 transition-colors" title="Xem các câu hỏi">
                                                    {{ $cat->questions_count }} câu hỏi &rarr;
                                                </a>
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <a href="{{ route('teacher.exams.index', ['subject_id' => $cat->subject_id, 'category_id' => $cat->id]) }}" class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-purple-50 text-purple-700 hover:bg-purple-100 transition-colors" title="Xem các đề thi">
                                                    {{ $cat->exams_count }} đề thi
                                                </a>
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" 
                                                            onclick="openEditCategoryModal({{ $cat->id }}, '{{ addslashes($cat->name) }}', {{ $cat->subject_id ?? 'null' }}, '{{ addslashes($cat->description ?? '') }}')" 
                                                            class="p-1.5 text-gray-400 hover:text-blue-600 rounded-lg hover:bg-blue-50 transition-colors" 
                                                            title="Sửa danh mục">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                    </button>
                                                    <button type="button" 
                                                            onclick="confirmDeleteCategory('{{ route('teacher.categories.destroy', $cat) }}', '{{ addslashes($cat->name) }}', {{ $cat->questions_count }})" 
                                                            class="p-1.5 text-gray-400 hover:text-rose-600 rounded-lg hover:bg-rose-50 transition-colors" 
                                                            title="Xóa danh mục">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Modal Tạo / Sửa Danh mục -->
    <div id="categoryModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" onclick="closeCategoryModal()"></div>
        <div class="flex items-center justify-center min-h-screen px-4 text-center sm:p-0">
            <div class="relative bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-md w-full p-6 space-y-4">
                <div class="flex justify-between items-center pb-2 border-b border-gray-100">
                    <h3 id="categoryModalTitle" class="text-lg font-bold text-gray-900">Tạo danh mục mới</h3>
                    <button type="button" onclick="closeCategoryModal()" class="text-gray-400 hover:text-gray-500">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form id="categoryForm" method="POST" action="{{ route('teacher.categories.store') }}" class="space-y-4">
                    @csrf
                    <div id="methodContainer"></div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Môn học <span class="text-red-500">*</span></label>
                        <select name="subject_id" id="modal_subject_id" required class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 px-4 py-2 text-sm bg-gray-50">
                            <option value="">-- Chọn môn học --</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tên danh mục / Chuyên đề <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="modal_category_name" required placeholder="VD: Chương 1: Giới thiệu căn bản" class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 px-4 py-2 text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả (Tùy chọn)</label>
                        <textarea name="description" id="modal_category_description" rows="3" placeholder="Mô tả nội dung chương hoặc chuyên đề..." class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 px-4 py-2 text-sm"></textarea>
                    </div>

                    <div class="pt-3 flex gap-3 border-t border-gray-100">
                        <button type="button" onclick="closeCategoryModal()" class="flex-1 px-4 py-2.5 bg-gray-100 text-gray-700 font-medium text-sm rounded-xl hover:bg-gray-200 transition-colors">
                            Hủy bỏ
                        </button>
                        <button type="submit" id="btnSubmitCategory" class="flex-1 px-4 py-2.5 bg-blue-600 text-white font-medium text-sm rounded-xl hover:bg-blue-700 transition-colors shadow-sm shadow-blue-200">
                            Lưu danh mục
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Xác nhận Xóa Danh mục -->
    <div id="deleteCategoryModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" onclick="closeDeleteCategoryModal()"></div>
        <div class="flex items-center justify-center min-h-screen px-4 text-center sm:p-0">
            <div class="relative bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-md w-full p-6 space-y-4">
                <div class="w-12 h-12 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center mx-auto">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <div class="text-center">
                    <h3 class="text-lg font-bold text-gray-900">Xác nhận xóa danh mục</h3>
                    <p class="text-sm text-gray-500 mt-2">
                        Bạn có chắc chắn muốn xóa danh mục <span id="deleteCategoryName" class="font-bold text-gray-800"></span>?
                    </p>
                    <p class="text-xs text-amber-700 bg-amber-50 p-2.5 rounded-lg border border-amber-100 mt-3 text-left">
                        ℹ️ Lưu ý: Các câu hỏi và đề thi thuộc danh mục này sẽ chuyển sang trạng thái <strong>"Chưa phân loại"</strong> (không bị xóa nội dung câu hỏi).
                    </p>
                </div>
                <form id="deleteCategoryForm" method="POST" class="pt-3 flex gap-3">
                    @csrf
                    @method('DELETE')
                    <button type="button" onclick="closeDeleteCategoryModal()" class="flex-1 px-4 py-2.5 bg-gray-100 text-gray-700 font-medium text-sm rounded-xl hover:bg-gray-200 transition-colors">
                        Hủy bỏ
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2.5 bg-rose-600 text-white font-medium text-sm rounded-xl hover:bg-rose-700 transition-colors shadow-sm shadow-rose-200">
                        Đồng ý xóa
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openCreateCategoryModal(subjectId = null) {
            document.getElementById('categoryModalTitle').textContent = 'Tạo danh mục mới';
            const form = document.getElementById('categoryForm');
            form.action = "{{ route('teacher.categories.store') }}";
            document.getElementById('methodContainer').innerHTML = '';
            document.getElementById('modal_category_name').value = '';
            document.getElementById('modal_category_description').value = '';
            if (subjectId) {
                document.getElementById('modal_subject_id').value = subjectId;
            } else {
                document.getElementById('modal_subject_id').value = '';
            }
            document.getElementById('categoryModal').classList.remove('hidden');
        }

        function openEditCategoryModal(id, name, subjectId, description) {
            document.getElementById('categoryModalTitle').textContent = 'Sửa danh mục';
            const form = document.getElementById('categoryForm');
            form.action = `/giang-vien/danh-muc-mon-hoc/${id}`;
            document.getElementById('methodContainer').innerHTML = '<input type="hidden" name="_method" value="PUT">';
            document.getElementById('modal_category_name').value = name;
            document.getElementById('modal_category_description').value = description;
            document.getElementById('modal_subject_id').value = subjectId || '';
            document.getElementById('categoryModal').classList.remove('hidden');
        }

        function closeCategoryModal() {
            document.getElementById('categoryModal').classList.add('hidden');
        }

        function confirmDeleteCategory(deleteUrl, categoryName, questionCount) {
            document.getElementById('deleteCategoryForm').action = deleteUrl;
            document.getElementById('deleteCategoryName').textContent = `"${categoryName}"`;
            document.getElementById('deleteCategoryModal').classList.remove('hidden');
        }

        function closeDeleteCategoryModal() {
            document.getElementById('deleteCategoryModal').classList.add('hidden');
        }
    </script>
@endsection
