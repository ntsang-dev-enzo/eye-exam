@extends('layouts.teacher')

@section('title', 'Ngân hàng Câu hỏi')

@section('content')
    <div class="space-y-6">
        <!-- Header & Filters -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <h3 class="text-xl font-bold text-gray-900">Ngân hàng Câu hỏi</h3>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-100">
                        {{ $questions->total() }} câu hỏi
                    </span>
                </div>
                <p class="text-sm text-gray-500 mt-1">Quản lý, phân loại câu hỏi theo danh mục môn học và chuyên đề</p>
            </div>
            
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('teacher.categories.index') }}" class="inline-flex items-center px-3.5 py-2 bg-purple-50 hover:bg-purple-100 text-purple-700 text-sm font-semibold rounded-xl transition-colors border border-purple-200 shadow-sm">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    Quản lý Danh mục
                </a>

                <a href="{{ route('teacher.questions.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-xl shadow-sm shadow-blue-200 transition-colors">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tạo câu hỏi mới
                </a>
            </div>
        </div>

        <!-- Filter Bar & View Mode Toggle -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <form action="{{ route('teacher.questions.index') }}" method="GET" class="flex flex-wrap items-center gap-3 flex-1">
                <div class="relative flex-1 min-w-[200px] max-w-xs">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm nội dung câu hỏi..." class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-xl text-sm focus:ring-blue-500 focus:border-blue-500 shadow-sm">
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

                <select name="category_id" class="text-sm rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500" onchange="this.form.submit()">
                    <option value="">-- Tất cả danh mục --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>

                <select name="type" class="text-sm rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500" onchange="this.form.submit()">
                    <option value="">-- Loại câu --</option>
                    <option value="multiple_choice" {{ request('type') === 'multiple_choice' ? 'selected' : '' }}>Trắc nghiệm</option>
                    <option value="essay" {{ request('type') === 'essay' ? 'selected' : '' }}>Tự luận</option>
                </select>

                @if(request('search') || request('subject_id') || request('category_id') || request('type'))
                    <a href="{{ route('teacher.questions.index') }}" class="text-xs text-gray-500 hover:text-rose-600 font-semibold underline">
                        Xóa bộ lọc
                    </a>
                @endif
            </form>
        </div>

        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-100 flex items-center gap-2">
                <svg class="w-5 h-5 shrink-0 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Floating Bulk Action Toolbar -->
        <form id="bulkForm" action="{{ route('teacher.questions.bulk-category') }}" method="POST">
            @csrf
            <div id="bulkActionToolbar" class="fixed bottom-6 left-1/2 transform -translate-x-1/2 z-40 bg-gray-900 text-white px-6 py-3.5 rounded-2xl shadow-2xl flex items-center gap-4 hidden transition-all border border-gray-800">
                <span class="text-sm font-semibold flex items-center gap-2">
                    <span id="selectedCountBadge" class="w-6 h-6 rounded-full bg-blue-600 text-xs flex items-center justify-center font-bold">0</span>
                    câu hỏi đã chọn
                </span>
                
                <div class="h-5 w-px bg-gray-700"></div>

                <div class="flex items-center gap-2">
                    <span class="text-xs text-gray-300">Chuyển sang danh mục:</span>
                    <select name="category_id" class="text-xs text-gray-900 rounded-lg border-0 px-3 py-1.5 focus:ring-blue-500">
                        <option value="">-- Chưa phân loại --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }} ({{ $cat->subject->name ?? '' }})</option>
                        @endforeach
                    </select>
                    <button type="submit" class="px-3.5 py-1.5 bg-blue-600 hover:bg-blue-500 text-xs font-bold rounded-lg transition-colors shadow-sm">
                        Lưu chuyển đổi
                    </button>
                </div>

                <button type="button" onclick="clearBulkSelection()" class="text-xs text-gray-400 hover:text-white ml-2">
                    Bỏ chọn
                </button>
            </div>

            <!-- Table View -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                <th class="px-4 py-4 border-b border-gray-100 w-10 text-center">
                                    <input type="checkbox" id="selectAllCheckbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                                </th>
                                <th class="px-4 py-4 border-b border-gray-100 w-16">ID</th>
                                <th class="px-6 py-4 border-b border-gray-100">Nội dung câu hỏi</th>
                                <th class="px-6 py-4 border-b border-gray-100 w-44">Danh mục môn học</th>
                                <th class="px-6 py-4 border-b border-gray-100 w-28 text-center">Loại</th>
                                <th class="px-6 py-4 border-b border-gray-100 w-24 text-center">Độ khó</th>
                                <th class="px-6 py-4 border-b border-gray-100 text-right w-24">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($questions as $question)
                                <tr class="hover:bg-gray-50 transition-colors question-row">
                                    <td class="px-4 py-4 text-center">
                                        <input type="checkbox" name="question_ids[]" value="{{ $question->id }}" class="question-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-500 font-mono">#{{ $question->id }}</td>
                                    <td class="px-6 py-4">
                                        <p class="text-sm font-semibold text-gray-900 line-clamp-2">{{ $question->content }}</p>
                                        <p class="text-xs text-gray-500 mt-1">Môn: <span class="font-medium text-gray-700">{{ $question->subject->name ?? 'N/A' }}</span></p>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($question->category)
                                            <a href="{{ route('teacher.questions.index', ['subject_id' => $question->subject_id, 'category_id' => $question->category_id]) }}" class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-purple-50 text-purple-700 hover:bg-purple-100 border border-purple-100 transition-colors" title="Lọc theo danh mục này">
                                                <span class="w-1.5 h-1.5 rounded-full bg-purple-500 mr-1.5"></span>
                                                {{ $question->category->name }}
                                            </a>
                                        @else
                                            <span class="text-xs text-gray-400 italic">Chưa phân loại</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($question->type === 'multiple_choice')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700">Trắc nghiệm</span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-50 text-purple-700">Tự luận</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($question->difficulty === 'easy')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700">Dễ</span>
                                        @elseif($question->difficulty === 'medium')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-700">TB</span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-rose-50 text-rose-700">Khó</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <a href="{{ route('teacher.questions.edit', $question) }}" class="p-1.5 text-gray-400 hover:text-blue-600 rounded-lg hover:bg-blue-50 transition-colors" title="Sửa">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                            </a>
                                            <button type="button" onclick="deleteSingleQuestion('{{ route('teacher.questions.destroy', $question) }}')" class="p-1.5 text-gray-400 hover:text-rose-600 rounded-lg hover:bg-rose-50 transition-colors" title="Xóa">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                        Chưa có câu hỏi nào phù hợp với bộ lọc. <a href="{{ route('teacher.questions.create') }}" class="text-blue-600 hover:underline">Tạo câu hỏi mới ngay</a>.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $questions->links() }}
                </div>
            </div>
        </form>
    </div>

    <!-- Hidden Form for Single Question Deletion -->
    <form id="deleteSingleForm" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>

    <script>
        const selectAll = document.getElementById('selectAllCheckbox');
        const checkboxes = document.querySelectorAll('.question-checkbox');
        const toolbar = document.getElementById('bulkActionToolbar');
        const countBadge = document.getElementById('selectedCountBadge');

        function updateToolbar() {
            const checkedBoxes = document.querySelectorAll('.question-checkbox:checked');
            const count = checkedBoxes.length;
            countBadge.textContent = count;
            if (count > 0) {
                toolbar.classList.remove('hidden');
            } else {
                toolbar.classList.add('hidden');
            }
        }

        selectAll.addEventListener('change', function() {
            checkboxes.forEach(cb => cb.checked = this.checked);
            updateToolbar();
        });

        checkboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                updateToolbar();
                if (!this.checked) {
                    selectAll.checked = false;
                }
            });
        });

        function clearBulkSelection() {
            checkboxes.forEach(cb => cb.checked = false);
            selectAll.checked = false;
            updateToolbar();
        }

        function deleteSingleQuestion(url) {
            if (confirm('Bạn có chắc chắn muốn xóa câu hỏi này?')) {
                const form = document.getElementById('deleteSingleForm');
                form.action = url;
                form.submit();
            }
        }
    </script>
@endsection
