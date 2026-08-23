<!DOCTYPE html>
<html lang="vi" class="bg-slate-50 h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Làm bài thi: {{ $exam->title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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

        <div class="flex items-center gap-6">
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

        // Remove no-select if copy/paste is permitted
        if (!antiCheatConfig.preventCopyPaste) {
            document.body.classList.remove('no-select');
            document.querySelectorAll('.no-select').forEach(el => el.classList.remove('no-select'));
        }

        function sendAntiCheatLog(eventType, eventData = null, durationSeconds = null) {
            if (!antiCheatConfig.enabled) return;

            const payload = {
                event_type: eventType,
                event_data: eventData,
                duration_seconds: durationSeconds
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

        // Disable Copy, Paste, Cut
        if (antiCheatConfig.preventCopyPaste) {
            document.addEventListener('copy', event => {
                event.preventDefault();
                sendAntiCheatLog('copy', { action: 'copy_text' });
                showToastAlert('Cảnh báo vi phạm', 'Hành vi sao chép (Copy) nội dung bài thi bị cấm.', 'danger');
            });

            document.addEventListener('paste', event => {
                event.preventDefault();
                sendAntiCheatLog('paste', { action: 'paste_text' });
                showToastAlert('Cảnh báo vi phạm', 'Hành vi dán (Paste) nội dung bị cấm trong phòng thi.', 'danger');
            });

            document.addEventListener('cut', event => {
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
            if (examBody.requestFullscreen) {
                examBody.requestFullscreen().then(() => {
                    const fs = document.getElementById('fsOverlay');
                    if (fs) fs.remove();
                    sendAntiCheatLog('fullscreen_enter');
                }).catch(err => {
                    console.log('Fullscreen error:', err.message);
                });
            }
        }

        if (antiCheatConfig.requireFullscreen) {
            document.addEventListener('DOMContentLoaded', () => {
                checkAndEnforceFullscreen();
            });

            document.addEventListener('fullscreenchange', () => {
                if (!document.fullscreenElement && !isSubmitting) {
                    checkAndEnforceFullscreen();
                    handleOutScreen('fullscreen_exit');
                    showToastAlert('Thoát toàn màn hình', 'Bạn vừa rời chế độ toàn màn hình. Vui lòng bật lại để tiếp tục làm bài.', 'danger');
                } else if (document.fullscreenElement) {
                    const fs = document.getElementById('fsOverlay');
                    if (fs) fs.remove();
                    handleInScreen('fullscreen_enter');
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
            if (!antiCheatConfig.preventTabSwitch) return;

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
            if (!antiCheatConfig.preventTabSwitch) return;

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

        window.addEventListener('beforeunload', function (e) {
            if (timeRemaining > 0 && !isSubmitting) {
                e.preventDefault();
                e.returnValue = 'Bạn có chắc chắn muốn rời khỏi trang? Bài làm của bạn có thể chưa được nộp hoàn tất.';
            }
        });
    </script>
</body>
</html>
