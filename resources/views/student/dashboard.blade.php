@extends('layouts.student')

@section('title', 'Bảng điều khiển Sinh viên')

@section('content')
<div class="max-w-6xl mx-auto space-y-8 pb-16">

    <!-- 1. Header & Welcome Banner -->
    <div class="bg-gradient-to-br from-indigo-900 via-indigo-800 to-slate-900 rounded-3xl p-8 text-white relative overflow-hidden shadow-xl border border-indigo-700/50">
        <!-- Decorative Glow Backgrounds -->
        <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full bg-indigo-500/20 blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -left-24 w-96 h-96 rounded-full bg-blue-500/20 blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur-md border border-white/15 text-xs font-semibold text-indigo-200 mb-3">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Cổng thi trực tuyến EyeExam
                </div>
                <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight mb-2">Xin chào, {{ auth()->user()->name }}!</h1>
                <p class="text-sm text-indigo-200/90 flex items-center gap-4 flex-wrap">
                    <span>Mã sinh viên: <strong class="text-white font-mono">{{ auth()->user()->code }}</strong></span>
                    <span>•</span>
                    <span>Lớp / Chuyên ngành: <strong class="text-white">{{ auth()->user()->class_name ?? 'Sinh viên' }}</strong></span>
                </p>
            </div>

            <div class="shrink-0 flex items-center gap-3">
                <a href="{{ route('student.exams.index') }}" class="px-5 py-3 rounded-2xl bg-white text-indigo-900 font-bold text-sm hover:bg-indigo-50 transition-all shadow-md active:scale-95 flex items-center gap-2">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    Tất cả kỳ thi
                </a>
            </div>
        </div>
    </div>

    <!-- 2. Summary Statistics Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold shrink-0 shadow-sm">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
            <div>
                <p class="text-xs text-slate-500 font-semibold uppercase tracking-wider">Bài thi được giao</p>
                <p class="text-2xl font-black text-slate-900 mt-0.5">{{ $stats['total_assigned'] }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold shrink-0 shadow-sm">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="text-xs text-slate-500 font-semibold uppercase tracking-wider">Lượt đã hoàn thành</p>
                <p class="text-2xl font-black text-emerald-600 mt-0.5">{{ $stats['total_submitted'] }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold shrink-0 shadow-sm">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path></svg>
            </div>
            <div>
                <p class="text-xs text-slate-500 font-semibold uppercase tracking-wider">Điểm trung bình</p>
                <p class="text-2xl font-black text-purple-600 mt-0.5">
                    {{ $stats['avg_score'] !== null ? $stats['avg_score'] . ' đ' : '--' }}
                </p>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold shrink-0 shadow-sm">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="text-xs text-slate-500 font-semibold uppercase tracking-wider">Đang làm dở</p>
                <p class="text-2xl font-black text-amber-600 mt-0.5">{{ $stats['in_progress_count'] }}</p>
            </div>
        </div>
    </div>

    <!-- 3. Section: Danh Sách Kỳ Thi Theo Danh Mục -->
    <div class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-3">
            <div>
                <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    Kỳ thi được phân công theo danh mục
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Chọn danh mục môn học để lọc nhanh các đề thi cần làm</p>
            </div>

            <!-- Category Filter Pills -->
            @if(count($categories) > 0)
                <div class="flex items-center gap-1.5 flex-wrap">
                    <button type="button" onclick="filterExamCategory('all')" id="tab-all" class="category-pill active px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all bg-indigo-600 text-white shadow-sm">
                        Tất cả ({{ $assignedExams->count() }})
                    </button>
                    @foreach($categories as $cat)
                        @php
                            $count = $assignedExams->filter(function($e) use ($cat) {
                                return $cat['type'] === 'category' ? ($e->category_id == str_replace('cat_', '', $cat['id'])) : ($e->subject_id == str_replace('subj_', '', $cat['id']));
                            })->count();
                        @endphp
                        <button type="button" onclick="filterExamCategory('{{ $cat['id'] }}')" id="tab-{{ $cat['id'] }}" class="category-pill px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 shadow-sm">
                            {{ $cat['name'] }} ({{ $count }})
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        @if($assignedExams->isEmpty())
            <div class="bg-white rounded-3xl p-12 text-center border border-slate-200 shadow-sm">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-50 mb-4 text-slate-400">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <h3 class="text-base font-bold text-slate-900 mb-1">Chưa có bài thi nào được giao</h3>
                <p class="text-xs text-slate-500">Giảng viên chưa phân công bài thi nào cho bạn vào thời điểm này.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="examsContainer">
                @foreach($assignedExams as $exam)
                    @php
                        $catKey = $exam->category ? 'cat_' . $exam->category->id : ($exam->subject ? 'subj_' . $exam->subject->id : 'none');
                    @endphp
                    <div class="exam-item-card bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-all flex flex-col justify-between overflow-hidden relative" data-category="{{ $catKey }}">
                        <!-- Top Accent Line -->
                        <div class="h-1.5 w-full bg-{{ $exam->status_color }}-500"></div>

                        <div class="p-6 flex-1 flex flex-col justify-between space-y-4">
                            <!-- Header tags -->
                            <div>
                                <div class="flex items-center justify-between gap-2 mb-2.5">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-{{ $exam->status_color }}-100 text-{{ $exam->status_color }}-800">
                                        {{ $exam->calculated_status }}
                                    </span>
                                    <span class="text-xs font-mono font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-md">#{{ $exam->code }}</span>
                                </div>

                                <h3 class="text-base font-bold text-slate-900 line-clamp-2 hover:text-indigo-600 transition-colors">
                                    {{ $exam->title }}
                                </h3>

                                <div class="flex items-center gap-2 mt-2 flex-wrap">
                                    <span class="text-xs font-semibold text-slate-500">{{ $exam->subject->name ?? 'Môn học' }}</span>
                                    @if($exam->category)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-purple-50 text-purple-700 border border-purple-100">
                                            {{ $exam->category->name }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Meta details -->
                            <div class="grid grid-cols-2 gap-2 text-xs bg-slate-50 p-3 rounded-xl border border-slate-100">
                                <div>
                                    <span class="text-slate-400 block">Thời lượng:</span>
                                    <strong class="text-slate-700 font-bold">{{ $exam->duration_minutes }} phút</strong>
                                </div>
                                <div>
                                    <span class="text-slate-400 block">Số câu hỏi:</span>
                                    <strong class="text-slate-700 font-bold">{{ $exam->total_questions }} câu</strong>
                                </div>
                            </div>

                            @if($exam->submitted_count > 0)
                                <div class="p-2.5 bg-emerald-50 rounded-xl border border-emerald-100 flex items-center justify-between text-xs">
                                    <span class="text-emerald-800 font-semibold">Điểm cao nhất:</span>
                                    <span class="font-black text-sm text-emerald-700">{{ $exam->score !== null ? $exam->score . ' đ' : 'Chưa chấm' }}</span>
                                </div>
                            @endif

                            <!-- Action Buttons -->
                            <div class="pt-2 space-y-2">
                                @if($exam->has_in_progress)
                                    <a href="{{ route('student.exams.take', $exam) }}" class="w-full py-2.5 px-4 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold rounded-xl shadow-sm transition-all flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        Tiếp tục làm bài
                                    </a>
                                @elseif($exam->can_take)
                                    <form action="{{ route('student.exams.join') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="code" value="{{ $exam->code }}">
                                        <button type="submit" class="w-full py-2.5 px-4 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-sm transition-all flex items-center justify-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                            {{ $exam->submitted_count > 0 ? 'Thi lại (Lần ' . ($exam->submitted_count + 1) . ')' : 'Làm bài ngay' }}
                                        </button>
                                    </form>
                                @elseif($exam->calculated_status === 'Sắp thi')
                                    <button disabled class="w-full py-2.5 px-4 bg-slate-100 text-slate-400 text-xs font-bold rounded-xl cursor-not-allowed text-center border border-slate-200">
                                        Chưa đến giờ thi
                                    </button>
                                @elseif($exam->calculated_status === 'Đã hết hạn')
                                    <button disabled class="w-full py-2.5 px-4 bg-slate-100 text-slate-400 text-xs font-bold rounded-xl cursor-not-allowed text-center border border-slate-200">
                                        Kỳ thi đã kết thúc
                                    </button>
                                @else
                                    <button disabled class="w-full py-2.5 px-4 bg-slate-100 text-slate-400 text-xs font-bold rounded-xl cursor-not-allowed text-center border border-slate-200">
                                        Đã hết lượt làm bài
                                    </button>
                                @endif

                                <!-- Review Button -->
                                @if($exam->can_review)
                                    <a href="{{ route('student.exams.review', $exam) }}" class="w-full py-2 px-3 bg-white hover:bg-slate-50 text-indigo-700 text-xs font-bold rounded-xl border border-indigo-200 transition-colors flex items-center justify-center gap-1.5 shadow-sm">
                                        <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        Xem lại kết quả & bài làm
                                    </a>
                                @elseif($exam->submitted_count > 0 && !$exam->allow_review)
                                    <p class="text-[11px] text-slate-400 text-center italic">Giảng viên không mở xem lại bài thi này.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- 4. Section: Lịch Sử Bài Thi Gần Đây -->
    <div class="space-y-4 pt-6">
        <div class="flex items-center justify-between border-b border-slate-200 pb-3">
            <div>
                <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Lịch sử làm bài thi gần đây
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Danh sách các phiên làm bài thi bạn đã tham gia</p>
            </div>
        </div>

        @if($attempts->isEmpty())
            <div class="bg-white rounded-3xl p-8 text-center border border-slate-200 shadow-sm text-slate-400">
                <p class="text-sm font-medium">Chưa có lịch sử làm bài nào.</p>
            </div>
        @else
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50/80 text-slate-600 font-bold uppercase tracking-wider border-b border-slate-200">
                            <tr>
                                <th class="py-3.5 px-4">Lượt thi</th>
                                <th class="py-3.5 px-4">Đề thi / Môn học</th>
                                <th class="py-3.5 px-4">Thời gian nộp</th>
                                <th class="py-3.5 px-4">Trạng thái</th>
                                <th class="py-3.5 px-4 text-center">Điểm số</th>
                                <th class="py-3.5 px-4 text-right">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                            @foreach($attempts as $attempt)
                                <tr class="hover:bg-slate-50/60 transition-colors">
                                    <td class="py-3.5 px-4 font-mono font-bold text-indigo-700">
                                        #{{ $attempt->id }}
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <p class="font-bold text-slate-900 line-clamp-1">{{ $attempt->exam->title }}</p>
                                        <div class="flex items-center gap-1.5 mt-0.5">
                                            <span class="text-[11px] text-slate-500">{{ $attempt->exam->subject->name ?? 'Môn học' }}</span>
                                            @if($attempt->exam->category)
                                                <span class="px-1.5 py-0.5 rounded text-[10px] bg-purple-50 text-purple-700 border border-purple-100">
                                                    {{ $attempt->exam->category->name }}
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-600">
                                        {{ $attempt->submitted_at ? $attempt->submitted_at->format('d/m/Y H:i') : ($attempt->started_at ? $attempt->started_at->format('d/m/Y H:i') : '--') }}
                                    </td>
                                    <td class="py-3.5 px-4">
                                        @if($attempt->status === 'submitted')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">
                                                Đã nộp bài
                                            </span>
                                        @elseif($attempt->status === 'in_progress')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800">
                                                Đang làm dở
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-rose-100 text-rose-800">
                                                Vi phạm / Khóa
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-center font-black text-sm">
                                        @if($attempt->score_value !== null)
                                            <span class="{{ $attempt->score_value >= 5 ? 'text-emerald-600' : 'text-rose-600' }}">
                                                {{ $attempt->score_value }} đ
                                            </span>
                                        @else
                                            <span class="text-slate-400 font-normal">--</span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            @if($attempt->status === 'in_progress')
                                                <a href="{{ route('student.exams.take', $attempt->exam) }}" class="px-2.5 py-1 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-lg shadow-sm transition-all text-xs">
                                                    Tiếp tục
                                                </a>
                                            @else
                                                @if($attempt->exam->allow_review)
                                                    <a href="{{ route('student.exams.review', $attempt->exam) }}" class="px-2.5 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 font-bold rounded-lg transition-colors text-xs flex items-center gap-1">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                        Xem lại
                                                    </a>
                                                @else
                                                    <span class="text-[11px] text-slate-400 italic">Không cho xem</span>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>

</div>

<!-- Category Filtering Script -->
<script>
    function filterExamCategory(catId) {
        // Update button states
        document.querySelectorAll('.category-pill').forEach(btn => {
            btn.classList.remove('bg-indigo-600', 'text-white', 'active');
            btn.classList.add('bg-white', 'text-slate-700', 'border-slate-200');
        });
        const activeBtn = document.getElementById('tab-' + catId);
        if (activeBtn) {
            activeBtn.classList.add('bg-indigo-600', 'text-white', 'active');
            activeBtn.classList.remove('bg-white', 'text-slate-700', 'border-slate-200');
        }

        // Filter cards
        const cards = document.querySelectorAll('.exam-item-card');
        cards.forEach(card => {
            if (catId === 'all' || card.dataset.category === catId) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    }
</script>
@endsection
