@extends('layouts.student')

@section('title', 'Kỳ thi của tôi')

@section('content')
<div class="max-w-6xl mx-auto space-y-8 pb-16">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 flex items-center gap-2">
                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                Kỳ thi được giao
            </h1>
            <p class="text-xs text-slate-500 mt-1">Danh sách các bài thi mà giảng viên đã phân công cho bạn theo từng danh mục môn học.</p>
        </div>

        <!-- Category Filter Pills -->
        @if(count($categories) > 0)
            <div class="flex items-center gap-1.5 flex-wrap">
                <button type="button" onclick="filterExamCategory('all')" id="tab-all" class="category-pill active px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all bg-indigo-600 text-white shadow-sm">
                    Tất cả ({{ $exams->count() }})
                </button>
                @foreach($categories as $cat)
                    @php
                        $count = $exams->filter(function($e) use ($cat) {
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

    @if($exams->isEmpty())
        <div class="bg-white rounded-3xl p-12 text-center border border-slate-200 shadow-sm">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-50 mb-4 text-slate-400">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
            <h3 class="text-base font-bold text-slate-900 mb-1">Chưa có bài thi nào</h3>
            <p class="text-xs text-slate-500">Hiện tại bạn chưa được giao bài thi nào.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="examsContainer">
            @foreach($exams as $exam)
                @php
                    $catKey = $exam->category ? 'cat_' . $exam->category->id : ($exam->subject ? 'subj_' . $exam->subject->id : 'none');
                @endphp
                <div class="exam-item-card bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-all relative overflow-hidden flex flex-col justify-between" data-category="{{ $catKey }}">
                    <!-- Color strip based on status -->
                    <div class="h-1.5 w-full bg-{{ $exam->status_color }}-500"></div>
                    
                    <div class="p-6 flex-1 flex flex-col justify-between space-y-4">
                        <div>
                            <div class="flex justify-between items-start mb-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-{{ $exam->status_color }}-100 text-{{ $exam->status_color }}-800">
                                    {{ $exam->calculated_status }}
                                </span>
                                <span class="text-xs font-mono font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-md">#{{ $exam->code }}</span>
                            </div>
                            
                            <h3 class="text-base font-bold text-slate-900 mb-1 line-clamp-2 hover:text-indigo-600 transition-colors">{{ $exam->title }}</h3>
                            <div class="flex items-center gap-2 mb-2 flex-wrap">
                                <p class="text-xs font-semibold text-slate-500">{{ $exam->subject->name ?? 'Môn học' }}</p>
                                @if($exam->category)
                                    <span class="px-2 py-0.5 rounded text-[11px] font-medium bg-purple-50 text-purple-700 border border-purple-100">
                                        {{ $exam->category->name }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-2 bg-slate-50 p-3 rounded-xl border border-slate-100 text-xs">
                            <div>
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Thời gian</p>
                                <p class="text-xs font-bold text-slate-700 mt-0.5">{{ $exam->duration_minutes }} phút</p>
                            </div>
                            <div>
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Số câu hỏi</p>
                                <p class="text-xs font-bold text-slate-700 mt-0.5">{{ $exam->total_questions }} câu</p>
                            </div>
                        </div>
                        
                        <div class="space-y-1 text-xs">
                            @if($exam->start_at || $exam->end_at)
                                @if($exam->start_at)
                                <div class="flex justify-between text-slate-500">
                                    <span>Mở đề:</span>
                                    <span class="font-medium text-slate-700">{{ $exam->start_at->format('d/m/Y H:i') }}</span>
                                </div>
                                @endif
                                @if($exam->end_at)
                                <div class="flex justify-between text-slate-500">
                                    <span>Đóng đề:</span>
                                    <span class="font-medium text-slate-700">{{ $exam->end_at->format('d/m/Y H:i') }}</span>
                                </div>
                                @endif
                            @else
                                <div class="text-[11px] text-slate-400 italic">Không giới hạn thời gian mở/đóng.</div>
                            @endif
                        </div>
                        
                        <!-- Score & Status info if previously attempted -->
                        @if($exam->submitted_count > 0)
                            <div class="p-2.5 bg-emerald-50 rounded-xl border border-emerald-100 flex items-center justify-between text-xs">
                                <span class="font-semibold text-emerald-800">Điểm cao nhất:</span>
                                <span class="font-black text-sm text-emerald-700">{{ $exam->score !== null ? $exam->score . ' đ' : 'Chưa chấm' }}</span>
                            </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="space-y-2 pt-2 border-t border-slate-100">
                            @if($exam->has_in_progress)
                                <a href="{{ route('student.exams.take', $exam) }}" class="w-full py-2.5 px-4 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold rounded-xl shadow-sm transition-all flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    Tiếp tục làm bài
                                </a>
                            @elseif($exam->can_take)
                                @if($exam->require_face_verification)
                                    <button type="button" onclick="openFaceVerifyModal({{ $exam->id }}, '{{ addslashes($exam->title) }}')" class="w-full py-2.5 px-4 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-sm transition-all flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                        <span>{{ $exam->submitted_count > 0 ? 'Xác thực & Thi lại (Lần ' . ($exam->submitted_count + 1) . ')' : 'Xác thực khuôn mặt & Vào thi' }}</span>
                                    </button>
                                @else
                                    <form action="{{ route('student.exams.join') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="code" value="{{ $exam->code }}">
                                        <button type="submit" class="w-full py-2.5 px-4 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-sm transition-all flex items-center justify-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                            {{ $exam->submitted_count > 0 ? 'Thi lại (Lần ' . ($exam->submitted_count + 1) . ')' : 'Làm bài ngay' }}
                                        </button>
                                    </form>
                                @endif
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

                            <!-- Review Results Button (if permitted by teacher) -->
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

@include('student.exams.partials.face_verify_modal')
@endsection
