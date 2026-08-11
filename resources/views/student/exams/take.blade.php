<!DOCTYPE html>
<html lang="vi" class="bg-slate-50 h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Làm bài thi: {{ $exam->title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans text-slate-800 antialiased overflow-hidden flex flex-col">

    <!-- Header -->
    <header class="bg-white border-b border-slate-200 px-6 py-3 flex items-center justify-between shrink-0 shadow-sm z-10 relative">
        <div class="flex items-center gap-4">
            <div class="bg-indigo-600 text-white p-2 rounded-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
            </div>
            <div>
                <h1 class="font-bold text-lg leading-tight">{{ $exam->title }}</h1>
                <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">{{ $exam->subject->name }}</p>
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

    <!-- Main Content -->
    <div class="flex-1 flex overflow-hidden">
        
        <!-- Questions Area (Left) -->
        <div class="flex-1 overflow-y-auto scroll-smooth bg-slate-50 p-6 md:p-10 relative" id="questionsArea">
            <form id="examForm" action="{{ route('student.exams.submit', $exam) }}" method="POST" class="max-w-4xl mx-auto space-y-8 pb-32">
                @csrf
                
                @php 
                    $questions = $exam->questions;
                    if($exam->shuffle_questions) $questions = $questions->shuffle(); 
                @endphp

                @foreach($questions as $index => $question)
                    <div id="q-{{ $index + 1 }}" class="bg-white rounded-3xl p-8 shadow-sm border border-slate-200 question-block transition-all duration-300">
                        <div class="flex items-start gap-4">
                            <!-- Question Number Badge -->
                            <div class="shrink-0 flex flex-col items-center justify-center w-12 h-12 bg-indigo-50 rounded-2xl border border-indigo-100 text-indigo-600 font-black text-lg">
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
                                                        onchange="markAnswered({{ $index + 1 }})">
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
                                            oninput="markAnswered({{ $index + 1 }}, this.value)"></textarea>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </form>
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
                        <button type="button" id="nav-{{ $index + 1 }}" onclick="scrollToQuestion({{ $index + 1 }})" 
                            class="aspect-square flex items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600 font-bold text-sm transition-all hover:border-indigo-400 hover:text-indigo-600">
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

    <!-- Confirm Submit Modal -->
    <div id="confirmModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeConfirmModal()"></div>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="relative bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-md w-full p-8">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-indigo-100 mb-6">
                    <svg class="h-8 w-8 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <h3 class="text-2xl font-black text-slate-900 text-center mb-2">Xác nhận nộp bài</h3>
                <p class="text-slate-500 text-center mb-8">Bạn có chắc chắn muốn nộp bài thi ngay bây giờ? Bạn sẽ không thể sửa đổi đáp án sau khi nộp.</p>
                
                <div class="flex items-center gap-4">
                    <button type="button" onclick="closeConfirmModal()" class="flex-1 px-4 py-3 bg-white border border-slate-300 rounded-xl text-slate-700 font-bold hover:bg-slate-50 transition-colors">
                        Kiểm tra lại
                    </button>
                    <button type="button" onclick="document.getElementById('examForm').submit()" class="flex-1 px-4 py-3 bg-indigo-600 rounded-xl text-white font-bold hover:bg-indigo-700 transition-colors shadow-md">
                        Nộp bài ngay
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const totalQuestions = {{ count($questions) }};
        let answeredQuestions = new Set();
        
        // Timer Logic
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
                // Auto submit
                document.getElementById('examForm').submit();
            } else {
                timerDisplay.textContent = formatTime(timeRemaining);
                if (timeRemaining < 300) { // last 5 minutes
                    timerDisplay.classList.remove('text-indigo-600');
                    timerDisplay.classList.add('text-red-600', 'animate-pulse');
                }
            }
        }, 1000);

        // Navigation Logic
        function scrollToQuestion(index) {
            const el = document.getElementById('q-' + index);
            if(el) {
                // Remove highlight from all
                document.querySelectorAll('.question-block').forEach(b => {
                    b.classList.remove('ring-2', 'ring-indigo-400', 'ring-offset-2');
                });
                
                // Add highlight to target
                el.classList.add('ring-2', 'ring-indigo-400', 'ring-offset-2');
                el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                
                // Remove highlight after 2s
                setTimeout(() => el.classList.remove('ring-2', 'ring-indigo-400', 'ring-offset-2'), 2000);
            }
        }

        function markAnswered(index, value = true) {
            const navBtn = document.getElementById('nav-' + index);
            
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
            document.getElementById('unansweredCount').textContent = totalQuestions - answeredQuestions.size;
        }

        // Modal Logic
        function confirmSubmit() {
            document.getElementById('confirmModal').classList.remove('hidden');
        }
        
        function closeConfirmModal() {
            document.getElementById('confirmModal').classList.add('hidden');
        }

        // Prevent closing window accidentally
        window.addEventListener('beforeunload', function (e) {
            if (timeRemaining > 0) {
                e.preventDefault();
                e.returnValue = 'Bạn có chắc chắn muốn rời khỏi trang? Bài làm của bạn có thể không được lưu.';
            }
        });

        // Ensure form submission removes the beforeunload listener
        document.getElementById('examForm').addEventListener('submit', function() {
            window.removeEventListener('beforeunload', function(){});
        });
    </script>
</body>
</html>
