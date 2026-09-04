<!DOCTYPE html>
<html lang="vi" class="bg-slate-50 h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Làm bài thi: {{ $exam->title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="{{ asset('vendor/face-api/face-api.min.js') }}"></script>
    <style>
        .no-select {
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }
        @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        .toast-animate {
            animation: slideInRight 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
    </style>
</head>
<body class="h-full font-sans text-slate-800 antialiased overflow-hidden flex flex-col no-select">

    <!-- Toast Container (Top Right) -->
    <div id="toastContainer" class="fixed top-5 right-5 z-[99999] flex flex-col gap-2.5 pointer-events-none max-w-sm w-full"></div>

    <!-- Offline Alert Banner (Fixed Top) -->
    <div id="offlineBanner" class="hidden bg-rose-600 text-white px-6 py-2.5 text-xs font-bold flex items-center justify-between shadow-lg z-50 animate-pulse">
        <div class="flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0l-2.829-2.829m-4.243 4.243a9 9 0 01-12.728 0m0 0l2.829-2.829m-2.829 2.829L3 21m2.828-12.536a5 5 0 017.072 0m0 0l2.829 2.829"></path></svg>
            <span>Mất kết nối Internet. Hệ thống đang tự động lưu bài vào bộ nhớ thiết bị và sẽ đồng bộ khi có mạng lại.</span>
        </div>
        <span class="bg-rose-700 px-2 py-0.5 rounded text-[10px] uppercase font-bold">Offline</span>
    </div>

    <!-- Header -->
    <header class="bg-white border-b border-slate-200 px-6 py-3 flex items-center justify-between shrink-0 shadow-sm z-10 relative">
        <div class="flex items-center gap-4">
            <div class="bg-indigo-600 text-white p-2 rounded-xl shadow-sm">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
            </div>
            <div>
                <h1 class="font-bold text-lg leading-tight text-slate-900">{{ $exam->title }}</h1>
                <div class="flex items-center gap-3 text-xs text-slate-500 font-medium">
                    <span class="uppercase tracking-wider font-semibold text-indigo-600">{{ $exam->subject->name }}</span>
                    <span>•</span>
                    <!-- Auto-save status -->
                    <span id="saveStatusIndicator" class="flex items-center gap-1 text-emerald-600 font-medium">
                        <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span>Đã lưu đáp án</span>
                    </span>
                    <span>•</span>
                    <!-- Network status -->
                    <span id="networkStatusBadge" class="flex items-center gap-1 text-emerald-600 font-medium">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span>Trực tuyến</span>
                    </span>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-5">
            <!-- Quick Tools: Scientific Calculator & Personal Note -->
            <div class="flex items-center gap-2 bg-slate-100/90 p-1 rounded-2xl border border-slate-200">
                <!-- Scientific Calculator Button -->
                <button type="button" onclick="toggleCalculator()" id="btnHeaderCalc"
                    class="px-3 py-1.5 rounded-xl bg-white hover:bg-indigo-50 border border-slate-200 hover:border-indigo-300 text-slate-700 hover:text-indigo-700 font-bold text-xs flex items-center gap-1.5 shadow-xs transition-all active:scale-95"
                    title="Mở máy tính khoa học">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                    <span>Máy tính</span>
                </button>

                <!-- Personal Note Button -->
                <button type="button" onclick="toggleNote()" id="btnHeaderNote"
                    class="px-3 py-1.5 rounded-xl bg-white hover:bg-amber-50 border border-slate-200 hover:border-amber-300 text-slate-700 hover:text-amber-800 font-bold text-xs flex items-center gap-1.5 shadow-xs transition-all active:scale-95"
                    title="Mở nháp & ghi chú cá nhân">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    <span>Ghi chú nháp</span>
                </button>
            </div>

            <!-- Timer -->
            <div class="flex flex-col items-end">
                <span class="text-xs text-slate-500 font-medium">Thời gian còn lại</span>
                <div id="countdown" class="text-2xl font-black text-indigo-600 tracking-tight" data-time="{{ $timeRemaining }}">
                    00:00:00
                </div>
            </div>
            
            <button type="button" onclick="confirmSubmit()" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-md transition-all active:scale-95 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Nộp bài
            </button>
        </div>
    </header>

    <!-- Main Content Area -->
    <div class="flex-1 flex overflow-hidden">
        
        <!-- Questions Area (Left) -->
        <div class="flex-1 overflow-y-auto scroll-smooth bg-slate-50 p-6 md:p-10 relative flex flex-col justify-between" id="questionsArea">
            <form id="examForm" action="{{ route('student.exams.submit', $exam) }}" method="POST" class="max-w-4xl mx-auto w-full space-y-8 pb-10">
                @csrf
                
                @php 
                    $questions = $exam->questions;
                    if($exam->shuffle_questions) $questions = $questions->shuffle(); 
                    $questionChunks = $questions->chunk(5);
                    $totalPages = $questionChunks->count();
                @endphp

                <!-- Loop chunks of 5 questions -->
                @foreach($questionChunks as $chunkIndex => $chunk)
                    <div class="question-page space-y-8 {{ $chunkIndex === 0 ? '' : 'hidden' }}" id="page-{{ $chunkIndex + 1 }}" data-page="{{ $chunkIndex + 1 }}">
                        @foreach($chunk as $chunkItemIndex => $question)
                            @php
                                $globalIndex = ($chunkIndex * 5) + $chunkItemIndex + 1;
                                $savedAnswer = $savedAnswers->get($question->id);
                                $savedAnswerId = $savedAnswer ? $savedAnswer->answer_id : null;
                            @endphp
                            <div id="q-{{ $globalIndex }}" class="bg-white rounded-3xl p-8 shadow-sm border border-slate-200 question-block transition-all duration-300">
                                <div class="flex items-start gap-4">
                                    <!-- Question Number Badge -->
                                    <div class="shrink-0 flex flex-col items-center justify-center w-12 h-12 bg-indigo-50 rounded-2xl border border-indigo-100 text-indigo-600 font-black text-lg">
                                        {{ $globalIndex }}
                                    </div>
                                    
                                    <div class="flex-1 pt-1">
                                        <!-- Category Badge if available -->
                                        @if($question->category)
                                            <div class="mb-2">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-50 text-purple-700 border border-purple-100">
                                                    {{ $question->category->name }}
                                                </span>
                                            </div>
                                        @endif

                                        <!-- Question Content -->
                                        <div class="text-lg text-slate-800 font-medium leading-relaxed mb-6 no-select">
                                            {!! nl2br(e($question->content)) !!}
                                            <span class="text-sm font-normal text-slate-400 ml-2">({{ $question->pivot->points ?? 1 }} điểm)</span>
                                        </div>

                                        <!-- Answers -->
                                        @if($question->type === 'multiple_choice')
                                            @php 
                                                $answers = $question->answers;
                                                if($exam->shuffle_answers) $answers = $answers->shuffle();
                                            @endphp
                                            <div class="space-y-3">
                                                @foreach($answers as $ansIndex => $answer)
                                                    <label class="flex items-start p-4 rounded-xl border border-slate-200 hover:border-indigo-400 hover:bg-indigo-50/50 cursor-pointer transition-colors group has-[:checked]:border-indigo-600 has-[:checked]:bg-indigo-50 has-[:checked]:ring-1 has-[:checked]:ring-indigo-600">
                                                        <div class="flex items-center h-6">
                                                            <input type="radio" name="answers[{{ $question->id }}]" value="{{ $answer->id }}" 
                                                                class="w-5 h-5 text-indigo-600 border-slate-300 focus:ring-indigo-600 cursor-pointer"
                                                                {{ (string)$savedAnswerId === (string)$answer->id ? 'checked' : '' }}
                                                                onchange="handleAnswerSelect({{ $globalIndex }}, {{ $question->id }}, {{ $answer->id }})">
                                                        </div>
                                                        <div class="ml-3">
                                                            <span class="block text-slate-700 font-medium group-hover:text-indigo-900 group-has-[:checked]:text-indigo-900 leading-relaxed">{{ $answer->content }}</span>
                                                        </div>
                                                    </label>
                                                @endforeach
                                            </div>
                                        @else
                                            <div>
                                                <textarea name="answers[{{ $question->id }}]" rows="6" 
                                                    class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm text-slate-800 placeholder:text-slate-400 p-4" 
                                                    placeholder="Nhập câu trả lời của bạn..."
                                                    oninput="handleEssayInput({{ $globalIndex }}, {{ $question->id }}, this.value)"></textarea>
                                            </div>
                                        @endif

                                        <!-- Question Footer with Clear Answer Button -->
                                        <div class="flex items-center justify-between mt-5 pt-3 border-t border-slate-100 flex-wrap gap-2">
                                            <span class="text-xs text-slate-400 font-medium">Câu {{ $globalIndex }} / {{ count($questions) }}</span>
                                            <button type="button" onclick="clearQuestionAnswer({{ $globalIndex }}, {{ $question->id }})" 
                                                class="text-xs font-semibold text-slate-500 hover:text-rose-600 hover:bg-rose-50 px-2.5 py-1.5 rounded-xl border border-slate-200 hover:border-rose-200 transition-all flex items-center gap-1.5 shadow-sm active:scale-95">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                Xóa đáp án câu này
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </form>

            <!-- Bottom Pagination Control Bar (5 questions per page) -->
            <div class="max-w-4xl mx-auto w-full pt-4 pb-8 flex items-center justify-between border-t border-slate-200">
                <button type="button" id="btnPrevPage" onclick="changePage(currentPage - 1)" class="px-5 py-2.5 bg-white border border-slate-200 rounded-xl text-sm font-semibold text-slate-700 hover:bg-slate-100 disabled:opacity-40 disabled:cursor-not-allowed transition-colors flex items-center gap-2 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    Trang trước
                </button>

                <div class="flex items-center gap-2">
                    <span class="text-xs text-slate-500 font-medium">Trang <span id="currentPageNum" class="font-bold text-slate-800">1</span> / {{ $totalPages }}</span>
                    <span class="text-xs text-slate-400 font-normal">(5 câu / trang)</span>
                </div>

                <button type="button" id="btnNextPage" onclick="changePage(currentPage + 1)" class="px-5 py-2.5 bg-white border border-slate-200 rounded-xl text-sm font-semibold text-slate-700 hover:bg-slate-100 disabled:opacity-40 disabled:cursor-not-allowed transition-colors flex items-center gap-2 shadow-sm">
                    Trang sau
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </button>
            </div>
        </div>

        <!-- Navigator Sidebar (Right) -->
        <div class="w-80 bg-white border-l border-slate-200 flex flex-col shrink-0 shadow-[-4px_0_15px_-3px_rgba(0,0,0,0.05)] z-20 hidden md:flex">
            <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                <h3 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    Bảng câu hỏi
                </h3>
                
                <div class="flex items-center justify-between text-sm">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-indigo-600"></div>
                        <span class="text-slate-600 font-medium">Đã làm (<span id="answeredCount">0</span>)</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-slate-200 border border-slate-300"></div>
                        <span class="text-slate-600 font-medium">Chưa làm (<span id="unansweredCount">{{ count($questions) }}</span>)</span>
                    </div>
                </div>
            </div>
            
            <div class="flex-1 overflow-y-auto p-6">
                <div class="grid grid-cols-5 gap-3" id="navigatorGrid">
                    @foreach($questions as $index => $question)
                        @php
                            $isPreAnswered = $savedAnswers->has($question->id) && $savedAnswers[$question->id]->answer_id !== null;
                        @endphp
                        <button type="button" id="nav-{{ $index + 1 }}" onclick="jumpToQuestion({{ $index + 1 }})" 
                            class="aspect-square flex items-center justify-center rounded-xl border {{ $isPreAnswered ? 'bg-indigo-600 text-white border-indigo-600' : 'border-slate-200 bg-white text-slate-600' }} font-bold text-sm transition-all hover:border-indigo-400 hover:text-indigo-600">
                            {{ $index + 1 }}
                        </button>
                    @endforeach
                </div>
            </div>
            
            <!-- Student info footer -->
            <div class="p-6 bg-slate-50 border-t border-slate-200">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-lg shrink-0">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <div class="overflow-hidden">
                        <p class="text-sm font-bold text-slate-800 truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-slate-500 truncate">{{ auth()->user()->code }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Review & Confirm Submit Modal -->
    <div id="confirmModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeConfirmModal()"></div>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-12 text-center sm:p-0">
            <div class="relative bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-xl w-full p-6 sm:p-8 z-10 space-y-6">
                
                <!-- Modal Header -->
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-slate-900 leading-tight">Kiểm tra bài làm trước khi nộp</h3>
                            <p class="text-xs text-slate-500">Đối soát các câu trả lời để đảm bảo không bỏ sót câu hỏi nào.</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeConfirmModal()" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-100 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Progress & Stats Cards -->
                <div class="space-y-3">
                    <div class="flex items-center justify-between text-xs font-semibold text-slate-600">
                        <span>Tiến độ hoàn thành</span>
                        <span id="reviewProgressText" class="font-bold text-indigo-600">0%</span>
                    </div>
                    <div class="w-full bg-slate-100 h-2.5 rounded-full overflow-hidden">
                        <div id="reviewProgressBar" class="bg-indigo-600 h-full rounded-full transition-all duration-300" style="width: 0%"></div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 pt-2">
                        <div class="bg-emerald-50/80 border border-emerald-100 rounded-2xl p-3.5 flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-emerald-500 text-white flex items-center justify-center font-bold text-xs shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <div>
                                <span class="block text-[11px] font-bold text-emerald-800 uppercase tracking-wider">Đã trả lời</span>
                                <span id="reviewAnsweredCount" class="text-lg font-black text-emerald-900">0 câu</span>
                            </div>
                        </div>

                        <div id="reviewUnansweredCard" class="bg-amber-50/80 border border-amber-100 rounded-2xl p-3.5 flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-amber-500 text-white flex items-center justify-center font-bold text-xs shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            </div>
                            <div>
                                <span class="block text-[11px] font-bold text-amber-800 uppercase tracking-wider">Chưa làm</span>
                                <span id="reviewUnansweredCount" class="text-lg font-black text-amber-900">0 câu</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Missing Questions Alert & Quick Jump Badges (Bắt lỗi thiếu câu) -->
                <div id="missingQuestionsBox" class="bg-amber-50 border border-amber-200 rounded-2xl p-4 space-y-2.5">
                    <div class="flex items-center gap-2 text-amber-900 font-bold text-xs">
                        <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        <span>Cảnh báo: Bạn còn các câu hỏi chưa chọn đáp án!</span>
                    </div>
                    <p class="text-xs text-amber-700 leading-relaxed">Nhấp vào từng câu bên dưới để chuyển nhanh đến câu hỏi đó và bổ sung câu trả lời:</p>
                    <div id="missingQuestionsBadges" class="flex flex-wrap gap-2 pt-1">
                        <!-- Populated by JS -->
                    </div>
                </div>

                <!-- All Done Success Box -->
                <div id="allDoneBox" class="hidden bg-emerald-50 border border-emerald-200 rounded-2xl p-4 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-sm shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-emerald-900">Đã hoàn thành 100% câu hỏi!</p>
                        <p class="text-xs text-emerald-700 mt-0.5">Bạn đã trả lời đầy đủ tất cả câu hỏi trong bài thi và có thể tự tin nộp bài.</p>
                    </div>
                </div>

                <!-- Interactive Question Status Grid in Modal -->
                <div>
                    <span class="block text-xs font-bold text-slate-700 mb-2.5">Sơ đồ tổng quan toàn bộ bài làm (Nhấp để đến câu hỏi):</span>
                    <div class="grid grid-cols-10 gap-2 max-h-36 overflow-y-auto p-1" id="reviewModalGrid">
                        <!-- Populated by JS -->
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="pt-2 flex flex-col sm:flex-row items-center gap-3">
                    <button type="button" onclick="closeConfirmModal()" class="w-full sm:w-1/2 px-4 py-3 bg-white border border-slate-300 rounded-xl text-slate-700 font-bold hover:bg-slate-50 transition-colors text-sm flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"></path></svg>
                        Tiếp tục làm bài
                    </button>
                    <button type="button" id="btnFinalSubmit" onclick="submitExamNow()" class="w-full sm:w-1/2 px-4 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl transition-colors shadow-md text-sm flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Xác nhận nộp bài
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const totalQuestions = {{ count($questions) }};
        const totalPages = {{ $totalPages }};
        const examAttemptId = {{ $attempt->id }};
        const storageKey = 'exam_draft_' + examAttemptId;
        const offlineAnswersKey = 'exam_offline_answers_' + examAttemptId;
        const offlineLogsKey = 'exam_offline_logs_' + examAttemptId;
        
        let currentPage = 1;
        let answeredQuestions = new Set();
        let isSubmitting = false;
        let originalPageTitle = document.title;

        // Auto-save & sync API endpoints
        const saveAnswerUrl = '{{ route("student.exams.save-answer", $attempt->id) }}';
        const syncOfflineUrl = '{{ route("student.exams.sync-offline", $attempt->id) }}';
        const cheatLogUrl = '{{ route("student.exams.cheat", $attempt->id) }}';
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // ====================================================
        // 1. 5 QUESTIONS PER PAGE PAGINATION & JUMPING
        // ====================================================
        function changePage(pageNum, targetQuestionIndex = null) {
            if (pageNum < 1 || pageNum > totalPages) return;
            currentPage = pageNum;

            // Toggle page containers
            document.querySelectorAll('.question-page').forEach(page => {
                const pIndex = parseInt(page.dataset.page);
                if (pIndex === currentPage) {
                    page.classList.remove('hidden');
                } else {
                    page.classList.add('hidden');
                }
            });

            // Update Pagination controls
            document.getElementById('currentPageNum').textContent = currentPage;
            document.getElementById('btnPrevPage').disabled = (currentPage === 1);
            document.getElementById('btnNextPage').disabled = (currentPage === totalPages);

            // Scroll action
            if (targetQuestionIndex) {
                setTimeout(() => {
                    const el = document.getElementById('q-' + targetQuestionIndex);
                    if (el) {
                        document.querySelectorAll('.question-block').forEach(b => {
                            b.classList.remove('ring-2', 'ring-indigo-400', 'ring-offset-2');
                        });
                        el.classList.add('ring-2', 'ring-indigo-400', 'ring-offset-2');
                        el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        setTimeout(() => el.classList.remove('ring-2', 'ring-indigo-400', 'ring-offset-2'), 2000);
                    }
                }, 50);
            } else {
                document.getElementById('questionsArea').scrollTo({ top: 0, behavior: 'smooth' });
            }
        }

        // Jump to question from right sidebar
        function jumpToQuestion(index) {
            const targetPage = Math.ceil(index / 5);
            changePage(targetPage, index);
        }

        // Initial pagination state
        changePage(1);

        // ====================================================
        // 2. TIMER & AUTO SUBMIT LOGIC
        // ====================================================
        let timeRemaining = parseInt(document.getElementById('countdown').dataset.time);
        const timerDisplay = document.getElementById('countdown');
        
        function formatTime(seconds) {
            const h = Math.floor(seconds / 3600);
            const m = Math.floor((seconds % 3600) / 60);
            const s = seconds % 60;
            return `${h.toString().padStart(2, '0')}:${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
        }
        
        const timerInterval = setInterval(() => {
            timeRemaining--;
            if (timeRemaining <= 0) {
                clearInterval(timerInterval);
                timerDisplay.textContent = '00:00:00';
                timerDisplay.classList.add('text-red-600');
                isSubmitting = true;
                document.getElementById('examForm').submit();
            } else {
                timerDisplay.textContent = formatTime(timeRemaining);
                if (timeRemaining < 300) {
                    timerDisplay.classList.remove('text-indigo-600');
                    timerDisplay.classList.add('text-red-600', 'animate-pulse');
                }
            }
        }, 1000);

        function markAnswered(index, value = true) {
            const navBtn = document.getElementById('nav-' + index);
            if (!navBtn) return;
            
            if (value && value.toString().trim() !== '') {
                answeredQuestions.add(index);
                navBtn.classList.remove('border-slate-200', 'text-slate-600', 'bg-white');
                navBtn.classList.add('bg-indigo-600', 'text-white', 'border-indigo-600');
            } else {
                answeredQuestions.delete(index);
                navBtn.classList.add('border-slate-200', 'text-slate-600', 'bg-white');
                navBtn.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600');
            }
            
            document.getElementById('answeredCount').textContent = answeredQuestions.size;
            document.getElementById('unansweredCount').textContent = Math.max(0, totalQuestions - answeredQuestions.size);
        }

        // Initialize pre-answered questions
        @foreach($questions as $index => $question)
            @if($savedAnswers->has($question->id) && $savedAnswers[$question->id]->answer_id !== null)
                answeredQuestions.add({{ $index + 1 }});
            @endif
        @endforeach
        document.getElementById('answeredCount').textContent = answeredQuestions.size;
        document.getElementById('unansweredCount').textContent = Math.max(0, totalQuestions - answeredQuestions.size);

        // ====================================================
        // 3. REAL-TIME AUTO SAVE ENGINE & OFFLINE QUEUE
        // ====================================================
        function updateSaveStatus(status, text) {
            const indicator = document.getElementById('saveStatusIndicator');
            if (status === 'saving') {
                indicator.className = 'flex items-center gap-1 text-amber-600 font-medium';
                indicator.innerHTML = `
                    <svg class="animate-spin w-3.5 h-3.5 text-amber-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    <span>Đang lưu...</span>
                `;
            } else if (status === 'saved') {
                indicator.className = 'flex items-center gap-1 text-emerald-600 font-medium';
                indicator.innerHTML = `
                    <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>${text || 'Đã lưu đáp án'}</span>
                `;
            } else if (status === 'offline') {
                indicator.className = 'flex items-center gap-1 text-amber-700 font-bold';
                indicator.innerHTML = `
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    <span>Đã lưu offline</span>
                `;
            }
        }

        function handleAnswerSelect(questionIndex, questionId, answerId) {
            markAnswered(questionIndex, true);
            autoSaveAnswer(questionId, answerId);
            backupToLocalStorage();
        }

        let essayDebounceTimer = null;
        function handleEssayInput(questionIndex, questionId, textValue) {
            markAnswered(questionIndex, textValue.trim().length > 0);
            clearTimeout(essayDebounceTimer);
            essayDebounceTimer = setTimeout(() => {
                autoSaveAnswer(questionId, null, textValue);
                backupToLocalStorage();
            }, 500);
        }

        function clearQuestionAnswer(questionIndex, questionId) {
            const questionBlock = document.getElementById('q-' + questionIndex);
            if (!questionBlock) return;

            // Uncheck radios
            const radios = questionBlock.querySelectorAll(`input[type="radio"][name="answers[${questionId}]"]`);
            let hadValue = false;
            radios.forEach(r => {
                if (r.checked) {
                    r.checked = false;
                    hadValue = true;
                }
            });

            // Clear textarea if essay
            const textarea = questionBlock.querySelector(`textarea[name="answers[${questionId}]"]`);
            if (textarea && textarea.value.trim() !== '') {
                textarea.value = '';
                hadValue = true;
            }

            if (!hadValue) {
                showToastAlert('Thông báo', `Câu ${questionIndex} chưa được chọn đáp án.`, 'info');
                return;
            }

            markAnswered(questionIndex, false);
            autoSaveAnswer(questionId, null, null);
            backupToLocalStorage();
            showToastAlert('Đã xóa lựa chọn', `Đã xóa đáp án của câu số ${questionIndex}.`, 'info');
        }

        function autoSaveAnswer(questionId, answerId, answerText = null) {
            updateSaveStatus('saving');

            if (!navigator.onLine) {
                queueOfflineAnswer(questionId, answerId, answerText);
                updateSaveStatus('offline');
                return;
            }

            fetch(saveAnswerUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    question_id: questionId,
                    answer_id: answerId,
                    answer_text: answerText
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    updateSaveStatus('saved', `Đã lưu lúc ${data.saved_at}`);
                }
            })
            .catch(err => {
                console.warn('Network save failed, queueing offline:', err);
                queueOfflineAnswer(questionId, answerId, answerText);
                updateSaveStatus('offline');
            });
        }

        function queueOfflineAnswer(questionId, answerId, answerText) {
            try {
                let queue = JSON.parse(localStorage.getItem(offlineAnswersKey) || '{}');
                queue[questionId] = {
                    question_id: questionId,
                    answer_id: answerId,
                    answer_text: answerText,
                    timestamp: new Date().toISOString()
                };
                localStorage.setItem(offlineAnswersKey, JSON.stringify(queue));
            } catch(e) {}
        }

        function backupToLocalStorage() {
            const formData = new FormData(document.getElementById('examForm'));
            const data = {};
            for (let [key, val] of formData.entries()) {
                if (key !== '_token') data[key] = val;
            }
            localStorage.setItem(storageKey, JSON.stringify(data));
        }

        // ====================================================
        // 4. ONLINE / OFFLINE DETECTION & AUTO SYNC ENGINE
        // ====================================================
        function handleOnlineStatusChange() {
            const banner = document.getElementById('offlineBanner');
            const netBadge = document.getElementById('networkStatusBadge');

            if (navigator.onLine) {
                banner.classList.add('hidden');
                netBadge.className = 'flex items-center gap-1 text-emerald-600 font-medium';
                netBadge.innerHTML = '<span class="w-2 h-2 rounded-full bg-emerald-500"></span><span>Trực tuyến</span>';
                
                syncOfflineData();
            } else {
                banner.classList.remove('hidden');
                netBadge.className = 'flex items-center gap-1 text-rose-600 font-bold';
                netBadge.innerHTML = '<span class="w-2 h-2 rounded-full bg-rose-500 animate-ping"></span><span>Ngoại tuyến (Offline)</span>';
                updateSaveStatus('offline');
                sendAntiCheatLog('connection_lost');
            }
        }

        window.addEventListener('online', handleOnlineStatusChange);
        window.addEventListener('offline', handleOnlineStatusChange);

        function syncOfflineData() {
            const offlineAnswers = JSON.parse(localStorage.getItem(offlineAnswersKey) || '{}');
            const offlineLogs = JSON.parse(localStorage.getItem(offlineLogsKey) || '[]');

            const answersArray = Object.values(offlineAnswers);

            if (answersArray.length === 0 && offlineLogs.length === 0) {
                sendAntiCheatLog('connection_restored');
                return;
            }

            updateSaveStatus('saving');

            fetch(syncOfflineUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    answers: answersArray,
                    logs: offlineLogs
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    localStorage.removeItem(offlineAnswersKey);
                    localStorage.removeItem(offlineLogsKey);
                    updateSaveStatus('saved', `Đã đồng bộ lại lúc ${data.synced_at}`);
                    showToastAlert('Đã kết nối lại Internet', 'Toàn bộ câu trả lời đã được đồng bộ lên hệ thống.', 'success');
                }
            })
            .catch(err => {
                console.error('Error syncing offline data:', err);
            });
        }

        // ====================================================
        // 5. TOAST NOTIFICATION SYSTEM (NON-BLOCKING)
        // ====================================================
        function showToastAlert(title, message, type = 'warning') {
            const container = document.getElementById('toastContainer');
            if (!container) return;

            const toast = document.createElement('div');
            toast.className = 'toast-animate pointer-events-auto bg-slate-900/95 text-white p-4 rounded-2xl shadow-xl border border-slate-700 flex items-start gap-3 text-xs';
            
            let iconSvg = `
                <div class="w-8 h-8 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
            `;
            if (type === 'danger') {
                iconSvg = `
                    <div class="w-8 h-8 rounded-xl bg-rose-500/20 text-rose-400 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                `;
            } else if (type === 'success') {
                iconSvg = `
                    <div class="w-8 h-8 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                `;
            }

            toast.innerHTML = `
                ${iconSvg}
                <div class="flex-1 min-w-0 pt-0.5">
                    <p class="font-bold text-slate-100 text-sm leading-tight">${title}</p>
                    <p class="text-slate-300 mt-1 leading-normal">${message}</p>
                </div>
                <button type="button" onclick="this.closest('.toast-animate').remove()" class="text-slate-400 hover:text-white p-1">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            `;

            container.appendChild(toast);

            setTimeout(() => {
                toast.style.transition = 'opacity 0.3s, transform 0.3s';
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(100%)';
                setTimeout(() => toast.remove(), 300);
            }, 4000);
        }

        // ====================================================
        // 6. ANTI-CHEAT CONFIGURATION & FORBIDDEN ACTIONS
        // ====================================================
        const antiCheatConfig = {
            enabled: {{ ($exam->enable_anti_cheat ?? true) ? 'true' : 'false' }},
            requireFullscreen: {{ (($exam->enable_anti_cheat ?? true) && ($exam->require_fullscreen ?? true)) ? 'true' : 'false' }},
            preventTabSwitch: {{ (($exam->enable_anti_cheat ?? true) && ($exam->prevent_tab_switch ?? true)) ? 'true' : 'false' }},
            preventCopyPaste: {{ (($exam->enable_anti_cheat ?? true) && ($exam->prevent_copy_paste ?? true)) ? 'true' : 'false' }},
            preventRightClick: {{ (($exam->enable_anti_cheat ?? true) && ($exam->prevent_right_click ?? true)) ? 'true' : 'false' }},
            preventScreenCapture: {{ (($exam->enable_anti_cheat ?? true) && ($exam->prevent_screen_capture ?? true)) ? 'true' : 'false' }}
        };

        // CỜ TRẠNG THÁI: Chỉ bắt đầu ghi log và giám sát thi khi thí sinh đã bấm Bật toàn màn hình và bắt đầu làm bài
        let isExamActive = !antiCheatConfig.requireFullscreen;

        // Remove no-select if copy/paste is permitted
        if (!antiCheatConfig.preventCopyPaste) {
            document.body.classList.remove('no-select');
            document.querySelectorAll('.no-select').forEach(el => el.classList.remove('no-select'));
        }

        function sendAntiCheatLog(eventType, eventData = null, durationSeconds = null, snapshot = null) {
            if (!antiCheatConfig.enabled) return;
            // Chỉ bắt đầu ghi nhận log sau khi thí sinh đã bấm bật toàn màn hình và bắt đầu làm bài
            if (!isExamActive && eventType !== 'fullscreen_enter') return;

            const payload = {
                event_type: eventType,
                event_data: eventData,
                duration_seconds: durationSeconds,
                snapshot: snapshot
            };

            if (!navigator.onLine) {
                try {
                    let logsQueue = JSON.parse(localStorage.getItem(offlineLogsKey) || '[]');
                    logsQueue.push({ ...payload, timestamp: new Date().toISOString() });
                    localStorage.setItem(offlineLogsKey, JSON.stringify(logsQueue));
                } catch(e) {}
                return;
            }

            fetch(cheatLogUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            }).catch(e => console.log('Anti cheat error:', e));
        }

        // Disable Context Menu (Chuột phải)
        if (antiCheatConfig.preventRightClick) {
            document.addEventListener('contextmenu', event => {
                event.preventDefault();
                sendAntiCheatLog('right_click', { action: 'right_click_contextmenu' });
                showToastAlert('Thao tác không được phép', 'Nhấp chuột phải bị vô hiệu hóa theo quy định phòng thi.', 'warning');
            });
        }

        // Copy, Paste, Cut Handlers:
        // Cho phép sao chép từ bản nháp/máy tính và dán vào ô làm bài thi lúc đang thi
        if (antiCheatConfig.preventCopyPaste) {
            document.addEventListener('copy', event => {
                // Cho phép sao chép nội dung từ bảng nháp cá nhân hoặc máy tính khoa học
                if (event.target && event.target.closest('#examPersonalNote, #personalNoteWidget, #scientificCalculatorWidget')) {
                    return; // Hợp lệ, cho phép sao chép nháp
                }
                event.preventDefault();
                sendAntiCheatLog('copy', { action: 'copy_question_text' });
                showToastAlert('Cảnh báo vi phạm', 'Hành vi sao chép đề bài thi bị nghiêm cấm theo quy chế phòng thi.', 'danger');
            });

            document.addEventListener('paste', event => {
                // Cho phép dán nội dung nháp vào ô câu trả lời bài thi hoặc bảng nháp cá nhân
                if (event.target && (
                    event.target.tagName === 'TEXTAREA' || 
                    event.target.tagName === 'INPUT' || 
                    event.target.closest('#examPersonalNote, #personalNoteWidget, #scientificCalculatorWidget, #questionsArea')
                )) {
                    return; // Hợp lệ, cho phép dán vào bài làm lúc đang thi
                }
                event.preventDefault();
                sendAntiCheatLog('paste', { action: 'paste_text' });
                showToastAlert('Cảnh báo vi phạm', 'Hành vi dán (Paste) nội dung không được phép trong phòng thi.', 'danger');
            });

            document.addEventListener('cut', event => {
                // Cho phép cắt trong vùng nháp cá nhân hoặc ô trả lời
                if (event.target && event.target.closest('#examPersonalNote, #personalNoteWidget, #scientificCalculatorWidget, textarea, input')) {
                    return; // Hợp lệ
                }
                event.preventDefault();
                sendAntiCheatLog('cut', { action: 'cut_text' });
                showToastAlert('Cảnh báo vi phạm', 'Hành vi cắt (Cut) nội dung bài thi bị cấm.', 'danger');
            });
        }

        // Keyboard Shortcut Blockers (PrtSc, Win+Shift+S, F12, DevTools, Ctrl+P, Ctrl+U)
        if (antiCheatConfig.preventScreenCapture) {
            document.addEventListener('keydown', function(e) {
                // PrtSc
                if (e.key === 'PrintScreen') {
                    e.preventDefault();
                    navigator.clipboard && navigator.clipboard.writeText && navigator.clipboard.writeText('');
                    sendAntiCheatLog('tab_switch', { action: 'print_screen' });
                    showToastAlert('Cảnh báo vi phạm', 'Hành vi chụp ảnh màn hình (PrintScreen) bị cấm.', 'danger');
                }
                // Win + Shift + S or Meta + Shift + S
                if ((e.metaKey || e.key === 'Meta') && e.shiftKey && (e.key === 's' || e.key === 'S')) {
                    e.preventDefault();
                    navigator.clipboard && navigator.clipboard.writeText && navigator.clipboard.writeText('');
                    sendAntiCheatLog('tab_switch', { action: 'screen_snipping' });
                    showToastAlert('Cảnh báo vi phạm', 'Hành vi sử dụng công cụ chụp ảnh màn hình bị cấm.', 'danger');
                }
                // F12 or Ctrl + Shift + I / J / C (DevTools)
                if (e.key === 'F12' || (e.ctrlKey && e.shiftKey && ['I', 'i', 'J', 'j', 'C', 'c'].includes(e.key))) {
                    e.preventDefault();
                    sendAntiCheatLog('tab_switch', { action: 'inspect_element_devtools' });
                    showToastAlert('Cảnh báo vi phạm', 'Hành vi mở công cụ lập trình viên (DevTools) bị cấm.', 'danger');
                }
                // Ctrl + P (Print)
                if (e.ctrlKey && (e.key === 'p' || e.key === 'P')) {
                    e.preventDefault();
                    sendAntiCheatLog('tab_switch', { action: 'print_dialog' });
                    showToastAlert('Cảnh báo vi phạm', 'Hành vi in bài thi bị cấm.', 'warning');
                }
                // Ctrl + U (View Source)
                if (e.ctrlKey && (e.key === 'u' || e.key === 'U')) {
                    e.preventDefault();
                    sendAntiCheatLog('tab_switch', { action: 'view_source' });
                    showToastAlert('Cảnh báo vi phạm', 'Hành vi xem mã nguồn bài thi bị cấm.', 'warning');
                }
                // Ctrl + S (Save page)
                if (e.ctrlKey && (e.key === 's' || e.key === 'S')) {
                    e.preventDefault();
                }
            });
        }

        // ====================================================
        // 7. FULLSCREEN ENFORCEMENT ENGINE
        // ====================================================
        const examBody = document.documentElement;

        function checkAndEnforceFullscreen() {
            if (!antiCheatConfig.requireFullscreen) return;

            if (!document.fullscreenElement) {
                let fsOverlay = document.getElementById('fsOverlay');
                if (!fsOverlay) {
                    fsOverlay = document.createElement('div');
                    fsOverlay.id = 'fsOverlay';
                    fsOverlay.className = 'fixed inset-0 bg-slate-900/95 backdrop-blur-md z-[9999] flex flex-col items-center justify-center p-6 text-center';
                    fsOverlay.innerHTML = `
                        <div class="bg-white p-8 rounded-3xl max-w-md w-full text-center shadow-2xl border border-slate-100">
                            <div class="w-16 h-16 bg-indigo-100 text-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                            </div>
                            <h2 class="text-2xl font-black text-slate-900 mb-2">Chế độ thi Nghiêm ngặt</h2>
                            <p class="text-slate-600 text-sm mb-6 leading-relaxed">
                                Kỳ thi yêu cầu chế độ <strong>Toàn màn hình (Fullscreen)</strong>. Mọi hành vi thoát hoặc chuyển màn hình đều được ghi nhận vào nhật ký giám sát.
                            </p>
                            <button onclick="requestFullscreenMode()" class="px-6 py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl w-full transition-all shadow-md active:scale-95 flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                                Bật Toàn màn hình & Làm bài
                            </button>
                        </div>
                    `;
                    document.body.appendChild(fsOverlay);
                }
            }
        }

        function requestFullscreenMode() {
            function activateExamSession() {
                const fs = document.getElementById('fsOverlay');
                if (fs) fs.remove();

                if (!isExamActive) {
                    isExamActive = true;
                    console.log('[Exam] Thí sinh đã bấm bật toàn màn hình và bắt đầu làm bài. Bắt đầu kích hoạt giám sát & ghi log.');
                    sendAntiCheatLog('fullscreen_enter', { action: 'exam_started' });

                    // Bắt đầu chu kỳ chụp snapshot định kỳ gửi giảng viên (nếu bật camera AI)
                    setTimeout(() => {
                        if (typeof captureAndAnalyzeSnapshot === 'function') {
                            captureAndAnalyzeSnapshot();
                            scheduleNextProctorSnapshot();
                        }
                    }, 4000);
                }
            }

            if (examBody.requestFullscreen) {
                examBody.requestFullscreen().then(() => {
                    activateExamSession();
                }).catch(err => {
                    console.log('Fullscreen error / permission issue:', err.message);
                    activateExamSession();
                });
            } else {
                activateExamSession();
            }
        }

        if (antiCheatConfig.requireFullscreen) {
            document.addEventListener('DOMContentLoaded', () => {
                checkAndEnforceFullscreen();
            });

            document.addEventListener('fullscreenchange', () => {
                if (!document.fullscreenElement && !isSubmitting) {
                    // Chỉ xử lý cảnh báo thoát toàn màn hình nếu bài thi đã được bắt đầu
                    if (isExamActive) {
                        checkAndEnforceFullscreen();
                        handleOutScreen('fullscreen_exit');
                        showToastAlert('Thoát toàn màn hình', 'Bạn vừa rời chế độ toàn màn hình. Vui lòng bật lại để tiếp tục làm bài.', 'danger');
                    }
                } else if (document.fullscreenElement) {
                    const fs = document.getElementById('fsOverlay');
                    if (fs) fs.remove();
                    if (!isExamActive) {
                        isExamActive = true;
                        console.log('[Exam] Bật toàn màn hình thành công. Bắt đầu giám sát & ghi log.');
                        sendAntiCheatLog('fullscreen_enter', { action: 'exam_started' });
                        setTimeout(() => {
                            if (typeof captureAndAnalyzeSnapshot === 'function') {
                                captureAndAnalyzeSnapshot();
                                scheduleNextProctorSnapshot();
                            }
                        }, 4000);
                    } else {
                        handleInScreen('fullscreen_enter');
                    }
                }
            });
        }

        // ====================================================
        // 8. VISIBILITYCHANGE & WINDOW BLUR/FOCUS TRACKER
        // ====================================================
        let outOfScreenTimer = null;
        let outOfScreenSeconds = 0;
        let lastOutTimestamp = null;

        function handleOutScreen(eventType = 'tab_switch') {
            if (!isExamActive || !antiCheatConfig.preventTabSwitch) return;

            if (!outOfScreenTimer) {
                lastOutTimestamp = Date.now();
                document.title = '[CẢNH BÁO] QUAY LẠI LÀM BÀI!';
                sendAntiCheatLog(eventType, { reason: 'alt_tab_or_focus_lost' });

                // Force exit fullscreen on blur/tab switch if fullscreen is required
                if (antiCheatConfig.requireFullscreen && document.fullscreenElement && document.exitFullscreen) {
                    document.exitFullscreen().catch(() => {});
                }

                outOfScreenTimer = setInterval(() => {
                    outOfScreenSeconds++;
                    if (outOfScreenSeconds % 3 === 0) {
                        sendAntiCheatLog('tab_switch', { action: 'away_from_exam' }, 3);
                    }
                }, 1000);
            }
        }

        function handleInScreen(eventType = 'window_focus') {
            if (!isExamActive || !antiCheatConfig.preventTabSwitch) return;

            if (outOfScreenTimer) {
                document.title = originalPageTitle;
                clearInterval(outOfScreenTimer);
                outOfScreenTimer = null;

                const duration = Math.max(1, Math.round((Date.now() - (lastOutTimestamp || Date.now())) / 1000));
                let remain = outOfScreenSeconds % 3;
                if (remain > 0) {
                    sendAntiCheatLog('tab_switch', { duration: duration }, remain);
                }
                sendAntiCheatLog(eventType, { duration_away: duration });

                // Alert student with toast showing exact away time
                showToastAlert(
                    'Phát hiện rời màn hình làm bài',
                    `Bạn vừa chuyển ứng dụng (Alt+Tab) hoặc rời trang trong ${duration} giây. Hành vi đã được ghi nhận!`,
                    'danger'
                );

                outOfScreenSeconds = 0;
                lastOutTimestamp = null;

                // Re-enforce fullscreen check
                if (antiCheatConfig.requireFullscreen) {
                    checkAndEnforceFullscreen();
                }
            }
        }

        if (antiCheatConfig.preventTabSwitch) {
            // Tab switch / page hide via visibilitychange
            document.addEventListener('visibilitychange', function() {
                if (timeRemaining > 0 && !isSubmitting) {
                    if (document.hidden || document.visibilityState === 'hidden') {
                        handleOutScreen('tab_switch');
                    } else if (document.visibilityState === 'visible') {
                        handleInScreen('window_focus');
                    }
                }
            });

            // Window blur / focus (Alt+Tab to VS Code, Discord, Word, etc.)
            window.addEventListener('blur', function() {
                if (timeRemaining > 0 && !isSubmitting) {
                    handleOutScreen('window_blur');
                }
            });

            window.addEventListener('focus', function() {
                handleInScreen('window_focus');
            });
        }

        // ====================================================
        // 9. SUBMISSION & REVIEW BEFORE SUBMIT
        // ====================================================
        function confirmSubmit() {
            const answeredCount = answeredQuestions.size;
            const unansweredCount = Math.max(0, totalQuestions - answeredCount);
            const progressPercent = totalQuestions > 0 ? Math.round((answeredCount / totalQuestions) * 100) : 0;

            // Update Progress & Counts
            document.getElementById('reviewProgressText').textContent = `${progressPercent}%`;
            document.getElementById('reviewProgressBar').style.width = `${progressPercent}%`;
            document.getElementById('reviewAnsweredCount').textContent = `${answeredCount} / ${totalQuestions} câu`;
            document.getElementById('reviewUnansweredCount').textContent = `${unansweredCount} câu`;

            const missingBox = document.getElementById('missingQuestionsBox');
            const allDoneBox = document.getElementById('allDoneBox');
            const missingBadgesContainer = document.getElementById('missingQuestionsBadges');
            const modalGrid = document.getElementById('reviewModalGrid');
            const finalSubmitBtn = document.getElementById('btnFinalSubmit');

            missingBadgesContainer.innerHTML = '';
            modalGrid.innerHTML = '';

            const missingQuestionsList = [];
            for (let i = 1; i <= totalQuestions; i++) {
                const isAns = answeredQuestions.has(i);
                if (!isAns) {
                    missingQuestionsList.push(i);
                }

                // Build interactive grid cell inside modal
                const gridCell = document.createElement('button');
                gridCell.type = 'button';
                gridCell.onclick = () => {
                    closeConfirmModal();
                    jumpToQuestion(i);
                };
                gridCell.className = `aspect-square rounded-lg flex items-center justify-center font-bold text-xs transition-all ${
                    isAns 
                    ? 'bg-emerald-600 text-white hover:bg-emerald-700 shadow-sm' 
                    : 'bg-rose-50 text-rose-700 border border-rose-300 hover:bg-rose-100 hover:border-rose-500'
                }`;
                gridCell.title = `Câu ${i}: ${isAns ? 'Đã làm' : 'Chưa làm (Nhấp để đến câu này)'}`;
                gridCell.textContent = i;
                modalGrid.appendChild(gridCell);
            }

            if (unansweredCount > 0) {
                missingBox.classList.remove('hidden');
                allDoneBox.classList.add('hidden');

                // Update missing box header
                missingBox.innerHTML = `
                    <div class="flex items-center gap-2 text-rose-900 font-bold text-xs">
                        <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        <span>Không thể nộp bài: Bạn còn ${unansweredCount} câu chưa làm!</span>
                    </div>
                    <p class="text-xs text-rose-700 leading-relaxed">Quy định phòng thi yêu cầu <strong>trả lời đầy đủ 100% câu hỏi</strong> mới được phép nộp bài. Nhấp vào các câu bên dưới để làm bổ sung:</p>
                    <div id="missingQuestionsBadges" class="flex flex-wrap gap-2 pt-1"></div>
                `;

                const newBadgesContainer = document.getElementById('missingQuestionsBadges');
                missingQuestionsList.forEach(qNum => {
                    const badgeBtn = document.createElement('button');
                    badgeBtn.type = 'button';
                    badgeBtn.onclick = () => {
                        closeConfirmModal();
                        jumpToQuestion(qNum);
                    };
                    badgeBtn.className = 'px-3 py-1.5 bg-rose-100/90 hover:bg-rose-200 text-rose-900 border border-rose-300 rounded-xl font-bold text-xs flex items-center gap-1.5 transition-colors shadow-sm cursor-pointer';
                    badgeBtn.innerHTML = `<span>Câu ${qNum} ➔</span>`;
                    newBadgesContainer.appendChild(badgeBtn);
                });

                // Lock final submit button
                if (finalSubmitBtn) {
                    finalSubmitBtn.disabled = true;
                    finalSubmitBtn.className = 'w-full sm:w-1/2 px-4 py-3 bg-slate-100 text-slate-400 font-bold rounded-xl cursor-not-allowed text-xs flex items-center justify-center gap-2 border border-slate-200 shadow-none';
                    finalSubmitBtn.innerHTML = `
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        <span>Chưa đủ điều kiện nộp bài (Còn ${unansweredCount} câu)</span>
                    `;
                }
            } else {
                missingBox.classList.add('hidden');
                allDoneBox.classList.remove('hidden');

                // Unlock final submit button
                if (finalSubmitBtn) {
                    finalSubmitBtn.disabled = false;
                    finalSubmitBtn.className = 'w-full sm:w-1/2 px-4 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl transition-colors shadow-md text-sm flex items-center justify-center gap-2 active:scale-95 cursor-pointer';
                    finalSubmitBtn.innerHTML = `
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>Xác nhận nộp bài ngay</span>
                    `;
                }
            }

            document.getElementById('confirmModal').classList.remove('hidden');
        }
        
        function closeConfirmModal() {
            document.getElementById('confirmModal').classList.add('hidden');
        }

        function submitExamNow() {
            if (answeredQuestions.size < totalQuestions) {
                const missingCount = totalQuestions - answeredQuestions.size;
                showToastAlert('Chưa hoàn thành', `Bạn còn ${missingCount} câu chưa làm. Bắt buộc hoàn thành 100% mới được nộp bài!`, 'danger');
                
                // Find first missing question and jump to it
                for (let i = 1; i <= totalQuestions; i++) {
                    if (!answeredQuestions.has(i)) {
                        closeConfirmModal();
                        jumpToQuestion(i);
                        break;
                    }
                }
                return;
            }

            isSubmitting = true;
            localStorage.removeItem(storageKey);
            localStorage.removeItem(offlineAnswersKey);
            localStorage.removeItem(offlineLogsKey);
            document.getElementById('examForm').submit();
        }

        // Note: Đã tắt cảnh báo rời khỏi trang web theo yêu cầu người dùng

        // ====================================================
        // AI PROCTORING ENGINE (RANDOM INTERVALS 2 - 4 MINS)
        // ====================================================
        @if($exam->enable_proctor_camera ?? true)
        let proctorStream = null;
        let proctorTimer = null;

        async function initProctorCamera() {
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                console.warn('Webcam không được hỗ trợ trên trình duyệt này.');
                return;
            }

            const proctorVideo = document.getElementById('proctorVideo');
            if (!proctorVideo) {
                console.warn('proctorVideo DOM element not found, retrying in 500ms...');
                setTimeout(initProctorCamera, 500);
                return;
            }

            try {
                proctorStream = await navigator.mediaDevices.getUserMedia({
                    video: { width: { ideal: 640 }, height: { ideal: 480 }, facingMode: 'user' },
                    audio: false
                });

                proctorVideo.srcObject = proctorStream;
                try {
                    await proctorVideo.play();
                } catch(e) {
                    console.log('Video play policy handled:', e);
                }

                // Start In-Browser Realtime Face Detection (0 or >1 faces & head pose)
                initRealtimeFaceDetection();

                // Nếu kỳ thi không yêu cầu toàn màn hình thì bắt đầu snapshot sau 6s, ngược lại sẽ bắt đầu khi thí sinh bấm Bật toàn màn hình
                if (isExamActive) {
                    setTimeout(() => {
                        captureAndAnalyzeSnapshot();
                        scheduleNextProctorSnapshot();
                    }, 6000);
                }

            } catch (err) {
                console.error('Proctor camera init error:', err);
            }
        }

        function scheduleNextProctorSnapshot() {
            // Configured snapshot interval set by Teacher (default 120s if not set)
            const baseInterval = {{ intval($exam->proctor_interval_seconds ?: 120) }};
            
            // Random variation around the teacher's configured interval (±10%, min 3s) so timing is not completely predictable
            const jitterRange = Math.max(3, Math.round(baseInterval * 0.10));
            const minSec = Math.max(10, baseInterval - jitterRange);
            const maxSec = baseInterval + jitterRange;
            const randomDelay = Math.floor(Math.random() * (maxSec - minSec + 1) + minSec) * 1000;
            
            console.log(`[AI Proctor] Next snapshot in ${Math.round(randomDelay / 1000)}s (Configured by Teacher: ${baseInterval}s).`);

            if (proctorTimer) clearTimeout(proctorTimer);
            proctorTimer = setTimeout(async () => {
                if (!isSubmitting && isExamActive) {
                    await captureAndAnalyzeSnapshot();
                    scheduleNextProctorSnapshot();
                }
            }, randomDelay);
        }

        async function captureAndAnalyzeSnapshot() {
            const proctorVideo = document.getElementById('proctorVideo');
            const proctorCanvas = document.getElementById('proctorOffscreenCanvas');
            if (!isExamActive || !proctorVideo || !proctorCanvas || isSubmitting) return;

            // Wait if video dimensions are not ready yet
            if (proctorVideo.videoWidth === 0 || proctorVideo.videoHeight === 0) {
                console.warn('[AI Proctor] Video feed not ready yet, will retry shortly...');
                setTimeout(captureAndAnalyzeSnapshot, 2000);
                return;
            }

            proctorCanvas.width = proctorVideo.videoWidth || 640;
            proctorCanvas.height = proctorVideo.videoHeight || 480;
            const ctx = proctorCanvas.getContext('2d');
            ctx.drawImage(proctorVideo, 0, 0, proctorCanvas.width, proctorCanvas.height);
            const snapData = proctorCanvas.toDataURL('image/jpeg', 0.85);

            try {
                const response = await fetch('{{ route("student.exams.proctor-snapshot", $attempt->id) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ image: snapData })
                });

                const data = await response.json();
                console.log('[AI Proctor] Snapshot analyzed & saved silently:', data.status);
                // Note: Không báo cáo chụp hay hiện cảnh báo làm phiền sinh viên (chạy hoàn toàn ngầm)
            } catch (err) {
                console.warn('[AI Proctor] Snapshot upload notice:', err);
            }
        }

        function toggleProctorPip() {
            const body = document.getElementById('pipBody');
            const btn = document.getElementById('pipToggleBtn');
            if (body.classList.contains('hidden')) {
                body.classList.remove('hidden');
                btn.innerHTML = '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>';
            } else {
                body.classList.add('hidden');
                btn.innerHTML = '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>';
            }
        }

        // ----------------------------------------------------
        // REALTIME CLIENT-SIDE HEAD POSE & EYE TRACKING
        // Chạy ngầm trên trình duyệt để theo dõi hướng đầu và mắt
        // Khi quay đầu sang trái hoặc phải liên tục 6-8 giây -> ghi log thời gian & hướng quay
        // Tuyệt đối không hiện bất kỳ thông báo nào cho thí sinh
        // ----------------------------------------------------
        // ----------------------------------------------------
        // REALTIME CLIENT-SIDE FACE & HEAD POSE TRACKING
        // Chạy nhẹ trên trình duyệt bằng TinyFaceDetector:
        // ----------------------------------------------------
        // REALTIME CLIENT-SIDE FACE DETECTION (LIGHTWEIGHT)
        // Chạy siêu nhẹ trên trình duyệt bằng TinyFaceDetector:
        // 1. Phát hiện 0 khuôn mặt (Vắng mặt trước camera >= 2.5s)
        // 2. Phát hiện > 1 khuôn mặt (Nhiều người trước camera >= 1.0s)
        // 3. Theo dõi quay mặt sang hướng khác liên tục >= 10s (khi có 1 khuôn mặt)
        // Khi phát hiện bất thường: TỰ ĐỘNG CHỤP ẢNH TỨC THÌ GỬI VỀ CHO GIẢNG VIÊN KIỂM TRA
        // (Lưu vào ExamProctorSnapshot và hiển thị ngay trên Bộ sưu tập ảnh AI của Giảng viên)
        // ----------------------------------------------------
        let isFaceTrackingRunning = false;
        let faceTrackingTimer = null;
        let absentStartTime = null;
        let lastAbsentLogTime = 0;
        let multipleFacesStartTime = null;
        let lastMultipleLogTime = 0;
        let currentSustainedDirection = null; // 'left' | 'right' | 'down' | 'up' | null
        let sustainedDirectionStartTime = null;
        let lastHeadTurnLogTime = 0;

        const ABSENT_THRESHOLD_MS = 2500; // 2.5s liên tục không thấy khuôn mặt thí sinh
        const MULTIPLE_THRESHOLD_MS = 1000; // 1.0s liên tục thấy >= 2 khuôn mặt
        const SUSTAINED_TURN_MIN_SECONDS = 10; // 10 giây quay mặt sang hướng khác liên tục mới chụp ảnh
        const ANOMALY_LOG_COOLDOWN_MS = 15000; // Cooldown 15s giữa 2 lần ghi nhận cùng loại

        function captureOffscreenSnapshot(video) {
            try {
                const canvas = document.getElementById('proctorOffscreenCanvas');
                if (!canvas || !video || video.videoWidth === 0) return null;
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                return canvas.toDataURL('image/jpeg', 0.85);
            } catch (e) {
                return null;
            }
        }

        function drawFaceBoundingBox(ctx, x, y, width, height, label, strokeColor = '#10b981', textColor = '#ffffff') {
            ctx.save();
            ctx.strokeStyle = strokeColor;
            ctx.lineWidth = 2.5;
            ctx.shadowColor = strokeColor;
            ctx.shadowBlur = 6;

            // 4 góc viền công nghệ AI (Tech corner brackets)
            const cornerLength = Math.max(8, Math.min(width, height) * 0.22);
            ctx.beginPath();
            // Góc trên - trái
            ctx.moveTo(x, y + cornerLength);
            ctx.lineTo(x, y);
            ctx.lineTo(x + cornerLength, y);
            // Góc trên - phải
            ctx.moveTo(x + width - cornerLength, y);
            ctx.lineTo(x + width, y);
            ctx.lineTo(x + width, y + cornerLength);
            // Góc dưới - phải
            ctx.moveTo(x + width, y + height - cornerLength);
            ctx.lineTo(x + width, y + height);
            ctx.lineTo(x + width - cornerLength, y + height);
            // Góc dưới - trái
            ctx.moveTo(x + cornerLength, y + height);
            ctx.lineTo(x, y + height);
            ctx.lineTo(x, y + height - cornerLength);
            ctx.stroke();

            // Khung viền mờ bên trong
            ctx.shadowBlur = 0;
            ctx.strokeStyle = strokeColor + '44';
            ctx.lineWidth = 1;
            ctx.strokeRect(x, y, width, height);

            // Nhãn trạng thái (Tag hiển thị trên đầu khuôn mặt)
            if (label) {
                ctx.font = 'bold 10px Inter, sans-serif';
                const textMetrics = ctx.measureText(label);
                const padX = 5;
                const tagWidth = textMetrics.width + padX * 2;
                const tagHeight = 16;
                const tagY = Math.max(2, y - tagHeight - 3);

                ctx.fillStyle = strokeColor;
                ctx.beginPath();
                if (ctx.roundRect) {
                    ctx.roundRect(x, tagY, tagWidth, tagHeight, 4);
                } else {
                    ctx.rect(x, tagY, tagWidth, tagHeight);
                }
                ctx.fill();

                ctx.fillStyle = textColor;
                ctx.fillText(label, x + padX, tagY + 12);
            }
            ctx.restore();
        }

        async function triggerInstantAnomalySnapshot(anomalyType, details, faceCount = 1, durationSeconds = null, directionText = null) {
            if (!isExamActive || isSubmitting) return;
            const video = document.getElementById('proctorVideo');
            if (!video || video.videoWidth === 0) return;

            const snapData = captureOffscreenSnapshot(video);
            if (!snapData) return;

            console.log(`[AI Proctor Realtime] 📸 Tự động chụp & gửi ảnh bất thường (${anomalyType}) về cho giảng viên kiểm tra...`);

            // 1. Gửi ảnh lên proctor-snapshot để lưu trực tiếp vào ExamProctorSnapshot và hiển thị trong Bộ sưu tập ảnh Giảng viên
            try {
                fetch('{{ route("student.exams.proctor-snapshot", $attempt->id) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        image: snapData,
                        trigger_source: 'browser_realtime_face_detection',
                        anomaly_type: anomalyType,
                        face_count: faceCount,
                        details: details,
                        duration_seconds: durationSeconds
                    })
                }).then(r => r.json()).then(data => {
                    console.log('[AI Proctor Realtime] Đã lưu snapshot bất thường vào ExamProctorSnapshot cho giảng viên:', data);
                }).catch(e => {
                    console.warn('[AI Proctor Realtime] Gửi snapshot thất bại:', e);
                });
            } catch (snapErr) {
                console.warn('[AI Proctor Realtime] Proctor snapshot request error:', snapErr);
            }

            // 2. Gửi thêm qua sendAntiCheatLog để ghi nhận event vi phạm trong timeline
            sendAntiCheatLog(anomalyType, {
                message: details,
                face_count: faceCount,
                duration_seconds: durationSeconds,
                direction_text: directionText,
                source: 'browser_realtime_face_detection'
            }, durationSeconds, snapData);
        }

        async function initRealtimeFaceDetection() {
            if (isFaceTrackingRunning) return;

            if (typeof faceapi === 'undefined') {
                console.log('[AI Proctor] Thư viện face-api đang tải, sẽ thử lại sau 500ms...');
                setTimeout(initRealtimeFaceDetection, 500);
                return;
            }

            isFaceTrackingRunning = true;
            console.log('[AI Proctor] Khởi tạo Realtime In-Browser Face Detection (0 hoặc >1 khuôn mặt)...');

            const modelPath = '{{ asset("vendor/face-api/models") }}';

            // 1. Nạp TinyFaceDetector trước để phát hiện khuôn mặt siêu nhẹ (~15ms)
            try {
                if (!faceapi.nets.tinyFaceDetector.isLoaded) {
                    await faceapi.nets.tinyFaceDetector.loadFromUri(modelPath);
                    console.log('[AI Proctor] Đã nạp thành công TinyFaceDetector siêu nhẹ.');
                }
                // Nạp thêm FaceLandmark68 ngầm để theo dõi hướng đầu khi có 1 khuôn mặt mà không làm chặn luồng
                if (!faceapi.nets.faceLandmark68Net.isLoaded) {
                    faceapi.nets.faceLandmark68Net.loadFromUri(modelPath)
                        .then(() => console.log('[AI Proctor] Đã nạp thành công FaceLandmark68.'))
                        .catch(err => console.warn('[AI Proctor] Landmark load warning:', err));
                }
            } catch (err) {
                console.warn('[AI Proctor] Lỗi nạp model AI trình duyệt, sẽ tự động thử lại sau 2s:', err);
                isFaceTrackingRunning = false;
                setTimeout(initRealtimeFaceDetection, 2000);
                return;
            }

            if (faceTrackingTimer) clearInterval(faceTrackingTimer);
            faceTrackingTimer = setInterval(async () => {
                if (!isExamActive || isSubmitting || typeof faceapi === 'undefined') return;
                if (!faceapi.nets.tinyFaceDetector.isLoaded) return;

                const video = document.getElementById('proctorVideo');
                if (!video || video.paused || video.ended || video.readyState < 2 || video.videoWidth === 0) return;

                try {
                    // PHÁT HIỆN TẤT CẢ KHUÔN MẶT SIÊU NHẸ BẰNG TINY FACE DETECTOR (~15ms)
                    const detections = await faceapi.detectAllFaces(
                        video, 
                        new faceapi.TinyFaceDetectorOptions({ inputSize: 224, scoreThreshold: 0.35 })
                    );

                    const faceCount = detections ? detections.length : 0;
                    const now = Date.now();

                    // =========================================================
                    // CẬP NHẬT CANVAS OVERLAY & BOUNDING BOX TRỰC TIẾP TRÊN CAMERA
                    // =========================================================
                    const overlayCanvas = document.getElementById('proctorOverlayCanvas');
                    let ctx = null;
                    if (overlayCanvas) {
                        if (overlayCanvas.width !== video.videoWidth || overlayCanvas.height !== video.videoHeight) {
                            overlayCanvas.width = video.videoWidth;
                            overlayCanvas.height = video.videoHeight;
                        }
                        ctx = overlayCanvas.getContext('2d');
                        ctx.clearRect(0, 0, overlayCanvas.width, overlayCanvas.height);
                    }

                    const wrapperEl = document.getElementById('proctorVideoWrapper');
                    const countBadge = document.getElementById('proctorFaceCountBadge');
                    const countDot = document.getElementById('proctorFaceCountDot');
                    const countText = document.getElementById('proctorFaceCountText');
                    const alertBanner = document.getElementById('proctorAlertBanner');
                    const alertText = document.getElementById('proctorAlertText');

                    // =========================================================
                    // 1. PHÁT HIỆN 0 KHUÔN MẶT (THÍ SINH VẮNG MẶT / RỜI KHỎI CAMERA)
                    // =========================================================
                    if (faceCount === 0) {
                        currentSustainedDirection = null;
                        sustainedDirectionStartTime = null;
                        multipleFacesStartTime = null;

                        // Cập nhật giao diện cảnh báo
                        if (countText) countText.innerText = '0 khuôn mặt';
                        if (countDot) countDot.className = 'w-1.5 h-1.5 rounded-full bg-rose-500 animate-ping';
                        if (countBadge) countBadge.className = 'absolute top-1.5 right-1.5 px-2 py-0.5 bg-rose-950/85 border border-rose-500/50 rounded-md text-[9px] font-bold text-rose-300 flex items-center gap-1.5 transition-all z-10';
                        if (wrapperEl) {
                            wrapperEl.classList.add('border-rose-500', 'ring-2', 'ring-rose-500/50');
                            wrapperEl.classList.remove('border-slate-800');
                        }
                        if (alertBanner) alertBanner.classList.remove('hidden');
                        if (alertText) alertText.innerText = 'Cảnh báo: 0 khuôn mặt!';

                        // Vẽ khung đỏ mờ gợi ý vị trí khuôn mặt
                        if (ctx && overlayCanvas) {
                            ctx.save();
                            ctx.strokeStyle = 'rgba(244, 63, 94, 0.5)';
                            ctx.setLineDash([8, 6]);
                            ctx.lineWidth = 2;
                            ctx.strokeRect(overlayCanvas.width * 0.18, overlayCanvas.height * 0.15, overlayCanvas.width * 0.64, overlayCanvas.height * 0.7);
                            ctx.restore();
                        }

                        if (absentStartTime === null) {
                            absentStartTime = now;
                        } else {
                            const absentMs = now - absentStartTime;
                            if (absentMs >= ABSENT_THRESHOLD_MS && (now - lastAbsentLogTime >= ANOMALY_LOG_COOLDOWN_MS)) {
                                lastAbsentLogTime = now;
                                const absentSec = Math.max(2, Math.round(absentMs / 1000));
                                const msg = `Camera không phát hiện thấy thí sinh trước màn hình (Vắng mặt ${absentSec} giây)`;

                                // Hiển thị cảnh báo trực tiếp cho thí sinh
                                showToastAlert('Cảnh báo vắng mặt', 'Không phát hiện thấy khuôn mặt thí sinh trước camera! Vui lòng ngồi trước màn hình.', 'danger');

                                // Tự động chụp ảnh gửi về cho giảng viên kiểm tra ngay lập tức
                                triggerInstantAnomalySnapshot('face_absent', msg, 0, absentSec);
                            }
                        }
                        return;
                    } else {
                        absentStartTime = null;
                    }

                    // =========================================================
                    // 2. PHÁT HIỆN TỪ 2 KHUÔN MẶT TRỞ LÊN (> 1 NGƯỜI TRƯỚC CAMERA)
                    // =========================================================
                    if (faceCount > 1) {
                        currentSustainedDirection = null;
                        sustainedDirectionStartTime = null;

                        // Cập nhật giao diện cảnh báo đỏ
                        if (countText) countText.innerText = `${faceCount} khuôn mặt`;
                        if (countDot) countDot.className = 'w-1.5 h-1.5 rounded-full bg-rose-500 animate-ping';
                        if (countBadge) countBadge.className = 'absolute top-1.5 right-1.5 px-2 py-0.5 bg-rose-950/85 border border-rose-500/50 rounded-md text-[9px] font-bold text-rose-300 flex items-center gap-1.5 transition-all z-10';
                        if (wrapperEl) {
                            wrapperEl.classList.add('border-rose-500', 'ring-2', 'ring-rose-500/50');
                            wrapperEl.classList.remove('border-slate-800');
                        }
                        if (alertBanner) alertBanner.classList.remove('hidden');
                        if (alertText) alertText.innerText = `Cảnh báo: Phát hiện ${faceCount} khuôn mặt!`;

                        // Vẽ Bounding Box màu đỏ trực tiếp quanh từng khuôn mặt
                        if (ctx && overlayCanvas) {
                            detections.forEach((det, idx) => {
                                const b = det.box;
                                const flippedX = overlayCanvas.width - (b.x + b.width);
                                drawFaceBoundingBox(ctx, flippedX, b.y, b.width, b.height, `Mặt #${idx + 1} (Vi phạm)`, '#f43f5e', '#ffffff');
                            });
                        }

                        if (multipleFacesStartTime === null) {
                            multipleFacesStartTime = now;
                        } else {
                            const multiMs = now - multipleFacesStartTime;
                            if (multiMs >= MULTIPLE_THRESHOLD_MS && (now - lastMultipleLogTime >= ANOMALY_LOG_COOLDOWN_MS)) {
                                lastMultipleLogTime = now;
                                const msg = `Phát hiện ${faceCount} khuôn mặt cùng xuất hiện trước camera (Nghi vấn có người trợ giúp)`;

                                // Hiển thị cảnh báo trực tiếp cho thí sinh
                                showToastAlert('Cảnh báo vi phạm', `Phát hiện ${faceCount} khuôn mặt cùng xuất hiện trước camera! Vui lòng chỉ một mình làm bài thi.`, 'danger');

                                // Tự động chụp ảnh gửi về cho giảng viên kiểm tra ngay lập tức
                                triggerInstantAnomalySnapshot('multiple_persons', msg, faceCount, null);
                            }
                        }
                        return;
                    } else {
                        multipleFacesStartTime = null;
                    }

                    // =========================================================
                    // 3. ĐÚNG 1 KHUÔN MẶT (HỢP LỆ)
                    // =========================================================
                    if (countText) countText.innerText = '1 khuôn mặt';
                    if (countDot) countDot.className = 'w-1.5 h-1.5 rounded-full bg-emerald-400';
                    if (countBadge) countBadge.className = 'absolute top-1.5 right-1.5 px-2 py-0.5 bg-black/75 border border-white/10 rounded-md text-[9px] font-bold text-emerald-400 flex items-center gap-1.5 transition-all z-10';

                    const b = detections[0].box;
                    const flippedX = overlayCanvas ? overlayCanvas.width - (b.x + b.width) : 0;

                    // Theo dõi hướng mặt & ánh mắt (khi landmark đã sẵn sàng)
                    if (faceapi.nets.faceLandmark68Net.isLoaded && detections.length === 1) {
                        try {
                            const faceWithLandmarks = await faceapi.detectSingleFace(
                                video, 
                                new faceapi.TinyFaceDetectorOptions({ inputSize: 224, scoreThreshold: 0.35 })
                            ).withFaceLandmarks();

                            if (faceWithLandmarks && faceWithLandmarks.landmarks) {
                                const landmarks = faceWithLandmarks.landmarks;
                                const nose = landmarks.getNose()[3]; // Point 30 (Nose tip)
                                const leftEye = landmarks.getLeftEye()[0];  // Point 36 (outer corner)
                                const rightEye = landmarks.getRightEye()[3]; // Point 45 (outer corner)

                                const eyeMidX = (leftEye.x + rightEye.x) / 2;
                                const eyeMidY = (leftEye.y + rightEye.y) / 2;
                                const eyeDist = Math.hypot(rightEye.x - leftEye.x, rightEye.y - leftEye.y) || 1;
                                const yaw = (nose.x - eyeMidX) / eyeDist;

                                const leftEyeInner = landmarks.getLeftEye()[3];
                                const rightEyeInner = landmarks.getRightEye()[0];
                                const eyeSpan = Math.abs(rightEyeInner.x - leftEyeInner.x) || 1;
                                const noseToLeft = Math.abs(nose.x - leftEyeInner.x);
                                const noseToRight = Math.abs(rightEyeInner.x - nose.x);
                                const gazeAsym = (noseToLeft - noseToRight) / eyeSpan;

                                // Tính góc cúi/ngửa mặt (Pitch) theo tỷ lệ khoảng cách Mắt-Mũi so với Mũi-Miệng
                                const mouth = landmarks.getMouth();
                                const mouthMidY = (mouth && mouth.length >= 7) ? (mouth[0].y + mouth[6].y) / 2 : (nose.y + eyeDist * 0.7);
                                const eyeToNose = Math.max(1, nose.y - eyeMidY);
                                const noseToMouth = Math.max(1, mouthMidY - nose.y);
                                const pitchRatio = eyeToNose / noseToMouth;

                                let detectedTurn = null;
                                let turnAction = '';

                                if (yaw >= 0.13 || (yaw >= 0.09 && gazeAsym >= 0.16)) {
                                    detectedTurn = 'left';
                                    turnAction = 'quay trái';
                                } else if (yaw <= -0.13 || (yaw <= -0.09 && gazeAsym <= -0.16)) {
                                    detectedTurn = 'right';
                                    turnAction = 'quay phải';
                                } else if (pitchRatio > 1.85) {
                                    detectedTurn = 'down';
                                    turnAction = 'cúi đầu';
                                } else if (pitchRatio < 0.45) {
                                    detectedTurn = 'up';
                                    turnAction = 'ngẩng đầu';
                                }

                                if (detectedTurn) {
                                    if (sustainedDirectionStartTime === null) {
                                        sustainedDirectionStartTime = now;
                                        currentSustainedDirection = detectedTurn;
                                    } else {
                                        currentSustainedDirection = detectedTurn;
                                    }
                                    const elapsedSec = Math.floor((now - sustainedDirectionStartTime) / 1000);

                                    if (elapsedSec >= SUSTAINED_TURN_MIN_SECONDS) {
                                        // ĐÃ QUAY MẶT LIÊN TỤC >= 10 GIÂY: VI PHẠM -> VẼ BOX ĐỎ, CẢNH BÁO VÀ CHỤP ẢNH (KHÔNG HIỆN SỐ GIÂY)
                                        if (ctx && overlayCanvas) {
                                            drawFaceBoundingBox(ctx, flippedX, b.y, b.width, b.height, `Thí sinh ${turnAction} quá lâu`, '#f43f5e', '#ffffff');
                                        }
                                        if (wrapperEl) {
                                            wrapperEl.classList.add('border-rose-500', 'ring-2', 'ring-rose-500/50');
                                            wrapperEl.classList.remove('border-slate-800', 'border-amber-500');
                                        }
                                        if (alertBanner) alertBanner.classList.remove('hidden');
                                        if (alertText) alertText.innerText = `Cảnh báo: Thí sinh ${turnAction} quá lâu!`;

                                        if (now - lastHeadTurnLogTime >= ANOMALY_LOG_COOLDOWN_MS) {
                                            lastHeadTurnLogTime = now;
                                            const msg = `AI phát hiện thí sinh ${turnAction} quá lâu, không nhìn trực diện màn hình.`;
                                            showToastAlert('Cảnh báo vi phạm', `Thí sinh ${turnAction} quá lâu! Vui lòng nhìn trực diện màn hình làm bài.`, 'danger');
                                            triggerInstantAnomalySnapshot('looking_away', msg, 1, elapsedSec, `Thí sinh ${turnAction} quá lâu`);
                                        }
                                    } else if (elapsedSec >= 2) {
                                        // ĐANG QUAY MẶT (từ 2s đến < 10s): VẼ BOX VÀNG CAM CẢNH BÁO (KHÔNG HIỆN SỐ GIÂY)
                                        if (ctx && overlayCanvas) {
                                            drawFaceBoundingBox(ctx, flippedX, b.y, b.width, b.height, `Đang ${turnAction}...`, '#f59e0b', '#ffffff');
                                        }
                                        if (wrapperEl) {
                                            wrapperEl.classList.remove('border-rose-500', 'ring-2', 'ring-rose-500/50', 'border-slate-800');
                                            wrapperEl.classList.add('border-amber-500');
                                        }
                                        if (alertBanner) alertBanner.classList.add('hidden');
                                    } else {
                                        // Dưới 2s: Vẽ box xanh hợp lệ
                                        if (ctx && overlayCanvas) {
                                            drawFaceBoundingBox(ctx, flippedX, b.y, b.width, b.height, 'Thí sinh (Hợp lệ)', '#10b981', '#ffffff');
                                        }
                                        if (wrapperEl) {
                                            wrapperEl.classList.remove('border-rose-500', 'ring-2', 'ring-rose-500/50', 'border-amber-500');
                                            wrapperEl.classList.add('border-slate-800');
                                        }
                                        if (alertBanner) alertBanner.classList.add('hidden');
                                    }
                                } else {
                                    // THÍ SINH NHÌN TRỰC DIỆN MÀN HÌNH: RESET TIMER, VẼ BOX XANH, KHÔNG CHỤP ẢNH
                                    sustainedDirectionStartTime = null;
                                    currentSustainedDirection = null;

                                    if (ctx && overlayCanvas) {
                                        drawFaceBoundingBox(ctx, flippedX, b.y, b.width, b.height, 'Thí sinh (Hợp lệ)', '#10b981', '#ffffff');
                                    }
                                    if (wrapperEl) {
                                        wrapperEl.classList.remove('border-rose-500', 'ring-2', 'ring-rose-500/50', 'border-amber-500');
                                        wrapperEl.classList.add('border-slate-800');
                                    }
                                    if (alertBanner) alertBanner.classList.add('hidden');
                                }
                            } else {
                                if (ctx && overlayCanvas) {
                                    drawFaceBoundingBox(ctx, flippedX, b.y, b.width, b.height, 'Thí sinh (Hợp lệ)', '#10b981', '#ffffff');
                                }
                            }
                        } catch (lmErr) {
                            if (ctx && overlayCanvas) {
                                drawFaceBoundingBox(ctx, flippedX, b.y, b.width, b.height, 'Thí sinh (Hợp lệ)', '#10b981', '#ffffff');
                            }
                        }
                    } else {
                        currentSustainedDirection = null;
                        sustainedDirectionStartTime = null;
                        if (ctx && overlayCanvas) {
                            drawFaceBoundingBox(ctx, flippedX, b.y, b.width, b.height, 'Thí sinh (Hợp lệ)', '#10b981', '#ffffff');
                        }
                        if (wrapperEl) {
                            wrapperEl.classList.remove('border-rose-500', 'ring-2', 'ring-rose-500/50', 'border-amber-500');
                            wrapperEl.classList.add('border-slate-800');
                        }
                        if (alertBanner) alertBanner.classList.add('hidden');
                    }
                } catch (loopErr) {
                    // Suppress any error to prevent disrupting student
                }
            }, 400);
        }

        window.addEventListener('DOMContentLoaded', () => {
            @if(!empty($exam->require_face_verification) && empty($attempt->face_verified_at))
                // Proctor camera will start after face verification completes
            @else
                initProctorCamera();
            @endif
        });
        @endif

        @if($exam->require_face_verification && empty($attempt->face_verified_at))
        window.addEventListener('DOMContentLoaded', () => {
            openFaceVerifyModal({{ $exam->id }}, '{{ addslashes($exam->title) }}');
        });
        @endif

        // ====================================================
        // MÁY TÍNH KHOA HỌC (SCIENTIFIC CALCULATOR) LOGIC
        // ====================================================
        let calcExpression = '';
        let calcCurrent = '0';
        let calcAngleMode = 'deg'; // 'deg' or 'rad'
        let calcMemory = 0;
        let calcLastAnswer = 0;
        let isCalcMinimized = false;

        function toggleCalculator() {
            const el = document.getElementById('scientificCalculatorWidget');
            if (!el) return;
            if (el.classList.contains('hidden')) {
                el.classList.remove('hidden');
                document.getElementById('btnHeaderCalc')?.classList.add('bg-indigo-100', 'text-indigo-800', 'border-indigo-400');
            } else {
                el.classList.add('hidden');
                document.getElementById('btnHeaderCalc')?.classList.remove('bg-indigo-100', 'text-indigo-800', 'border-indigo-400');
            }
        }

        function minimizeCalculator() {
            const body = document.getElementById('calcBody');
            const icon = document.getElementById('calcMinIcon');
            if (!body) return;
            isCalcMinimized = !isCalcMinimized;
            if (isCalcMinimized) {
                body.classList.add('hidden');
                if (icon) icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>';
            } else {
                body.classList.remove('hidden');
                if (icon) icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>';
            }
        }

        function updateCalcDisplay() {
            const histEl = document.getElementById('calcHistory');
            const dispEl = document.getElementById('calcDisplay');
            if (histEl) histEl.innerText = calcExpression;
            if (dispEl) dispEl.innerText = calcCurrent || '0';
        }

        function calcInputDigit(d) {
            if (calcCurrent === '0' || calcCurrent === 'Lỗi cú pháp') {
                calcCurrent = d;
            } else {
                calcCurrent += d;
            }
            updateCalcDisplay();
        }

        function calcInputDecimal() {
            if (!calcCurrent.includes('.')) {
                calcCurrent = (calcCurrent || '0') + '.';
                updateCalcDisplay();
            }
        }

        function calcInputOperator(op) {
            if (calcCurrent) {
                calcExpression += (calcExpression ? ' ' : '') + calcCurrent + ' ' + op;
                calcCurrent = '';
            } else if (calcExpression) {
                calcExpression = calcExpression.trim().replace(/[\+\-\×\÷\%]$/, op);
            }
            updateCalcDisplay();
        }

        function calcInputFunction(fn) {
            if (fn === 'pow2') {
                calcCurrent = `(${calcCurrent || '0'})^2`;
            } else if (fn === 'powY') {
                calcExpression += (calcExpression ? ' ' : '') + (calcCurrent || '0') + ' ^';
                calcCurrent = '';
            } else if (fn === 'fact') {
                calcCurrent = `fact(${calcCurrent || '0'})`;
            } else if (fn === 'recip') {
                calcCurrent = `(1/(${calcCurrent || '0'}))`;
            } else if (fn === 'negate') {
                if (calcCurrent.startsWith('-')) {
                    calcCurrent = calcCurrent.substring(1);
                } else {
                    calcCurrent = '-' + (calcCurrent || '0');
                }
            } else {
                // Trig or logs or roots
                if (calcCurrent && calcCurrent !== '0') {
                    calcCurrent = `${fn}(${calcCurrent})`;
                } else {
                    calcExpression += (calcExpression ? ' ' : '') + `${fn}(`;
                    calcCurrent = '';
                }
            }
            updateCalcDisplay();
        }

        function calcInputConstant(c) {
            if (c === 'pi') calcCurrent = 'π';
            if (c === 'e') calcCurrent = 'e';
            updateCalcDisplay();
        }

        function calcInputParenthesis(p) {
            if (p === '(') {
                calcExpression += (calcExpression ? ' ' : '') + '(';
            } else {
                if (calcCurrent) {
                    calcExpression += (calcExpression ? ' ' : '') + calcCurrent + ')';
                    calcCurrent = '';
                } else {
                    calcExpression += ')';
                }
            }
            updateCalcDisplay();
        }

        function calcToggleAngleMode() {
            calcAngleMode = calcAngleMode === 'deg' ? 'rad' : 'deg';
            const badge = document.getElementById('calcAngleModeBadge');
            if (badge) {
                badge.innerText = calcAngleMode.toUpperCase();
                badge.className = calcAngleMode === 'deg' 
                    ? 'px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-900/80 text-indigo-300 border border-indigo-700/60 hover:bg-indigo-800'
                    : 'px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-900/80 text-amber-300 border border-amber-700/60 hover:bg-amber-800';
            }
        }

        function calcClearAll() {
            calcExpression = '';
            calcCurrent = '0';
            updateCalcDisplay();
        }

        function calcBackspace() {
            if (calcCurrent.length > 1) {
                calcCurrent = calcCurrent.slice(0, -1);
            } else {
                calcCurrent = '0';
            }
            updateCalcDisplay();
        }

        function calcMemoryClear() {
            calcMemory = 0;
            showToastAlert('Máy tính', 'Bộ nhớ tạm (Memory) đã xóa.', 'info');
        }

        function calcMemoryRecall() {
            calcCurrent = String(calcMemory);
            updateCalcDisplay();
        }

        function calcMemoryAdd() {
            calcMemory += parseFloat(calcCurrent) || 0;
            showToastAlert('Máy tính', `Đã cộng vào bộ nhớ: M = ${calcMemory}`, 'info');
        }

        function calcMemorySub() {
            calcMemory -= parseFloat(calcCurrent) || 0;
            showToastAlert('Máy tính', `Đã trừ khỏi bộ nhớ: M = ${calcMemory}`, 'info');
        }

        function calcInputAns() {
            calcCurrent = String(calcLastAnswer);
            updateCalcDisplay();
        }

        function calcEvaluate() {
            try {
                let fullExpr = (calcExpression + (calcCurrent ? ' ' + calcCurrent : '')).trim();
                if (!fullExpr) return;

                // Close any unclosed parentheses
                const openCount = (fullExpr.match(/\(/g) || []).length;
                const closeCount = (fullExpr.match(/\)/g) || []).length;
                if (openCount > closeCount) {
                    fullExpr += ')'.repeat(openCount - closeCount);
                }

                let parsed = fullExpr
                    .replace(/×/g, '*')
                    .replace(/÷/g, '/')
                    .replace(/π/g, `(${Math.PI})`)
                    .replace(/\be\b/g, `(${Math.E})`)
                    .replace(/\^/g, '**')
                    .replace(/(\d+(\.\d+)?)%/g, '($1 * 0.01)');

                // Factorial function
                function fact(n) {
                    n = Math.round(Number(n));
                    if (n < 0) return NaN;
                    if (n <= 1) return 1;
                    let r = 1;
                    for (let i = 2; i <= n; i++) r *= i;
                    return r;
                }

                // Custom Trig aware of DEG / RAD mode
                const isDeg = calcAngleMode === 'deg';
                function sin(x) { return isDeg ? Math.sin((x * Math.PI) / 180) : Math.sin(x); }
                function cos(x) { return isDeg ? Math.cos((x * Math.PI) / 180) : Math.cos(x); }
                function tan(x) { return isDeg ? Math.tan((x * Math.PI) / 180) : Math.tan(x); }
                function asin(x) { return isDeg ? (Math.asin(x) * 180) / Math.PI : Math.asin(x); }
                function acos(x) { return isDeg ? (Math.acos(x) * 180) / Math.PI : Math.acos(x); }
                function atan(x) { return isDeg ? (Math.atan(x) * 180) / Math.PI : Math.atan(x); }
                function sqrt(x) { return Math.sqrt(x); }
                function ln(x) { return Math.log(x); }
                function log(x) { return Math.log10(x); }

                // Safe evaluation with context
                const result = Function('sin', 'cos', 'tan', 'asin', 'acos', 'atan', 'sqrt', 'ln', 'log', 'fact', `return (${parsed});`)(
                    sin, cos, tan, asin, acos, atan, sqrt, ln, log, fact
                );

                if (typeof result === 'number' && !isNaN(result) && isFinite(result)) {
                    // Round float jitter
                    const cleanResult = Number(result.toFixed(8));
                    calcLastAnswer = cleanResult;
                    calcExpression = fullExpr + ' =';
                    calcCurrent = String(cleanResult);
                } else if (!isFinite(result)) {
                    calcCurrent = 'Không xác định';
                } else {
                    calcCurrent = 'Lỗi cú pháp';
                }
            } catch (e) {
                calcCurrent = 'Lỗi cú pháp';
            }
            updateCalcDisplay();
        }

        // ====================================================
        // GHI CHÚ & NHÁP THI CÁ NHÂN (PERSONAL NOTE) LOGIC
        // ====================================================
        const noteStorageKey = 'exam_scratchpad_{{ $attempt->id }}';

        function toggleNote() {
            const el = document.getElementById('personalNoteWidget');
            if (!el) return;
            if (el.classList.contains('hidden')) {
                el.classList.remove('hidden');
                document.getElementById('btnHeaderNote')?.classList.add('bg-amber-100', 'text-amber-900', 'border-amber-400');
                initNoteData();
            } else {
                el.classList.add('hidden');
                document.getElementById('btnHeaderNote')?.classList.remove('bg-amber-100', 'text-amber-900', 'border-amber-400');
            }
        }

        function initNoteData() {
            const noteEl = document.getElementById('examPersonalNote');
            if (!noteEl) return;
            if (!noteEl.dataset.initialized) {
                noteEl.value = localStorage.getItem(noteStorageKey) || '';
                noteEl.dataset.initialized = 'true';
                updateNoteCounts();
            }
        }

        function onNoteInput() {
            const noteEl = document.getElementById('examPersonalNote');
            if (!noteEl) return;
            localStorage.setItem(noteStorageKey, noteEl.value);
            updateNoteCounts();
            const status = document.getElementById('noteSaveStatus');
            if (status) {
                status.innerText = '✓ Đã lưu nháp';
                status.className = 'text-[10px] text-emerald-600 font-bold block';
            }
        }

        function updateNoteCounts() {
            const noteEl = document.getElementById('examPersonalNote');
            if (!noteEl) return;
            const val = noteEl.value;
            const charCount = val.length;
            const wordCount = val.trim() ? val.trim().split(/\s+/).length : 0;
            const charEl = document.getElementById('noteCharCount');
            const wordEl = document.getElementById('noteWordCount');
            if (charEl) charEl.innerText = charCount;
            if (wordEl) wordEl.innerText = wordCount;
        }

        function insertNoteSymbol(sym) {
            const noteEl = document.getElementById('examPersonalNote');
            if (!noteEl) return;
            const start = noteEl.selectionStart || 0;
            const end = noteEl.selectionEnd || 0;
            const val = noteEl.value;
            noteEl.value = val.substring(0, start) + sym + val.substring(end);
            noteEl.selectionStart = noteEl.selectionEnd = start + sym.length;
            noteEl.focus();
            onNoteInput();
        }

        function copyNoteContent() {
            const noteEl = document.getElementById('examPersonalNote');
            if (!noteEl || !noteEl.value.trim()) {
                showToastAlert('Thông báo', 'Bản nháp đang trống, vui lòng nhập nội dung nháp trước khi sao chép.', 'info');
                return;
            }
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(noteEl.value).then(() => {
                    showToastAlert('Đã sao chép nháp', 'Nội dung nháp đã lưu vào clipboard. Bạn có thể dán (Paste) vào ô bài làm.', 'success');
                }).catch(() => {
                    noteEl.select();
                    document.execCommand('copy');
                    showToastAlert('Đã sao chép nháp', 'Đã sao chép nội dung nháp. Bạn có thể dán (Paste) vào bài làm.', 'success');
                });
            } else {
                noteEl.select();
                document.execCommand('copy');
                showToastAlert('Đã sao chép nháp', 'Đã sao chép nội dung nháp. Bạn có thể dán (Paste) vào bài làm.', 'success');
            }
        }

        function clearNoteContent() {
            if (confirm('Bạn có chắc chắn muốn xóa toàn bộ nội dung trong bản nháp này không?')) {
                const noteEl = document.getElementById('examPersonalNote');
                if (noteEl) {
                    noteEl.value = '';
                    onNoteInput();
                    showToastAlert('Đã xóa', 'Bản nháp đã được làm sạch.', 'success');
                }
            }
        }

        // ====================================================
        // DRAGGABLE WINDOW UTILITY
        // ====================================================
        function makeDraggable(element, handle) {
            if (!element || !handle) return;
            let pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;
            handle.onmousedown = dragMouseDown;

            function dragMouseDown(e) {
                if (e.target.tagName === 'BUTTON' || e.target.closest('button')) return;
                e.preventDefault();
                pos3 = e.clientX;
                pos4 = e.clientY;
                document.onmouseup = closeDragElement;
                document.onmousemove = elementDrag;
            }

            function elementDrag(e) {
                e.preventDefault();
                pos1 = pos3 - e.clientX;
                pos2 = pos4 - e.clientY;
                pos3 = e.clientX;
                pos4 = e.clientY;

                element.style.right = 'auto';
                element.style.bottom = 'auto';

                const newTop = Math.max(10, Math.min(window.innerHeight - 80, element.offsetTop - pos2));
                const newLeft = Math.max(10, Math.min(window.innerWidth - element.offsetWidth - 10, element.offsetLeft - pos1));

                element.style.top = newTop + "px";
                element.style.left = newLeft + "px";
            }

            function closeDragElement() {
                document.onmouseup = null;
                document.onmousemove = null;
            }
        }

        window.addEventListener('DOMContentLoaded', () => {
            initNoteData();
            makeDraggable(document.getElementById('scientificCalculatorWidget'), document.getElementById('calcDragHandle'));
            makeDraggable(document.getElementById('personalNoteWidget'), document.getElementById('noteDragHandle'));
        });
    </script>

    <!-- SCIENTIFIC CALCULATOR FLOATING WIDGET -->
    <div id="scientificCalculatorWidget" class="fixed top-20 right-6 z-40 hidden bg-slate-900 text-slate-100 rounded-3xl shadow-2xl border border-slate-700/80 overflow-hidden w-[360px] max-w-[95vw] select-none transition-all duration-200">
        <!-- Draggable Header -->
        <div id="calcDragHandle" class="bg-slate-800/90 px-4 py-3 border-b border-slate-700/80 flex items-center justify-between cursor-move">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-indigo-600/80 text-white flex items-center justify-center font-bold text-xs">
                    fx
                </div>
                <span class="font-bold text-xs tracking-wide text-slate-200">Máy tính khoa học</span>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="calcToggleAngleMode()" id="calcAngleModeBadge" 
                    class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-900/80 text-indigo-300 border border-indigo-700/60 hover:bg-indigo-800" title="Chuyển đổi Độ (DEG) / Radian (RAD)">
                    DEG
                </button>
                <button type="button" onclick="minimizeCalculator()" class="p-1 text-slate-400 hover:text-white rounded hover:bg-slate-700" title="Thu nhỏ/Mở rộng">
                    <svg id="calcMinIcon" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <button type="button" onclick="toggleCalculator()" class="p-1 text-slate-400 hover:text-rose-400 rounded hover:bg-slate-700" title="Đóng">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        </div>

        <div id="calcBody" class="p-4 space-y-3">
            <!-- Screen Display -->
            <div class="bg-black/60 rounded-2xl p-3 border border-slate-800 text-right">
                <div id="calcHistory" class="text-xs text-slate-400 font-mono h-5 truncate tracking-wide"></div>
                <div id="calcDisplay" class="text-2xl font-mono font-black text-white truncate tracking-tight">0</div>
            </div>

            <!-- Memory & Ans Bar -->
            <div class="grid grid-cols-5 gap-1.5 text-[11px] font-bold">
                <button type="button" onclick="calcMemoryClear()" class="py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white transition-colors">MC</button>
                <button type="button" onclick="calcMemoryRecall()" class="py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white transition-colors">MR</button>
                <button type="button" onclick="calcMemoryAdd()" class="py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white transition-colors">M+</button>
                <button type="button" onclick="calcMemorySub()" class="py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white transition-colors">M-</button>
                <button type="button" onclick="calcInputAns()" class="py-1.5 rounded-lg bg-indigo-900/60 hover:bg-indigo-800 text-indigo-300 transition-colors">Ans</button>
            </div>

            <!-- Scientific Functions Grid -->
            <div class="grid grid-cols-6 gap-1.5 text-[11px] font-semibold text-slate-300">
                <button type="button" onclick="calcInputFunction('sin')" class="py-2 rounded-xl bg-slate-800/80 hover:bg-indigo-900/60 hover:text-indigo-200 transition-colors">sin</button>
                <button type="button" onclick="calcInputFunction('cos')" class="py-2 rounded-xl bg-slate-800/80 hover:bg-indigo-900/60 hover:text-indigo-200 transition-colors">cos</button>
                <button type="button" onclick="calcInputFunction('tan')" class="py-2 rounded-xl bg-slate-800/80 hover:bg-indigo-900/60 hover:text-indigo-200 transition-colors">tan</button>
                <button type="button" onclick="calcInputConstant('pi')" class="py-2 rounded-xl bg-slate-800/80 hover:bg-indigo-900/60 hover:text-indigo-200 transition-colors">π</button>
                <button type="button" onclick="calcInputConstant('e')" class="py-2 rounded-xl bg-slate-800/80 hover:bg-indigo-900/60 hover:text-indigo-200 transition-colors">e</button>
                <button type="button" onclick="calcInputFunction('fact')" class="py-2 rounded-xl bg-slate-800/80 hover:bg-indigo-900/60 hover:text-indigo-200 transition-colors">x!</button>

                <button type="button" onclick="calcInputFunction('asin')" class="py-2 rounded-xl bg-slate-800/80 hover:bg-indigo-900/60 hover:text-indigo-200 transition-colors">sin⁻¹</button>
                <button type="button" onclick="calcInputFunction('acos')" class="py-2 rounded-xl bg-slate-800/80 hover:bg-indigo-900/60 hover:text-indigo-200 transition-colors">cos⁻¹</button>
                <button type="button" onclick="calcInputFunction('atan')" class="py-2 rounded-xl bg-slate-800/80 hover:bg-indigo-900/60 hover:text-indigo-200 transition-colors">tan⁻¹</button>
                <button type="button" onclick="calcInputFunction('sqrt')" class="py-2 rounded-xl bg-slate-800/80 hover:bg-indigo-900/60 hover:text-indigo-200 transition-colors">√</button>
                <button type="button" onclick="calcInputFunction('pow2')" class="py-2 rounded-xl bg-slate-800/80 hover:bg-indigo-900/60 hover:text-indigo-200 transition-colors">x²</button>
                <button type="button" onclick="calcInputFunction('powY')" class="py-2 rounded-xl bg-slate-800/80 hover:bg-indigo-900/60 hover:text-indigo-200 transition-colors">xʸ</button>

                <button type="button" onclick="calcInputFunction('ln')" class="py-2 rounded-xl bg-slate-800/80 hover:bg-indigo-900/60 hover:text-indigo-200 transition-colors">ln</button>
                <button type="button" onclick="calcInputFunction('log')" class="py-2 rounded-xl bg-slate-800/80 hover:bg-indigo-900/60 hover:text-indigo-200 transition-colors">log</button>
                <button type="button" onclick="calcInputFunction('recip')" class="py-2 rounded-xl bg-slate-800/80 hover:bg-indigo-900/60 hover:text-indigo-200 transition-colors">1/x</button>
                <button type="button" onclick="calcInputParenthesis('(')" class="py-2 rounded-xl bg-slate-800/80 hover:bg-indigo-900/60 hover:text-indigo-200 transition-colors">(</button>
                <button type="button" onclick="calcInputParenthesis(')')" class="py-2 rounded-xl bg-slate-800/80 hover:bg-indigo-900/60 hover:text-indigo-200 transition-colors">)</button>
                <button type="button" onclick="calcInputFunction('negate')" class="py-2 rounded-xl bg-slate-800/80 hover:bg-indigo-900/60 hover:text-indigo-200 transition-colors">±</button>
            </div>

            <!-- Main Keypad (Digits & Operations) -->
            <div class="grid grid-cols-4 gap-1.5 pt-1 text-sm font-bold">
                <button type="button" onclick="calcClearAll()" class="py-2.5 rounded-xl bg-rose-950/70 hover:bg-rose-900 text-rose-300 transition-colors">AC</button>
                <button type="button" onclick="calcBackspace()" class="py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 transition-colors">⌫</button>
                <button type="button" onclick="calcInputOperator('%')" class="py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 transition-colors">%</button>
                <button type="button" onclick="calcInputOperator('÷')" class="py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white transition-colors">÷</button>

                <button type="button" onclick="calcInputDigit('7')" class="py-2.5 rounded-xl bg-slate-800/90 hover:bg-slate-700 text-white transition-colors">7</button>
                <button type="button" onclick="calcInputDigit('8')" class="py-2.5 rounded-xl bg-slate-800/90 hover:bg-slate-700 text-white transition-colors">8</button>
                <button type="button" onclick="calcInputDigit('9')" class="py-2.5 rounded-xl bg-slate-800/90 hover:bg-slate-700 text-white transition-colors">9</button>
                <button type="button" onclick="calcInputOperator('×')" class="py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white transition-colors">×</button>

                <button type="button" onclick="calcInputDigit('4')" class="py-2.5 rounded-xl bg-slate-800/90 hover:bg-slate-700 text-white transition-colors">4</button>
                <button type="button" onclick="calcInputDigit('5')" class="py-2.5 rounded-xl bg-slate-800/90 hover:bg-slate-700 text-white transition-colors">5</button>
                <button type="button" onclick="calcInputDigit('6')" class="py-2.5 rounded-xl bg-slate-800/90 hover:bg-slate-700 text-white transition-colors">6</button>
                <button type="button" onclick="calcInputOperator('-')" class="py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white transition-colors">−</button>

                <button type="button" onclick="calcInputDigit('1')" class="py-2.5 rounded-xl bg-slate-800/90 hover:bg-slate-700 text-white transition-colors">1</button>
                <button type="button" onclick="calcInputDigit('2')" class="py-2.5 rounded-xl bg-slate-800/90 hover:bg-slate-700 text-white transition-colors">2</button>
                <button type="button" onclick="calcInputDigit('3')" class="py-2.5 rounded-xl bg-slate-800/90 hover:bg-slate-700 text-white transition-colors">3</button>
                <button type="button" onclick="calcInputOperator('+')" class="py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white transition-colors">+</button>

                <button type="button" onclick="calcInputDigit('0')" class="col-span-2 py-2.5 rounded-xl bg-slate-800/90 hover:bg-slate-700 text-white transition-colors">0</button>
                <button type="button" onclick="calcInputDecimal()" class="py-2.5 rounded-xl bg-slate-800/90 hover:bg-slate-700 text-white transition-colors">.</button>
                <button type="button" onclick="calcEvaluate()" class="py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white transition-colors shadow-md">=</button>
            </div>
        </div>
    </div>

    <!-- PERSONAL NOTE / SCRATCHPAD FLOATING WIDGET -->
    <div id="personalNoteWidget" class="fixed top-20 right-6 z-40 hidden bg-white text-slate-800 rounded-3xl shadow-2xl border border-slate-200 overflow-hidden w-[420px] max-w-[95vw] flex flex-col select-none transition-all duration-200">
        <!-- Draggable Header -->
        <div id="noteDragHandle" class="bg-amber-50/90 px-4 py-3 border-b border-amber-100 flex items-center justify-between cursor-move">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-amber-500 text-white flex items-center justify-center font-bold text-xs shadow-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                </div>
                <div>
                    <h4 class="font-bold text-xs text-amber-950">Ghi chú & Nháp thi</h4>
                    <span id="noteSaveStatus" class="text-[10px] text-emerald-600 font-bold block">✓ Đã tự động lưu</span>
                </div>
            </div>
            <div class="flex items-center gap-1.5">
                <button type="button" onclick="copyNoteContent()" class="px-2 py-1 rounded-lg text-[10px] font-bold bg-white text-amber-900 border border-amber-200 hover:bg-amber-100 transition-colors flex items-center gap-1 shadow-xs" title="Sao chép toàn bộ nháp để dán vào bài thi">
                    <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                    <span>Sao chép nháp</span>
                </button>
                <button type="button" onclick="clearNoteContent()" class="px-2 py-1 rounded-lg text-[10px] font-bold bg-white text-slate-700 border border-slate-200 hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200 transition-colors flex items-center gap-1" title="Xóa toàn bộ nội dung nháp">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    <span>Xóa nháp</span>
                </button>
                <button type="button" onclick="toggleNote()" class="p-1 text-slate-400 hover:text-rose-600 rounded hover:bg-amber-100" title="Đóng">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        </div>

        <!-- Symbol Quick Insertion Toolbar -->
        <div class="px-3 py-1.5 bg-amber-50/40 border-b border-amber-100 flex items-center gap-1 overflow-x-auto text-xs font-mono text-slate-700">
            <span class="text-[10px] text-amber-800/80 font-sans font-bold shrink-0 mr-1">Chèn nhanh:</span>
            @foreach(['π', '√', 'θ', 'α', 'β', 'Δ', '∞', '≠', '≤', '≥', '±', '×', '÷', '²', '³', '℃'] as $sym)
                <button type="button" onclick="insertNoteSymbol('{{ $sym }}')" class="px-1.5 py-0.5 rounded bg-white hover:bg-amber-200 border border-amber-200/80 text-slate-800 font-bold shrink-0 transition-colors">
                    {{ $sym }}
                </button>
            @endforeach
        </div>

        <!-- Textarea (Cho phép sao chép nháp và dán vào bài thi) -->
        <div class="p-3 bg-amber-50/20">
            <textarea id="examPersonalNote" rows="12" oninput="onNoteInput()"
                class="w-full bg-white border border-slate-200 rounded-2xl p-3.5 text-xs text-slate-800 font-mono leading-relaxed placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-400 resize-y"
                placeholder="Nhập công thức nháp, ghi chú bài thi tại đây... (Được phép sao chép nháp và dán vào bài thi lúc đang làm bài)"></textarea>
        </div>

        <!-- Footer Stats -->
        <div class="px-4 py-2 bg-slate-50 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
            <div class="flex items-center gap-3 font-medium">
                <span>Số từ: <strong id="noteWordCount" class="text-slate-800 font-bold">0</strong></span>
                <span>•</span>
                <span>Ký tự: <strong id="noteCharCount" class="text-slate-800 font-bold">0</strong></span>
            </div>
            <span class="text-[10px] text-emerald-600 font-bold">✓ Được phép sao chép nháp & dán vào bài</span>
        </div>
    </div>

    <!-- AI Proctoring Floating PIP Widget (Cố định vĩnh viễn ở góc dưới bên trái) -->
    @if($exam->enable_proctor_camera ?? true)
    <div id="proctorPipWidget" class="fixed bottom-6 left-6 z-40 bg-slate-900/90 backdrop-blur-md text-white rounded-2xl shadow-2xl border border-slate-700/80 p-3 overflow-hidden transition-all duration-300 w-52 sm:w-60 pointer-events-auto">
        <div class="flex items-center justify-between pb-2 mb-2 border-b border-slate-800 text-[11px] font-bold">
            <div class="flex items-center gap-1.5">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                </span>
                <span class="text-slate-200">Giám sát AI</span>
            </div>
            <button type="button" onclick="toggleProctorPip()" id="pipToggleBtn" class="p-1 text-slate-400 hover:text-white rounded hover:bg-slate-800 transition-colors" title="Thu nhỏ/Phóng to">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>
        </div>
        
        <div id="pipBody" class="space-y-2">
            <div id="proctorVideoWrapper" class="relative aspect-[4/3] rounded-xl overflow-hidden bg-black border border-slate-800 transition-all duration-300 shadow-inner">
                <!-- Camera Video Feed (Mirrored via CSS) -->
                <video id="proctorVideo" autoplay playsinline muted class="w-full h-full object-cover transform -scale-x-100"></video>
                
                <!-- Bounding Box & HUD Canvas Overlay -->
                <canvas id="proctorOverlayCanvas" class="absolute inset-0 w-full h-full pointer-events-none"></canvas>

                <!-- Face Count HUD Badge (Top Right) -->
                <div id="proctorFaceCountBadge" class="absolute top-1.5 right-1.5 px-2 py-0.5 bg-black/75 backdrop-blur-md rounded-md text-[9px] font-bold text-emerald-400 flex items-center gap-1.5 transition-all z-10 border border-white/10 shadow-sm">
                    <span id="proctorFaceCountDot" class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                    <span id="proctorFaceCountText">1 khuôn mặt</span>
                </div>

                <!-- Alert Overlay Banner (Appears when 0 or >1 faces) -->
                <div id="proctorAlertBanner" class="hidden absolute inset-x-0 bottom-0 bg-rose-600/95 backdrop-blur-xs px-2 py-1 text-center text-[10px] font-bold text-white transition-all z-10 flex items-center justify-center gap-1 shadow-lg animate-pulse">
                    <svg class="w-3 h-3 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <span id="proctorAlertText">Cảnh báo: 0 khuôn mặt!</span>
                </div>

                <!-- Live Camera Indicator (Bottom Left) -->
                <div id="proctorStatusBadge" class="absolute bottom-1.5 left-1.5 px-1.5 py-0.5 bg-black/60 backdrop-blur-xs rounded text-[9px] font-semibold text-emerald-400 flex items-center gap-1 z-10">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Trực tiếp</span>
                </div>
            </div>
        </div>
    </div>
    <canvas id="proctorOffscreenCanvas" class="hidden"></canvas>
    @endif

    @include('student.exams.partials.face_verify_modal')
</body>
</html>
