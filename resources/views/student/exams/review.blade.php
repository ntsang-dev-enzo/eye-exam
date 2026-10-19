<!DOCTYPE html>
<html lang="vi" class="bg-slate-50 h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xem lại bài thi: {{ $exam->title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans text-slate-800 antialiased overflow-hidden flex flex-col">
    @php
        $canViewScore = (bool) ($exam->allow_view_score ?? true);
        $totalQuestions = $exam->questions->count();
    @endphp

    <!-- Header -->
    <header class="bg-white border-b border-slate-200 px-4 sm:px-6 py-3 flex flex-wrap items-center justify-between gap-4 shrink-0 shadow-xs z-10 relative">
        <div class="flex items-center gap-3 sm:gap-4">
            <a href="{{ route('student.exams.index') }}" class="p-2 rounded-xl text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-colors" title="Quay lại danh sách bài thi">
                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h1 class="font-extrabold text-base sm:text-lg text-slate-900 leading-tight">Xem lại: {{ $exam->title }}</h1>
                    @if($exam->semester)
                        <span class="px-2 py-0.5 rounded-md text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                            {{ $exam->semester }}
                        </span>
                    @endif
                </div>
                <div class="flex items-center gap-2.5 text-xs text-slate-500 font-medium mt-1 flex-wrap">
                    @if($canViewScore)
                        <span class="uppercase tracking-wider font-semibold">Điểm số: <strong class="text-blue-600 font-extrabold text-sm">{{ $attempt->score_value !== null ? $attempt->score_value . ' / 10' : 'Chưa có' }}</strong></span>
                    @else
                        <span class="text-amber-700 bg-amber-50 px-2 py-0.5 rounded font-semibold flex items-center gap-1 border border-amber-200/60">
                            <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            Giảng viên chưa công bố điểm
                        </span>
                    @endif
                    <span class="text-slate-300">•</span>
                    <span>Nộp lúc: <strong class="text-slate-700 font-mono">{{ $attempt->submitted_at ? $attempt->submitted_at->format('H:i:s d/m/Y') : 'N/A' }}</strong></span>
                </div>
            </div>
        </div>

        <!-- Attempt Switcher -->
        @if(isset($allAttempts) && $allAttempts->count() > 1)
            <div class="flex items-center gap-2 bg-slate-50 px-3 py-1.5 rounded-xl border border-slate-200">
                <label for="attemptSwitcher" class="text-xs text-slate-500 font-semibold whitespace-nowrap flex items-center gap-1">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>Lần thi:</span>
                </label>
                <select id="attemptSwitcher" onchange="window.location.href = this.value" class="text-xs rounded-lg border-slate-300 py-1 pl-2.5 pr-7 font-bold text-slate-800 bg-white focus:ring-blue-500 focus:border-blue-500">
                    @foreach($allAttempts as $idx => $att)
                        <option value="{{ route('student.exams.review', ['exam' => $exam->id, 'attempt_id' => $att->id]) }}" {{ $att->id == $attempt->id ? 'selected' : '' }}>
                            Lần {{ $idx + 1 }} ({{ $att->submitted_at ? $att->submitted_at->format('H:i d/m') : '#' . $att->id }})
                            @if($canViewScore && $att->score_value !== null) - {{ $att->score_value }}đ @endif
                        </option>
                    @endforeach
                </select>
            </div>
        @endif
    </header>

    <!-- Main Content -->
    <div class="flex-1 flex overflow-hidden">
        <!-- Questions Area (Left) -->
        <div class="flex-1 overflow-y-auto scroll-smooth bg-slate-50 p-4 sm:p-6 md:p-10 relative" id="questionsArea">
            <div class="max-w-4xl mx-auto space-y-6 sm:space-y-8 pb-32">
                @foreach($exam->questions as $index => $question)
                    @php
                        $studentAns = $studentAnswers->get($question->id);
                        $isCorrect = $studentAns ? (bool) $studentAns->is_correct : false;
                        
                        // Border styling
                        if ($canViewScore) {
                            $cardBorder = $isCorrect ? 'border-emerald-200 shadow-emerald-500/5' : 'border-rose-200 shadow-rose-500/5';
                            $badgeColor = $isCorrect ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-rose-50 text-rose-600 border-rose-100';
                        } else {
                            $cardBorder = 'border-slate-200 shadow-slate-500/5';
                            $badgeColor = 'bg-blue-50 text-blue-700 border-blue-100';
                        }
                    @endphp
                    <div id="q-{{ $index + 1 }}" class="bg-white rounded-3xl p-6 sm:p-8 shadow-xs border {{ $cardBorder }} question-block transition-all duration-300">
                        <div class="flex items-start gap-4">
                            <!-- Question Number Badge -->
                            <div class="shrink-0 flex flex-col items-center justify-center w-11 h-11 sm:w-12 sm:h-12 {{ $badgeColor }} rounded-2xl border font-black text-base sm:text-lg">
                                {{ $index + 1 }}
                            </div>
                            
                            <div class="flex-1 pt-0.5">
                                <!-- Question Content -->
                                <div class="text-base sm:text-lg text-slate-800 font-medium leading-relaxed mb-6">
                                    {!! nl2br(e($question->content)) !!}
                                    @if($canViewScore)
                                        <span class="text-xs sm:text-sm font-normal text-slate-400 ml-2">({{ $question->pivot->points }} điểm)</span>
                                    @endif
                                </div>

                                <!-- Answers -->
                                @if($question->type === 'multiple_choice')
                                    <div class="space-y-3">
                                        @foreach($question->answers as $ansIndex => $answer)
                                            @php
                                                $isSelected = $studentAns && $studentAns->answer_id == $answer->id;
                                                $isTrueAnswer = (bool) $answer->is_correct;
                                                
                                                $bgClass = 'bg-white';
                                                $borderClass = 'border-slate-200';
                                                
                                                if ($canViewScore) {
                                                    if ($isSelected && $isTrueAnswer) {
                                                        $bgClass = 'bg-emerald-50';
                                                        $borderClass = 'border-emerald-500 ring-1 ring-emerald-500';
                                                    } elseif ($isSelected && !$isTrueAnswer) {
                                                        $bgClass = 'bg-rose-50';
                                                        $borderClass = 'border-rose-500 ring-1 ring-rose-500';
                                                    } elseif (!$isSelected && $isTrueAnswer) {
                                                        $bgClass = 'bg-emerald-50/30';
                                                        $borderClass = 'border-emerald-300 border-dashed';
                                                    }
                                                } else {
                                                    // Do not reveal correct answer
                                                    if ($isSelected) {
                                                        $bgClass = 'bg-blue-50';
                                                        $borderClass = 'border-blue-500 ring-1 ring-blue-500';
                                                    }
                                                }
                                            @endphp
                                            <div class="flex items-start p-3.5 sm:p-4 rounded-xl border {{ $borderClass }} {{ $bgClass }} transition-colors">
                                                <div class="flex items-center h-6">
                                                    <input type="radio" disabled {{ $isSelected ? 'checked' : '' }}
                                                        class="w-4 h-4 text-blue-600 border-slate-300">
                                                </div>
                                                <div class="ml-3 flex-1">
                                                    <span class="block text-slate-700 font-medium leading-relaxed text-sm sm:text-base">{{ $answer->content }}</span>
                                                </div>
                                                @if($canViewScore && $isTrueAnswer)
                                                    <div class="ml-3 shrink-0">
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-100 text-emerald-800">
                                                            Đáp án đúng
                                                        </span>
                                                    </div>
                                                @elseif(!$canViewScore && $isSelected)
                                                    <div class="ml-3 shrink-0">
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-blue-100 text-blue-800">
                                                            Đã chọn
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50/80">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Bài làm tự luận:</span>
                                            <span class="text-xs text-blue-600 font-semibold bg-blue-50 px-2 py-0.5 rounded">Tự luận</span>
                                        </div>
                                        <p class="text-slate-800 whitespace-pre-wrap text-sm leading-relaxed font-normal">{{ $studentAns->text_answer ?? $studentAns->answer_text ?? 'Không có câu trả lời' }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Navigator Sidebar (Right) -->
        <div class="w-80 bg-white border-l border-slate-200 flex flex-col shrink-0 shadow-[-4px_0_15px_-3px_rgba(0,0,0,0.05)] z-20 hidden md:flex">
            <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                <h3 class="font-bold text-slate-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    Bảng câu hỏi
                </h3>
            </div>
            
            <div class="flex-1 overflow-y-auto p-6">
                <div class="grid grid-cols-5 gap-3" id="navigatorGrid">
                    @foreach($exam->questions as $index => $question)
                        @php
                            $studentAns = $studentAnswers->get($question->id);
                            $isCorrect = $studentAns ? (bool) $studentAns->is_correct : false;
                            
                            if ($canViewScore) {
                                $btnClass = 'border-slate-200 text-slate-400 bg-white';
                                if ($studentAns) {
                                    $btnClass = $isCorrect ? 'border-emerald-500 bg-emerald-50 text-emerald-700' : 'border-rose-500 bg-rose-50 text-rose-700';
                                }
                            } else {
                                $hasAnswer = false;
                                if ($studentAns) {
                                    $hasAnswer = !empty($studentAns->answer_id) || !empty($studentAns->text_answer);
                                }
                                $btnClass = $hasAnswer ? 'border-blue-500 bg-blue-50 text-blue-700 font-bold' : 'border-slate-200 text-slate-400 bg-white';
                            }
                        @endphp
                        <button type="button" onclick="scrollToQuestion({{ $index + 1 }})" 
                            class="aspect-square flex items-center justify-center rounded-xl border font-bold text-sm transition-all hover:opacity-80 {{ $btnClass }}">
                            {{ $index + 1 }}
                        </button>
                    @endforeach
                </div>
            </div>
            
            <div class="p-6 bg-slate-50 border-t border-slate-200">
                @if($canViewScore)
                    <div class="flex flex-col gap-2.5 text-sm text-slate-600 font-medium">
                        <div class="flex justify-between items-center">
                            <span class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-emerald-500"></span> Đúng</span>
                            <span class="font-bold text-emerald-700">{{ $attempt->correct_answers }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-rose-500"></span> Sai</span>
                            <span class="font-bold text-rose-700">{{ $attempt->wrong_answers }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-slate-300"></span> Bỏ trống</span>
                            <span class="font-bold text-slate-600">{{ $attempt->unanswered }}</span>
                        </div>
                    </div>
                @else
                    <div class="space-y-2 text-xs">
                        <div class="p-3 bg-amber-50 rounded-xl border border-amber-200/70 text-amber-800">
                            <p class="font-bold flex items-center gap-1.5 mb-1">
                                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Chờ công bố điểm
                            </p>
                            <p class="text-[11px] leading-relaxed">Giảng viên tạm thời chưa mở xem điểm chi tiết cho bài thi này.</p>
                        </div>
                        <div class="flex justify-between items-center pt-2 text-slate-600 font-medium">
                            <span>Tổng số câu hỏi:</span>
                            <span class="font-bold text-slate-900">{{ $totalQuestions }} câu</span>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        function scrollToQuestion(index) {
            const el = document.getElementById('q-' + index);
            if(el) {
                el.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }
    </script>
</body>
</html>
