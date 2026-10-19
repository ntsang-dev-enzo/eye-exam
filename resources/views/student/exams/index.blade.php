@extends('layouts.student')

@section('title', 'Kỳ thi được giao')

@section('content')
<div class="space-y-6 sm:space-y-8 pb-16">

    <!-- 1. Page Header: Large Bold Navy Title & Short Description -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200/80 pb-5">
        <div class="space-y-1.5">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 border border-blue-100 text-[#0f274a] text-xs font-bold tracking-wide uppercase">
                <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
                Kỳ thi trực tuyến
            </div>
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black text-[#0f274a] tracking-tight">
                Kỳ thi được giao
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 font-medium leading-relaxed">
                Danh sách các bài thi do giảng viên phân công theo học phần, kèm lịch mở đề và thông tin làm bài chi tiết.
            </p>
        </div>

        <!-- Right Quick Counter Badges -->
        <div class="flex items-center gap-3 shrink-0">
            <div class="bg-white rounded-2xl px-4 py-2.5 border border-slate-200/80 shadow-[0_4px_20px_-4px_rgba(15,39,74,0.05)] flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold shrink-0 border border-blue-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
                <div>
                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Tổng bài thi</span>
                    <span class="text-sm sm:text-base font-black text-[#0f274a] font-mono">{{ $exams->count() }} kỳ thi</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Filters: Semester & Category (Lọc theo học kỳ & môn học) -->
    <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/80 p-3.5 sm:p-4 shadow-[0_4px_20px_-4px_rgba(15,39,74,0.03)] space-y-3">
        <!-- Semester Filter Tabs -->
        <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none border-b border-slate-100">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider px-2 shrink-0 flex items-center gap-1">
                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                Học kỳ:
            </span>
            <a href="{{ route('student.exams.index', array_merge(request()->except('semester'))) }}" 
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all shrink-0 cursor-pointer flex items-center gap-1.5 {{ empty($selectedSemester) ? 'bg-[#0f274a] text-white shadow-sm' : 'bg-slate-50 text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
                <span>Tất cả học kỳ</span>
            </a>
            @foreach($semesters as $sem)
                <a href="{{ route('student.exams.index', array_merge(request()->except('semester'), ['semester' => $sem])) }}" 
                   class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all shrink-0 cursor-pointer flex items-center gap-1.5 {{ $selectedSemester === $sem ? 'bg-[#0f274a] text-white shadow-sm' : 'bg-slate-50 text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
                    <span>{{ $sem }}</span>
                </a>
            @endforeach
        </div>

        <!-- Subject Filter Buttons (Các nút lọc môn học) -->
        @if(count($categories) > 0)
            <div class="flex items-center gap-2 overflow-x-auto pb-1 sm:pb-0 scrollbar-none pt-1">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider px-2 shrink-0 hidden md:inline-block">Lọc theo môn:</span>
                
                <button type="button" 
                    onclick="filterExamCategory('all')" 
                    id="tab-all" 
                    class="category-pill active px-4 py-2 rounded-xl sm:rounded-2xl text-xs font-bold transition-all duration-200 shrink-0 bg-blue-600 text-white shadow-md shadow-blue-600/20 border border-blue-600 cursor-pointer flex items-center gap-2">
                    <span>Tất cả môn</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] font-mono bg-white/20 text-white">{{ $exams->count() }}</span>
                </button>

                @foreach($categories as $cat)
                    @php
                        $count = $exams->filter(function($e) use ($cat) {
                            return $cat['type'] === 'category' ? ($e->category_id == str_replace('cat_', '', $cat['id'])) : ($e->subject_id == str_replace('subj_', '', $cat['id']));
                        })->count();
                    @endphp
                    <button type="button" 
                        onclick="filterExamCategory('{{ $cat['id'] }}')" 
                        id="tab-{{ $cat['id'] }}" 
                        class="category-pill px-4 py-2 rounded-xl sm:rounded-2xl text-xs font-bold transition-all duration-200 shrink-0 bg-slate-50 hover:bg-blue-50/60 text-slate-700 hover:text-blue-700 border border-slate-200/80 hover:border-blue-300 shadow-2xs cursor-pointer flex items-center gap-2">
                        <span>{{ $cat['name'] }}</span>
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] font-mono bg-white text-slate-500 border border-slate-200">{{ $count }}</span>
                    </button>
                @endforeach
            </div>
        @endif
    </div>

    <!-- 3. Exam Information Cards Grid -->
    @if($exams->isEmpty())
        <!-- Empty State -->
        <div class="bg-white rounded-3xl p-16 text-center border border-slate-200/80 shadow-[0_10px_35px_-10px_rgba(15,39,74,0.05)]">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-blue-50 text-blue-600 mb-4 border border-blue-100 shadow-inner">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 01-2-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-black text-[#0f274a] mb-2">Chưa có bài thi nào</h3>
            <p class="text-xs sm:text-sm text-slate-500 max-w-sm mx-auto leading-relaxed">
                Hiện tại bạn chưa được phân công kỳ thi nào trong danh sách. Vui lòng liên hệ giảng viên hoặc kiểm tra lại sau.
            </p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="examsContainer">
            @foreach($exams as $exam)
                @php
                    $catKey = $exam->category ? 'cat_' . $exam->category->id : ($exam->subject ? 'subj_' . $exam->subject->id : 'none');
                @endphp
                
                <!-- Individual Exam Card: Soft Rounded, Subtle Blue Border, Elevated Drop Shadow -->
                <div class="exam-item-card bg-white rounded-3xl border border-blue-100/90 shadow-[0_10px_30px_-5px_rgba(15,39,74,0.06)] hover:shadow-2xl hover:shadow-blue-950/12 hover:border-blue-300 transition-all duration-300 relative overflow-hidden flex flex-col justify-between group hover:-translate-y-1" data-category="{{ $catKey }}">
                    
                    <!-- Decorative Top Gradient Accent -->
                    <div class="h-1.5 w-full bg-gradient-to-r from-[#0f274a] via-blue-600 to-indigo-600"></div>

                    <div class="p-5 sm:p-6 flex-1 flex flex-col justify-between space-y-4">
                        
                        <!-- Header: Status Badge & Exam Code -->
                        <div>
                            <div class="flex justify-between items-center gap-2 mb-3">
                                <!-- Status Badge -->
                                @php
                                    $statusBadgeClasses = match($exam->status_color) {
                                        'rose' => 'bg-rose-50 text-rose-700 border-rose-200/70',
                                        'amber' => 'bg-amber-50 text-amber-700 border-amber-200/70',
                                        'emerald' => 'bg-emerald-50 text-emerald-700 border-emerald-200/70',
                                        'indigo' => 'bg-indigo-50 text-indigo-700 border-indigo-200/70',
                                        'blue' => 'bg-blue-50 text-blue-700 border-blue-200/70',
                                        default => 'bg-slate-100 text-slate-700 border-slate-200',
                                    };
                                @endphp
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border shadow-2xs {{ $statusBadgeClasses }}">
                                    @if(in_array($exam->calculated_status, ['Đang mở', 'Đang làm dở', 'Chưa làm']))
                                        <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
                                    @endif
                                    {{ $exam->calculated_status }}
                                </span>

                                <!-- Exam Code -->
                                <span class="text-xs font-mono font-bold text-slate-500 bg-slate-100 px-2.5 py-1 rounded-lg border border-slate-200/60 shadow-2xs">
                                    #{{ $exam->code }}
                                </span>
                            </div>
                            
                            <!-- Exam Title (Large, Bold Navy Text) -->
                            <h3 class="text-base sm:text-lg font-black text-[#0f274a] mb-1.5 line-clamp-2 group-hover:text-blue-600 transition-colors leading-snug">
                                {{ $exam->title }}
                            </h3>

                            <!-- Subject Name & Category Badge -->
                            <div class="flex items-center gap-2 mb-3 flex-wrap">
                                <span class="text-xs font-bold text-slate-600 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                    {{ $exam->subject->name ?? 'Môn học' }}
                                </span>
                                @if($exam->category)
                                    <span class="px-2.5 py-0.5 rounded-md text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-100/80">
                                        {{ $exam->category->name }}
                                    </span>
                                @endif
                                @if($exam->semester)
                                    <span class="px-2.5 py-0.5 rounded-md text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                        {{ $exam->semester }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Exam Metrics Box: Duration & Question Count -->
                        <div class="grid grid-cols-2 gap-2.5 bg-slate-50/80 p-3.5 rounded-2xl border border-slate-100">
                            <!-- Duration -->
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-xl bg-white text-blue-600 flex items-center justify-center shrink-0 border border-slate-200/80 shadow-2xs">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Thời gian</p>
                                    <p class="text-xs font-black text-[#0f274a] font-mono">{{ $exam->duration_minutes }} phút</p>
                                </div>
                            </div>

                            <!-- Question Count -->
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-xl bg-white text-indigo-600 flex items-center justify-center shrink-0 border border-slate-200/80 shadow-2xs">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Số câu hỏi</p>
                                    <p class="text-xs font-black text-[#0f274a] font-mono">{{ $exam->total_questions }} câu</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Open / Close Exam Schedule -->
                        <div class="space-y-1.5 text-xs bg-white rounded-xl p-2.5 border border-slate-100">
                            @if($exam->start_at || $exam->end_at)
                                @if($exam->start_at)
                                <div class="flex justify-between items-center text-slate-500">
                                    <span class="flex items-center gap-1.5 text-[11px] font-medium text-slate-400">
                                        <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        Mở thi:
                                    </span>
                                    <span class="font-bold text-slate-800 font-mono text-xs">{{ $exam->start_at->format('d/m/Y H:i') }}</span>
                                </div>
                                @endif
                                @if($exam->end_at)
                                <div class="flex justify-between items-center text-slate-500">
                                    <span class="flex items-center gap-1.5 text-[11px] font-medium text-slate-400">
                                        <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Đóng thi:
                                    </span>
                                    <span class="font-bold text-slate-800 font-mono text-xs">{{ $exam->end_at->format('d/m/Y H:i') }}</span>
                                </div>
                                @endif
                            @else
                                <div class="text-[11px] text-slate-400 italic text-center">Không giới hạn thời gian mở/đóng.</div>
                            @endif
                        </div>
                        
                        <!-- Highest Score Info Box -->
                        @if($exam->submitted_count > 0)
                            <div class="p-3 bg-gradient-to-r from-emerald-50 to-teal-50 rounded-2xl border border-emerald-100 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="w-6 h-6 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center">
                                        <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                    </span>
                                    <span class="font-bold text-xs text-emerald-900">Điểm cao nhất:</span>
                                </div>
                                <span class="font-black text-base text-emerald-700 font-mono">
                                    @if($exam->allow_view_score ?? true)
                                        {{ $exam->score !== null ? $exam->score . ' đ' : 'Chưa chấm' }}
                                    @else
                                        <span class="text-xs text-slate-500 font-medium italic flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                            Chờ công bố điểm
                                        </span>
                                    @endif
                                </span>
                            </div>
                        @endif

                        <!-- Action Buttons Section -->
                        <div class="space-y-2.5 pt-3 border-t border-slate-100">
                            @if(!auth()->user()->face_registered)
                                <a href="{{ route('student.face.register') }}" 
                                    class="w-full py-3 px-4 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white text-xs font-bold rounded-2xl shadow-md shadow-amber-500/20 transition-all flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                    <span>Cần cập nhật khuôn mặt để thi</span>
                                </a>
                            @elseif($exam->has_in_progress)
                                <!-- Continue In-Progress Exam (Navy Blue Background) -->
                                <a href="{{ route('student.exams.take', $exam) }}" 
                                    class="w-full py-3 px-4 bg-[#0f274a] hover:bg-[#1a365d] active:bg-[#0a192f] text-white text-xs font-bold rounded-2xl shadow-md shadow-blue-950/20 hover:shadow-lg transition-all flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    <span>Tiếp tục làm bài</span>
                                </a>
                            @elseif($exam->can_take)
                                @if($exam->require_face_verification)
                                    <!-- Face Verification & Retake/Take Exam (Prominent Navy Blue Background) -->
                                    <button type="button" onclick="openFaceVerifyModal({{ $exam->id }}, '{{ addslashes($exam->title) }}')" 
                                        class="w-full py-3 px-4 bg-[#0f274a] hover:bg-[#1a365d] active:bg-[#0a192f] text-white text-xs font-bold rounded-2xl shadow-md shadow-blue-950/25 hover:shadow-lg hover:-translate-y-0.5 active:translate-y-0 transition-all flex items-center justify-center gap-2 cursor-pointer">
                                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                        </svg>
                                        <span>{{ $exam->submitted_count > 0 ? 'Xác thực & Thi lại (Lần ' . ($exam->submitted_count + 1) . ')' : 'Xác thực khuôn mặt & Vào thi' }}</span>
                                    </button>
                                @else
                                    <!-- Standard Take/Retake Exam (Prominent Navy Blue Background) -->
                                    <form action="{{ route('student.exams.join') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="code" value="{{ $exam->code }}">
                                        <button type="submit" 
                                            class="w-full py-3 px-4 bg-[#0f274a] hover:bg-[#1a365d] active:bg-[#0a192f] text-white text-xs font-bold rounded-2xl shadow-md shadow-blue-950/25 hover:shadow-lg hover:-translate-y-0.5 active:translate-y-0 transition-all flex items-center justify-center gap-2 cursor-pointer">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                            </svg>
                                            <span>{{ $exam->submitted_count > 0 ? 'Thi lại (Lần ' . ($exam->submitted_count + 1) . ')' : 'Vào thi ngay' }}</span>
                                        </button>
                                    </form>
                                @endif
                            @elseif($exam->calculated_status === 'Sắp thi')
                                <button disabled class="w-full py-3 px-4 bg-slate-100 text-slate-400 text-xs font-bold rounded-2xl cursor-not-allowed text-center border border-slate-200/80">
                                    Chưa đến giờ thi
                                </button>
                            @elseif($exam->calculated_status === 'Đã hết hạn')
                                <button disabled class="w-full py-3 px-4 bg-slate-100 text-slate-400 text-xs font-bold rounded-2xl cursor-not-allowed text-center border border-slate-200/80">
                                    Kỳ thi đã kết thúc
                                </button>
                            @else
                                <button disabled class="w-full py-3 px-4 bg-slate-100 text-slate-400 text-xs font-bold rounded-2xl cursor-not-allowed text-center border border-slate-200/80">
                                    Đã hết lượt làm bài
                                </button>
                            @endif

                            <!-- Review Results Button: Prominent Blue Outlined Style (Kiểu nút viền xanh) -->
                            @if($exam->can_review)
                                <a href="{{ route('student.exams.review', $exam) }}" 
                                    class="w-full py-2.5 px-4 bg-white hover:bg-blue-50/60 text-blue-700 text-xs font-bold rounded-2xl border-2 border-blue-600 hover:border-blue-700 transition-all duration-200 flex items-center justify-center gap-2 shadow-xs cursor-pointer">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    <span>Xem lại kết quả & bài làm</span>
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
        // Update category pill button states
        document.querySelectorAll('.category-pill').forEach(btn => {
            btn.classList.remove('bg-[#0f274a]', 'text-white', 'border-[#0f274a]', 'active');
            btn.classList.add('bg-slate-50', 'text-slate-700', 'border-slate-200/80');
        });
        const activeBtn = document.getElementById('tab-' + catId);
        if (activeBtn) {
            activeBtn.classList.add('bg-[#0f274a]', 'text-white', 'border-[#0f274a]', 'active');
            activeBtn.classList.remove('bg-slate-50', 'text-slate-700', 'border-slate-200/80');
        }

        // Filter cards smoothly
        const cards = document.querySelectorAll('.exam-item-card');
        cards.forEach(card => {
            if (catId === 'all' || card.dataset.category === catId) {
                card.style.display = 'flex';
                card.style.opacity = '1';
            } else {
                card.style.display = 'none';
                card.style.opacity = '0';
            }
        });
    }
</script>

@include('student.exams.partials.face_verify_modal')
@endsection
