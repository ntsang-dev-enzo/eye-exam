@extends('layouts.teacher')

@section('title', 'Quản lý Đề thi')

@section('content')
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <!-- Header & Filters -->
        <div class="px-6 py-5 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <h3 class="font-semibold text-gray-800">Danh sách Đề thi</h3>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                    {{ $exams->total() }} đề thi
                </span>
            </div>
            
            <div class="flex flex-wrap items-center gap-3">
                <form action="{{ route('teacher.exams.index') }}" method="GET" class="flex flex-wrap items-center gap-2">
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm mã, tên đề..." class="pl-8 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 w-48 shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                    </div>

                    <select name="class_id" class="text-sm rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 max-w-xs" onchange="this.form.submit()">
                        <option value="">-- Tất cả lớp --</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>
                                {{ $c->name }}
                            </option>
                        @endforeach
                    </select>

                    <select name="subject_id" class="text-sm rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 max-w-xs" onchange="this.form.submit()">
                        <option value="">-- Tất cả môn học --</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                                {{ $subject->name }}
                            </option>
                        @endforeach
                    </select>

                    @if(isset($categories) && $categories->count() > 0)
                        <select name="category_id" class="text-sm rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 max-w-xs" onchange="this.form.submit()">
                            <option value="">-- Tất cả danh mục --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    @endif

                    <select name="status" class="text-sm rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" onchange="this.form.submit()">
                        <option value="">-- Tất cả trạng thái --</option>
                        <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Mở</option>
                        <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Đóng</option>
                    </select>
                </form>
                
                <a href="{{ route('teacher.exams.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tạo đề thi mới
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="m-6 p-4 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-100 flex items-center gap-2">
                <svg class="w-5 h-5 shrink-0 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <th class="px-6 py-4 border-b border-gray-100">Mã đề / Tên đề thi</th>
                        <th class="px-6 py-4 border-b border-gray-100">Cấu trúc</th>
                        <th class="px-6 py-4 border-b border-gray-100">Thời gian biểu</th>
                        <th class="px-6 py-4 border-b border-gray-100 text-center">Trạng thái</th>
                        <th class="px-6 py-4 border-b border-gray-100 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($exams as $exam)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2 mb-1 flex-wrap">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-gray-200 text-gray-700">{{ $exam->code }}</span>
                                    <p class="text-sm font-bold text-gray-900">{{ $exam->title }}</p>
                                    @if($exam->category)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-medium bg-purple-50 text-purple-700 border border-purple-100">
                                            {{ $exam->category->name }}
                                        </span>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-500">Môn: <span class="font-medium">{{ $exam->subject->name ?? 'N/A' }}</span></p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm text-gray-700 font-medium">{{ $exam->total_questions }} câu hỏi</p>
                                <p class="text-xs text-gray-500 mt-1">Làm bài: <span class="font-semibold text-blue-600">{{ $exam->duration_minutes }} phút</span></p>
                            </td>
                            <td class="px-6 py-4 ">
                                @if($exam->start_at || $exam->end_at)
                                    <p class="text-xs text-gray-600"><span class="font-medium text-emerald-600">Mở:</span> {{ $exam->start_at ? $exam->start_at->format('d/m/Y H:i') : 'Không giới hạn' }}</p>
                                    <p class="text-xs text-gray-600 mt-1"><span class="font-medium text-rose-600">Đóng:</span> {{ $exam->end_at ? $exam->end_at->format('d/m/Y H:i') : 'Không giới hạn' }}</p>
                                @else
                                    <span class="text-xs text-gray-500 italic">Luôn mở</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <form action="{{ route('teacher.exams.update-status', $exam) }}" method="POST" class="inline-block m-0">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" onchange="this.form.submit()" class="text-xs font-semibold rounded-full px-2.5 py-1 border-0 ring-1 ring-inset shadow-sm cursor-pointer appearance-none text-center
                                        @if($exam->status == 'published') bg-blue-50 text-blue-700 ring-blue-200 focus:ring-blue-300
                                        @else bg-red-50 text-red-700 ring-red-200 focus:ring-red-300
                                        @endif
                                    ">
                                        <option value="published" {{ $exam->status == 'published' ? 'selected' : '' }}>Mở</option>
                                        <option value="closed" {{ $exam->status == 'closed' ? 'selected' : '' }}>Đóng</option>
                                    </select>
                                </form>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('teacher.exams.results', $exam) }}" class="p-1.5 text-gray-400 hover:text-emerald-600 rounded-lg hover:bg-emerald-50 transition-colors" title="Xem kết quả">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                                    </a>
                                    <a href="{{ route('teacher.exams.edit', $exam) }}" class="p-1.5 text-gray-400 hover:text-indigo-600 rounded-lg hover:bg-indigo-50 transition-colors" title="Sửa đề thi">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    </a>
                                    <a href="{{ route('teacher.exams.monitor', $exam) }}" class="p-1.5 text-gray-400 hover:text-blue-600 rounded-lg hover:bg-blue-50 transition-colors" title="Giám sát phòng thi">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </a>
                                    <!-- Nút Xóa đề thi -->
                                    <button type="button" 
                                            onclick="confirmDeleteExam('{{ route('teacher.exams.destroy', $exam) }}', '{{ addslashes($exam->title) }}', '{{ $exam->code }}')" 
                                            class="p-1.5 text-gray-400 hover:text-rose-600 rounded-lg hover:bg-rose-50 transition-colors" 
                                            title="Xóa đề thi">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                Bạn chưa tạo kỳ thi nào. <a href="{{ route('teacher.exams.create') }}" class="text-blue-600 hover:underline">Tạo mới ngay</a>.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $exams->links() }}
        </div>
    </div>

    <!-- Modal Xác nhận Xóa đề thi -->
    <div id="deleteModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" onclick="closeDeleteModal()"></div>
        <div class="flex items-center justify-center min-h-screen px-4 text-center sm:p-0">
            <div class="relative bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-md w-full p-6 space-y-4">
                <div class="w-12 h-12 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center mx-auto">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <div class="text-center">
                    <h3 class="text-lg font-bold text-gray-900">Xác nhận xóa đề thi</h3>
                    <p class="text-sm text-gray-500 mt-2">
                        Bạn có chắc chắn muốn xóa đề thi <span id="deleteExamTitle" class="font-bold text-gray-800"></span> (<span id="deleteExamCode" class="font-mono font-semibold text-blue-600"></span>)?
                    </p>
                    <p class="text-xs text-rose-600 bg-rose-50 p-2.5 rounded-lg border border-rose-100 mt-3 text-left">
                        ⚠️ Cảnh báo: Thao tác này sẽ xóa toàn bộ câu hỏi trong đề thi, lượt làm bài và kết quả thi của sinh viên thuộc đề này. Hành động này không thể hoàn tác!
                    </p>
                </div>
                <form id="deleteExamForm" method="POST" class="pt-3 flex gap-3">
                    @csrf
                    @method('DELETE')
                    <button type="button" onclick="closeDeleteModal()" class="flex-1 px-4 py-2.5 bg-gray-100 text-gray-700 font-medium text-sm rounded-xl hover:bg-gray-200 transition-colors">
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
        function confirmDeleteExam(deleteUrl, examTitle, examCode) {
            document.getElementById('deleteExamForm').action = deleteUrl;
            document.getElementById('deleteExamTitle').textContent = `"${examTitle}"`;
            document.getElementById('deleteExamCode').textContent = examCode;
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }
    </script>
@endsection
