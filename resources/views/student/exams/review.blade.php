<!DOCTYPE html>
<html lang="vi" class="bg-slate-50 h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xem lại bài thi: {{ $exam->title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans text-slate-800 antialiased overflow-hidden flex flex-col">
    <!-- Header -->
    <header class="bg-white border-b border-slate-200 px-6 py-3 flex items-center justify-between shrink-0 shadow-sm z-10 relative">
        <div class="flex items-center gap-4">
            <a href="{{ route('student.exams.index') }}" class="p-2 rounded-xl text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h1 class="font-bold text-lg leading-tight">Xem lại: {{ $exam->title }}</h1>
                <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">Điểm của bạn: {{ $attempt->score_value }} / 10</p>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="flex-1 flex overflow-hidden">
        <!-- Questions Area (Left) -->
        <div class="flex-1 overflow-y-auto scroll-smooth bg-slate-50 p-6 md:p-10 relative" id="questionsArea">
            <div class="max-w-4xl mx-auto space-y-8 pb-32">
                @foreach($exam->questions as $index => $question)
                    @php
                        $studentAns = $studentAnswers->get($question->id);
                        $isCorrect = $studentAns ? $studentAns->is_correct : false;
                    @endphp
                    <div id="q-{{ $index + 1 }}" class="bg-white rounded-3xl p-8 shadow-sm border {{ $isCorrect ? 'border-emerald-200' : 'border-rose-200' }} question-block transition-all duration-300">
                        <div class="flex items-start gap-4">
                            <!-- Question Number Badge -->
                            <div class="shrink-0 flex flex-col items-center justify-center w-12 h-12 {{ $isCorrect ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-rose-50 text-rose-600 border-rose-100' }} rounded-2xl border font-black text-lg">
                                {{ $index + 1 }}
                            </div>
                            
                            <div class="flex-1 pt-1">
                                <!-- Question Content -->
                                <div class="text-lg text-slate-800 font-medium leading-relaxed mb-6">
                                    {!! nl2br(e($question->content)) !!}
                                    <span class="text-sm font-normal text-slate-400 ml-2">({{ $question->pivot->points }} điểm)</span>
                                </div>

                                <!-- Answers -->
                                @if($question->type === 'multiple_choice')
                                    <div class="space-y-3">
                                        @foreach($question->answers as $ansIndex => $answer)
                                            @php
                                                $isSelected = $studentAns && $studentAns->answer_id == $answer->id;
                                                $isTrueAnswer = $answer->is_correct;
                                                
                                                $bgClass = 'bg-white';
                                                $borderClass = 'border-slate-200';
                                                
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
                                            @endphp
                                            <div class="flex items-start p-4 rounded-xl border {{ $borderClass }} {{ $bgClass }} transition-colors">
                                                <div class="flex items-center h-6">
                                                    <input type="radio" disabled {{ $isSelected ? 'checked' : '' }}
                                                        class="w-5 h-5 text-indigo-600 border-slate-300">
                                                </div>
                                                <div class="ml-3 flex-1">
                                                    <span class="block text-slate-700 font-medium leading-relaxed">{{ $answer->content }}</span>
                                                </div>
                                                @if($isTrueAnswer)
                                                    <div class="ml-3 shrink-0">
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-100 text-emerald-800">
                                                            Đáp án đúng
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50">
                                        <p class="text-slate-700 whitespace-pre-wrap">{{ $studentAns->text_answer ?? 'Không có câu trả lời' }}</p>
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
                <h3 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    Bảng câu hỏi
                </h3>
            </div>
            
            <div class="flex-1 overflow-y-auto p-6">
                <div class="grid grid-cols-5 gap-3" id="navigatorGrid">
                    @foreach($exam->questions as $index => $question)
                        @php
                            $studentAns = $studentAnswers->get($question->id);
                            $isCorrect = $studentAns ? $studentAns->is_correct : false;
                            
                            $btnClass = 'border-slate-200 text-slate-400 bg-white';
                            if ($studentAns) {
                                $btnClass = $isCorrect ? 'border-emerald-500 bg-emerald-50 text-emerald-700' : 'border-rose-500 bg-rose-50 text-rose-700';
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
                <div class="flex flex-col gap-2 text-sm text-slate-600 font-medium">
                    <div class="flex justify-between items-center">
                        <span class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-emerald-500"></span> Đúng</span>
                        <span>{{ $attempt->correct_answers }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-rose-500"></span> Sai</span>
                        <span>{{ $attempt->wrong_answers }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-slate-300"></span> Bỏ trống</span>
                        <span>{{ $attempt->unanswered }}</span>
                    </div>
                </div>
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
