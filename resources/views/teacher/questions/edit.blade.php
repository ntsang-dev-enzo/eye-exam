@extends('layouts.teacher')

@section('title', 'Sửa Câu Hỏi')

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">
        <div class="flex items-center gap-2 mb-2">
            <a href="{{ route('teacher.questions.index') }}" class="text-sm font-medium text-gray-500 hover:text-blue-600 transition-colors">Ngân hàng câu hỏi</a>
            <span class="text-gray-400">/</span>
            <span class="text-sm font-medium text-gray-900">Sửa câu hỏi</span>
        </div>

        <form action="{{ route('teacher.questions.update', $question) }}" method="POST" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 space-y-6">
            @csrf
            @method('PUT')

            @if($errors->any())
                <div class="bg-red-50 text-red-700 p-4 rounded-xl text-sm border border-red-100">
                    <ul class="list-disc pl-5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Subject -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Môn học <span class="text-red-500">*</span></label>
                    <select name="subject_id" required class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 px-4 py-2.5 bg-gray-50 text-sm">
                        <option value="">-- Chọn môn học --</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ old('subject_id', $question->subject_id) == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Loại câu hỏi <span class="text-red-500">*</span></label>
                    <select name="type" id="questionType" required class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 px-4 py-2.5 bg-gray-50 text-sm">
                        <option value="multiple_choice" {{ old('type', $question->type) == 'multiple_choice' ? 'selected' : '' }}>Trắc nghiệm</option>
                        <option value="essay" {{ old('type', $question->type) == 'essay' ? 'selected' : '' }}>Tự luận</option>
                    </select>
                </div>
            </div>

            <!-- Content -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nội dung câu hỏi <span class="text-red-500">*</span></label>
                <textarea name="content" rows="4" required class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 px-4 py-3 bg-gray-50 text-sm" placeholder="Nhập câu hỏi tại đây...">{{ old('content', $question->content) }}</textarea>
            </div>

            <!-- Difficulty -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Độ khó <span class="text-red-500">*</span></label>
                <div class="flex items-center gap-6">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="difficulty" value="easy" class="text-emerald-600 focus:ring-emerald-500 w-4 h-4" {{ old('difficulty', $question->difficulty) == 'easy' ? 'checked' : '' }}>
                        <span class="text-sm font-medium text-gray-700">Dễ</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="difficulty" value="medium" class="text-amber-600 focus:ring-amber-500 w-4 h-4" {{ old('difficulty', $question->difficulty) == 'medium' ? 'checked' : '' }}>
                        <span class="text-sm font-medium text-gray-700">Trung bình</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="difficulty" value="hard" class="text-rose-600 focus:ring-rose-500 w-4 h-4" {{ old('difficulty', $question->difficulty) == 'hard' ? 'checked' : '' }}>
                        <span class="text-sm font-medium text-gray-700">Khó</span>
                    </label>
                </div>
            </div>

            <!-- Answers (for multiple choice) -->
            <div id="multipleChoiceSection" class="p-6 bg-gray-50 rounded-xl border border-gray-200" style="{{ old('type', $question->type) === 'essay' ? 'display: none;' : '' }}">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="font-medium text-gray-800">Các đáp án</h4>
                    <span class="text-xs text-gray-500">Chọn radio để đánh dấu đáp án đúng</span>
                </div>
                
                <div id="answersList" class="space-y-4">
                    @php
                        $oldAnswers = old('answers');
                        $oldCorrect = old('correct_answer');
                        
                        if ($oldAnswers === null) {
                            $answers = $question->answers;
                            $correctIndex = $answers->search(fn($a) => $a->is_correct);
                        } else {
                            $answers = collect($oldAnswers)->map(fn($content) => (object)['content' => $content]);
                            $correctIndex = $oldCorrect;
                        }
                        
                        // Ensure at least 4 answers fields
                        $answersCount = max(count($answers), 4);
                    @endphp

                    @for($i = 0; $i < $answersCount; $i++)
                        <div class="flex items-start gap-4 answer-row">
                            <div class="pt-3">
                                <input type="radio" name="correct_answer" value="{{ $i }}" class="w-5 h-5 text-blue-600 focus:ring-blue-500 cursor-pointer" {{ (string)$correctIndex === (string)$i ? 'checked' : '' }}>
                            </div>
                            <div class="flex-1 flex gap-2">
                                <span class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-gray-200 font-bold text-gray-600 answer-label">{{ chr(65 + $i) }}</span>
                                <input type="text" name="answers[]" class="flex-1 rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 px-4 py-2 text-sm" placeholder="Nhập đáp án..." value="{{ isset($answers[$i]) ? $answers[$i]->content : '' }}">
                            </div>
                            @if($i > 1)
                                <button type="button" class="mt-1 p-2 text-red-500 hover:bg-red-50 rounded-lg remove-answer" onclick="this.closest('.answer-row').remove(); updateLabels();">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            @else
                                <div class="w-9"></div>
                            @endif
                        </div>
                    @endfor
                </div>

                <button type="button" id="addAnswerBtn" class="mt-4 inline-flex items-center px-3 py-2 text-sm font-medium text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition-colors">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Thêm đáp án
                </button>
            </div>

            <!-- Explanation -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Lời giải / Giải thích (Tùy chọn)</label>
                <textarea name="explanation" rows="2" class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 px-4 py-3 bg-gray-50 text-sm" placeholder="Giải thích đáp án cho học viên...">{{ old('explanation', $question->explanation) }}</textarea>
            </div>

            <div class="pt-4 border-t border-gray-100 flex justify-end gap-3">
                <a href="{{ route('teacher.questions.index') }}" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors">
                    Hủy bỏ
                </a>
                <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 transition-colors shadow-sm">
                    Cập nhật câu hỏi
                </button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const typeSelect = document.getElementById('questionType');
            const mcSection = document.getElementById('multipleChoiceSection');
            const addAnswerBtn = document.getElementById('addAnswerBtn');
            const answersList = document.getElementById('answersList');

            // Toggle MC section based on type
            function toggleMC() {
                if (typeSelect.value === 'multiple_choice') {
                    mcSection.style.display = 'block';
                } else {
                    mcSection.style.display = 'none';
                }
            }

            typeSelect.addEventListener('change', toggleMC);
            // Already toggled by inline style, but bind event for changes
            
            // Update labels A, B, C, D...
            window.updateLabels = function() {
                const rows = answersList.querySelectorAll('.answer-row');
                rows.forEach((row, index) => {
                    const labelSpan = row.querySelector('.answer-label');
                    const radioInput = row.querySelector('input[type="radio"]');
                    if(labelSpan) labelSpan.textContent = String.fromCharCode(65 + index);
                    if(radioInput) radioInput.value = index;
                });
            };

            // Add answer row
            addAnswerBtn.addEventListener('click', function() {
                const rowCount = answersList.querySelectorAll('.answer-row').length;
                if (rowCount >= 6) {
                    alert('Tối đa 6 đáp án');
                    return;
                }

                const newRow = document.createElement('div');
                newRow.className = 'flex items-start gap-4 answer-row';
                newRow.innerHTML = `
                    <div class="pt-3">
                        <input type="radio" name="correct_answer" value="${rowCount}" class="w-5 h-5 text-blue-600 focus:ring-blue-500 cursor-pointer">
                    </div>
                    <div class="flex-1 flex gap-2">
                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-gray-200 font-bold text-gray-600 answer-label">${String.fromCharCode(65 + rowCount)}</span>
                        <input type="text" name="answers[]" class="flex-1 rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 px-4 py-2 text-sm" placeholder="Nhập đáp án...">
                    </div>
                    <button type="button" class="mt-1 p-2 text-red-500 hover:bg-red-50 rounded-lg remove-answer" onclick="this.closest('.answer-row').remove(); updateLabels();">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                `;
                answersList.appendChild(newRow);
            });
        });
    </script>
@endsection
