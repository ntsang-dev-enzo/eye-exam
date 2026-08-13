@extends('layouts.teacher')

@section('title', 'Sửa Đề Thi')

@section('content')
    <div class="max-w-5xl mx-auto pb-12">
        <form id="examForm" action="{{ route('teacher.exams.update', $exam) }}" method="POST" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 space-y-8 relative">
            @csrf
            @method('PUT')

            <!-- 1. Thông tin chung -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 border-b border-gray-100 pb-3 mb-5">1. Thông tin chung</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Môn học <span class="text-red-500">*</span></label>
                        <select name="subject_id" id="subject_id" required class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 px-4 py-2.5 bg-gray-50 text-sm">
                            <option value="">-- Chọn môn học --</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ $exam->subject_id == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tên đề thi <span class="text-red-500">*</span></label>
                        <input type="text" name="title" value="{{ old('title', $exam->title) }}" required class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 px-4 py-2.5 bg-gray-50 text-sm" placeholder="VD: Giữa kỳ Toán Rời Rạc">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Mô tả thêm (Tùy chọn)</label>
                        <textarea name="description" rows="2" class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 px-4 py-2.5 bg-gray-50 text-sm" placeholder="Hướng dẫn làm bài...">{{ old('description', $exam->description) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Thời gian làm bài (Phút) <span class="text-red-500">*</span></label>
                        <input type="number" name="duration_minutes" value="{{ old('duration_minutes', $exam->duration_minutes) }}" min="1" required class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 px-4 py-2.5 bg-gray-50 text-sm">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Số lần thi tối đa <span class="text-red-500">*</span></label>
                        <input type="number" name="max_attempts" value="{{ old('max_attempts', $exam->max_attempts ?? 1) }}" min="1" required class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 px-4 py-2.5 bg-gray-50 text-sm">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Thời gian bắt đầu (Tùy chọn)</label>
                        <input type="datetime-local" name="start_at" value="{{ old('start_at', $exam->start_at ? $exam->start_at->format('Y-m-d\TH:i') : '') }}" class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 px-4 py-2.5 bg-gray-50 text-sm">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Thời gian kết thúc (Tùy chọn)</label>
                        <input type="datetime-local" name="end_at" value="{{ old('end_at', $exam->end_at ? $exam->end_at->format('Y-m-d\TH:i') : '') }}" class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 px-4 py-2.5 bg-gray-50 text-sm">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Trạng thái <span class="text-red-500">*</span></label>
                        <select name="status" class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 px-4 py-2.5 bg-gray-50 text-sm">
                            <option value="published" {{ old('status', $exam->status) === 'published' ? 'selected' : '' }}>Mở</option>
                            <option value="closed" {{ old('status', $exam->status) === 'closed' ? 'selected' : '' }}>Đóng</option>
                        </select>
                    </div>
                    
                    <div class="flex flex-col justify-center gap-3 mt-4">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="shuffle_questions" value="1" class="text-blue-600 focus:ring-blue-500 rounded text-sm w-4 h-4" {{ old('shuffle_questions', $exam->shuffle_questions) ? 'checked' : '' }}>
                            <span class="text-sm text-gray-700 font-medium">Trộn thứ tự câu hỏi</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="shuffle_answers" value="1" class="text-blue-600 focus:ring-blue-500 rounded text-sm w-4 h-4" {{ old('shuffle_answers', $exam->shuffle_answers) ? 'checked' : '' }}>
                            <span class="text-sm text-gray-700 font-medium">Trộn thứ tự đáp án (Trắc nghiệm)</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="allow_review" value="1" class="text-blue-600 focus:ring-blue-500 rounded text-sm w-4 h-4" {{ old('allow_review', $exam->allow_review) ? 'checked' : '' }}>
                            <span class="text-sm text-gray-700 font-medium">Cho phép sinh viên xem lại bài thi</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- 2. Thiết lập câu hỏi -->
            <div>
                <div class="flex items-center justify-between border-b border-gray-100 pb-3 mb-5">
                    <h3 class="text-lg font-semibold text-gray-900">2. Thiết lập câu hỏi</h3>
                    <div class="flex gap-2">
                        <button type="button" id="btnOpenBankModal" class="px-4 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-sm font-medium rounded-lg transition-colors flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            Chọn từ ngân hàng
                        </button>
                        <button type="button" id="btnOpenManualModal" class="px-4 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-sm font-medium rounded-lg transition-colors flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Tạo câu hỏi thủ công
                        </button>
                    </div>
                </div>

                <!-- Danh sách câu hỏi đã chọn -->
                <div id="selectedQuestionsContainer" class="hidden">
                    <div class="overflow-x-auto border border-gray-200 rounded-xl mb-4">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    <th class="px-4 py-3 border-b w-12 text-center">STT</th>
                                    <th class="px-4 py-3 border-b">Nội dung</th>
                                    <th class="px-4 py-3 border-b w-24">Loại</th>
                                    <th class="px-4 py-3 border-b w-28">Điểm</th>
                                    <th class="px-4 py-3 border-b w-16 text-center">Xóa</th>
                                </tr>
                            </thead>
                            <tbody id="selectedQuestionsList" class="divide-y divide-gray-100 bg-white">
                                <!-- Các câu hỏi sẽ được append bằng JS -->
                            </tbody>
                        </table>
                    </div>
                    <div class="flex justify-between items-center text-sm font-medium text-gray-700">
                        <span>Tổng số câu: <span id="totalQuestionsCount" class="text-blue-600">0</span></span>
                        <span>Tổng điểm: <span id="totalPointsCount" class="text-emerald-600">0</span></span>
                    </div>
                </div>

                <!-- Empty State -->
                <div class="bg-gray-50 border border-dashed border-gray-300 rounded-xl p-8 text-center" id="emptyQuestions">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Đề thi chưa có câu hỏi nào</h3>
                    <p class="mt-1 text-sm text-gray-500">Hãy bắt đầu bằng việc chọn từ ngân hàng đề hoặc tạo câu hỏi mới (Trắc nghiệm/Tự luận).</p>
                </div>
            </div>

            <!-- Submit -->
            <div class="pt-4 flex justify-end gap-3 border-t border-gray-100">
                <a href="{{ route('teacher.exams.index') }}" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors">
                    Hủy bỏ
                </a>
                <button type="button" id="btnSubmitForm" class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 transition-colors shadow-sm">
                    Cập nhật đề thi
                </button>
            </div>
        </form>
    </div>

    <!-- Modal: Chọn từ Ngân hàng -->
    <div id="bankModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" onclick="closeModal('bankModal')"></div>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="relative bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-4xl w-full flex flex-col max-h-[90vh]">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                    <h3 class="text-lg font-bold text-gray-900">Chọn câu hỏi từ Ngân hàng</h3>
                    <button type="button" onclick="closeModal('bankModal')" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <div class="p-6 overflow-y-auto flex-1">
                    <div id="bankLoading" class="text-center py-8 hidden">
                        <svg class="animate-spin h-8 w-8 text-blue-600 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <p class="mt-2 text-sm text-gray-500">Đang tải câu hỏi...</p>
                    </div>
                    
                    <div id="bankContent" class="space-y-4">
                        <p class="text-sm text-amber-600 bg-amber-50 p-3 rounded-lg border border-amber-100" id="bankWarning">Vui lòng chọn Môn học ở form bên ngoài trước khi chọn câu hỏi từ ngân hàng.</p>
                        
                        <div class="overflow-x-auto border border-gray-200 rounded-lg hidden" id="bankTableContainer">
                            <table class="w-full text-left border-collapse">
                                <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                                    <tr>
                                        <th class="px-4 py-3 w-12 text-center">
                                            <input type="checkbox" id="selectAllBank" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        </th>
                                        <th class="px-4 py-3">Nội dung</th>
                                        <th class="px-4 py-3 w-24 text-center">Loại</th>
                                        <th class="px-4 py-3 w-24 text-center">Độ khó</th>
                                    </tr>
                                </thead>
                                <tbody id="bankQuestionsList" class="divide-y divide-gray-100">
                                    <!-- Populated by JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex justify-end gap-3">
                    <button type="button" onclick="closeModal('bankModal')" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">Đóng</button>
                    <button type="button" id="btnAddSelectedQuestions" class="px-4 py-2 bg-blue-600 rounded-lg text-sm font-medium text-white hover:bg-blue-700 disabled:opacity-50">Thêm vào Đề thi</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Tạo thủ công -->
    <div id="manualModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" onclick="closeModal('manualModal')"></div>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="relative bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-3xl w-full flex flex-col max-h-[90vh]">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                    <h3 class="text-lg font-bold text-gray-900">Tạo câu hỏi thủ công</h3>
                    <button type="button" onclick="closeModal('manualModal')" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <div class="p-6 overflow-y-auto flex-1">
                    <form id="manualQuestionForm" class="space-y-6">
                        <!-- Type & Difficulty -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Loại câu hỏi</label>
                                <select name="type" id="manualType" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                                    <option value="multiple_choice">Trắc nghiệm</option>
                                    <option value="essay">Tự luận</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Độ khó</label>
                                <select name="difficulty" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                                    <option value="easy">Dễ</option>
                                    <option value="medium" selected>Trung bình</option>
                                    <option value="hard">Khó</option>
                                </select>
                            </div>
                        </div>

                        <!-- Content -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nội dung</label>
                            <textarea name="content" required rows="3" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm" placeholder="Nhập câu hỏi..."></textarea>
                        </div>

                        <!-- Answers (MC only) -->
                        <div id="manualAnswersSection" class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <label class="block text-sm font-medium text-gray-700 mb-3">Đáp án (Chọn radio cho đáp án đúng)</label>
                            <div id="manualAnswersList" class="space-y-3">
                                <!-- Default 4 answers -->
                                @for($i = 0; $i < 4; $i++)
                                    <div class="flex items-center gap-3">
                                        <input type="radio" name="correct_answer" value="{{ $i }}" {{ $i == 0 ? 'checked' : '' }} class="w-4 h-4 text-emerald-600 focus:ring-emerald-500">
                                        <span class="text-sm font-bold w-6">{{ chr(65 + $i) }}</span>
                                        <input type="text" name="answers[]" class="flex-1 rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm py-1.5" placeholder="Nhập đáp án...">
                                    </div>
                                @endfor
                            </div>
                        </div>
                    </form>
                </div>
                
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex justify-end gap-3">
                    <button type="button" onclick="closeModal('manualModal')" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">Hủy</button>
                    <button type="button" id="btnSaveManualQuestion" class="px-4 py-2 bg-emerald-600 rounded-lg text-sm font-medium text-white hover:bg-emerald-700 relative">
                        <span id="btnSaveManualText">Lưu & Thêm vào đề</span>
                        <svg id="btnSaveManualSpinner" class="animate-spin h-5 w-5 absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript cho xử lý Đề thi -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        // State initialized with existing questions
        let selectedQuestions = [
            @foreach($exam->questions as $q)
            {
                id: {{ $q->id }},
                content: {!! json_encode($q->content) !!},
                type: '{{ $q->type }}',
                point: {{ $q->pivot->points }}
            },
            @endforeach
        ];
        
        let bankQuestions = [];
        
        // Elements
        const subjectSelect = document.getElementById('subject_id');
        const emptyState = document.getElementById('emptyQuestions');
        const container = document.getElementById('selectedQuestionsContainer');
        const tbody = document.getElementById('selectedQuestionsList');
        const totalQ = document.getElementById('totalQuestionsCount');
        const totalP = document.getElementById('totalPointsCount');
        const mainForm = document.getElementById('examForm');

        // Initial render
        renderSelectedQuestions();

        // Render UI
        function renderSelectedQuestions() {
            if (selectedQuestions.length === 0) {
                emptyState.classList.remove('hidden');
                container.classList.add('hidden');
                totalQ.textContent = 0;
                totalP.textContent = 0;
                return;
            }

            emptyState.classList.add('hidden');
            container.classList.remove('hidden');
            
            tbody.innerHTML = '';
            let tPoints = 0;

            selectedQuestions.forEach((q, index) => {
                tPoints += parseFloat(q.point || 1);
                
                const typeBadge = q.type === 'multiple_choice' 
                    ? '<span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-50 text-indigo-700">Trắc nghiệm</span>'
                    : '<span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-50 text-purple-700">Tự luận</span>';

                const tr = document.createElement('tr');
                tr.className = 'border-b border-gray-50';
                tr.innerHTML = `
                    <td class="px-4 py-3 text-center text-sm font-medium text-gray-500">${index + 1}</td>
                    <td class="px-4 py-3 text-sm text-gray-900 line-clamp-1" title="${q.content}">${q.content}</td>
                    <td class="px-4 py-3 text-center">${typeBadge}</td>
                    <td class="px-4 py-3">
                        <input type="number" step="0.25" min="0" value="${q.point || 1}" class="w-full text-sm border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500 point-input" data-idx="${index}">
                    </td>
                    <td class="px-4 py-3 text-center">
                        <button type="button" class="text-red-400 hover:text-red-600 btn-remove-q" data-idx="${index}">
                            <svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </td>
                `;
                tbody.appendChild(tr);
            });

            totalQ.textContent = selectedQuestions.length;
            totalP.textContent = tPoints.toFixed(2);

            // Bind events for dynamically added elements
            document.querySelectorAll('.btn-remove-q').forEach(btn => {
                btn.addEventListener('click', function() {
                    selectedQuestions.splice(this.getAttribute('data-idx'), 1);
                    renderSelectedQuestions();
                });
            });

            document.querySelectorAll('.point-input').forEach(inp => {
                inp.addEventListener('change', function() {
                    selectedQuestions[this.getAttribute('data-idx')].point = parseFloat(this.value) || 0;
                    renderSelectedQuestions(); // Re-render to update total points
                });
            });
        }

        // Modal Logic
        function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
        function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

        // Bank Modal Logic
        document.getElementById('btnOpenBankModal').addEventListener('click', () => {
            const subjId = subjectSelect.value;
            if (!subjId) {
                document.getElementById('bankWarning').classList.remove('hidden');
                document.getElementById('bankTableContainer').classList.add('hidden');
            } else {
                document.getElementById('bankWarning').classList.add('hidden');
                document.getElementById('bankTableContainer').classList.add('hidden');
                document.getElementById('bankLoading').classList.remove('hidden');
                
                // Fetch API
                axios.get(`{{ route('teacher.api.questions.index') }}?subject_id=${subjId}`)
                    .then(res => {
                        document.getElementById('bankLoading').classList.add('hidden');
                        document.getElementById('bankTableContainer').classList.remove('hidden');
                        
                        const list = document.getElementById('bankQuestionsList');
                        list.innerHTML = '';
                        bankQuestions = res.data;
                        
                        if(bankQuestions.length === 0) {
                            list.innerHTML = '<tr><td colspan="4" class="px-4 py-8 text-center text-gray-500">Ngân hàng chưa có câu hỏi nào cho môn này.</td></tr>';
                            return;
                        }

                        bankQuestions.forEach(q => {
                            // Check if already selected
                            const isSelected = selectedQuestions.some(sq => sq.id === q.id);
                            
                            const typeBadge = q.type === 'multiple_choice' ? 'Trắc nghiệm' : 'Tự luận';
                            const diffBadge = q.difficulty === 'easy' ? 'Dễ' : (q.difficulty === 'medium' ? 'TB' : 'Khó');

                            const tr = document.createElement('tr');
                            tr.className = isSelected ? 'bg-blue-50/50' : 'hover:bg-gray-50 cursor-pointer';
                            tr.innerHTML = `
                                <td class="px-4 py-3 text-center">
                                    <input type="checkbox" value="${q.id}" class="bank-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500" ${isSelected ? 'checked disabled' : ''}>
                                </td>
                                <td class="px-4 py-3 text-sm line-clamp-2">${q.content}</td>
                                <td class="px-4 py-3 text-center text-xs">${typeBadge}</td>
                                <td class="px-4 py-3 text-center text-xs">${diffBadge}</td>
                            `;
                            // Allow clicking row to check
                            if(!isSelected) {
                                tr.addEventListener('click', function(e) {
                                    if(e.target.type !== 'checkbox') {
                                        const cb = this.querySelector('input[type="checkbox"]');
                                        cb.checked = !cb.checked;
                                    }
                                });
                            }
                            list.appendChild(tr);
                        });
                    })
                    .catch(err => {
                        console.error(err);
                        alert('Lỗi tải ngân hàng câu hỏi');
                    });
            }
            openModal('bankModal');
        });

        document.getElementById('selectAllBank').addEventListener('change', function() {
            document.querySelectorAll('.bank-checkbox:not(:disabled)').forEach(cb => {
                cb.checked = this.checked;
            });
        });

        document.getElementById('btnAddSelectedQuestions').addEventListener('click', () => {
            const checkedBoxes = document.querySelectorAll('.bank-checkbox:checked:not(:disabled)');
            checkedBoxes.forEach(cb => {
                const qId = parseInt(cb.value);
                const qData = bankQuestions.find(q => q.id === qId);
                if(qData) {
                    selectedQuestions.push({
                        id: qData.id,
                        content: qData.content,
                        type: qData.type,
                        point: 1 // default point
                    });
                }
            });
            renderSelectedQuestions();
            closeModal('bankModal');
        });

        // Manual Modal Logic
        document.getElementById('btnOpenManualModal').addEventListener('click', () => {
            if (!subjectSelect.value) {
                alert('Vui lòng chọn môn học trước khi tạo câu hỏi.');
                return;
            }
            document.getElementById('manualQuestionForm').reset();
            document.getElementById('manualAnswersSection').style.display = 'block';
            openModal('manualModal');
        });

        document.getElementById('manualType').addEventListener('change', function() {
            document.getElementById('manualAnswersSection').style.display = this.value === 'multiple_choice' ? 'block' : 'none';
        });

        document.getElementById('btnSaveManualQuestion').addEventListener('click', function() {
            const form = document.getElementById('manualQuestionForm');
            if(!form.reportValidity()) return;

            const subjId = subjectSelect.value;
            const formData = new FormData(form);
            formData.append('subject_id', subjId);

            // UI Feedback
            this.disabled = true;
            document.getElementById('btnSaveManualText').classList.add('opacity-0');
            document.getElementById('btnSaveManualSpinner').classList.remove('hidden');

            axios.post('{{ route('teacher.api.questions.store') }}', formData)
                .then(res => {
                    if(res.data.success) {
                        const newQ = res.data.question;
                        selectedQuestions.push({
                            id: newQ.id,
                            content: newQ.content,
                            type: newQ.type,
                            point: 1
                        });
                        renderSelectedQuestions();
                        closeModal('manualModal');
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Lỗi tạo câu hỏi. Vui lòng kiểm tra lại dữ liệu nhập.');
                })
                .finally(() => {
                    this.disabled = false;
                    document.getElementById('btnSaveManualText').classList.remove('opacity-0');
                    document.getElementById('btnSaveManualSpinner').classList.add('hidden');
                });
        });

        // Submit Form Final Logic
        document.getElementById('btnSubmitForm').addEventListener('click', function() {
            if(!mainForm.reportValidity()) return;
            
            if(selectedQuestions.length === 0) {
                alert('Vui lòng thêm ít nhất một câu hỏi vào đề thi!');
                return;
            }

            // Append hidden inputs for questions and points
            selectedQuestions.forEach(q => {
                const inpQ = document.createElement('input');
                inpQ.type = 'hidden';
                inpQ.name = 'questions[]';
                inpQ.value = q.id;
                
                const inpP = document.createElement('input');
                inpP.type = 'hidden';
                inpP.name = `points[${q.id}]`;
                inpP.value = q.point;

                mainForm.appendChild(inpQ);
                mainForm.appendChild(inpP);
            });

            mainForm.submit();
        });
    </script>
@endsection
