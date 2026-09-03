@extends('layouts.teacher')

@section('title', 'Kết quả Đề thi')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex items-center justify-between gap-4">
        <div class="flex items-center gap-2">
            <a href="{{ route('teacher.exams.index') }}" class="text-sm font-medium text-gray-500 hover:text-blue-600 transition-colors">Quản lý kỳ thi</a>
            <span class="text-gray-400">/</span>
            <span class="text-sm font-semibold text-gray-900">Kết quả Đề thi</span>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('teacher.exams.monitor', $exam) }}" class="px-4 py-2 bg-blue-50 border border-blue-200 text-blue-700 rounded-xl hover:bg-blue-100 transition-colors font-semibold text-sm flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-blue-600 animate-ping"></span>
                Vào phòng giám sát trực tiếp
            </a>
            <a href="{{ route('teacher.exams.index') }}" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors font-medium text-sm">
                Quay lại
            </a>
        </div>
    </div>
    
    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Kết quả: {{ $exam->title }}</h2>
            <p class="text-sm text-gray-500 mt-1">Mã đề: <span class="font-bold text-gray-800">{{ $exam->code }}</span> - Môn: <span class="font-bold text-gray-800">{{ $exam->subject->name ?? 'N/A' }}</span></p>
        </div>
        <div class="text-right">
            <p class="text-sm text-gray-600">Tổng quan: <span class="font-bold text-blue-600">{{ $stats['total'] }}</span> lượt nộp bài</p>
            <p class="text-xs text-gray-500 mt-1">Trạng thái đề: <span class="font-medium {{ $exam->status === 'published' ? 'text-blue-600' : 'text-rose-600' }}">{{ $exam->status === 'published' ? 'Mở' : 'Đóng' }}</span></p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm text-center">
            <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-1">Điểm trung bình</p>
            <p class="text-3xl font-black text-blue-600">{{ number_format($stats['average'], 1) }}</p>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm text-center">
            <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-1">Cao nhất</p>
            <p class="text-3xl font-black text-emerald-600">{{ $stats['max'] }}</p>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm text-center">
            <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-1">Thấp nhất</p>
            <p class="text-3xl font-black text-rose-600">{{ $stats['min'] }}</p>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm text-center">
            <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-1">Tỉ lệ đạt (>= 5)</p>
            <p class="text-3xl font-black text-indigo-600">{{ $stats['pass_rate'] }}%</p>
        </div>
    </div>

    <!-- Bảng điểm & Theo dõi hành vi -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-bold text-gray-900 text-base">Danh sách kết quả & Nhật ký hành vi</h3>
            <span class="text-xs text-gray-500">Nhấp vào thí sinh hoặc nút <span class="font-bold text-blue-600">"Xem hành vi"</span> để đối soát nhật ký chống gian lận.</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/60 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <th class="px-6 py-3.5 border-b border-gray-100 w-12 text-center">STT</th>
                        <th class="px-6 py-3.5 border-b border-gray-100">Thí sinh</th>
                        <th class="px-6 py-3.5 border-b border-gray-100 text-center">Tình trạng</th>
                        <th class="px-6 py-3.5 border-b border-gray-100 text-center">Điểm số</th>
                        <th class="px-6 py-3.5 border-b border-gray-100 text-center">Chi tiết (Đ/S/B)</th>
                        <th class="px-6 py-3.5 border-b border-gray-100 text-center">Vi phạm</th>
                        <th class="px-6 py-3.5 border-b border-gray-100 text-center">Rời màn hình</th>
                        <th class="px-6 py-3.5 border-b border-gray-100 text-right">Hành vi & Nhật ký</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($attempts as $index => $attempt)
                        <tr class="hover:bg-blue-50/30 transition-colors cursor-pointer" onclick="openStudentBehaviorModal({{ $attempt->id }})">
                            <td class="px-6 py-4 text-center text-sm text-gray-500">{{ $index + 1 }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-sm shrink-0">
                                        {{ substr($attempt->student->name ?? '?', 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-900">{{ $attempt->student->name ?? 'Sinh viên vô danh' }}</p>
                                        <p class="text-xs text-gray-500 font-mono">{{ $attempt->student->code ?? '' }} • {{ $attempt->student->email ?? '' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($attempt->status === 'submitted')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700">Đã nộp bài</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-700">Đang thi</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($attempt->status === 'submitted')
                                    <span class="text-lg font-black text-blue-600">{{ $attempt->score_value }}</span>
                                @else
                                    <span class="text-sm text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($attempt->status === 'submitted')
                                    <div class="flex items-center justify-center gap-2 text-xs font-medium">
                                        <span class="text-emerald-600 font-bold" title="Câu đúng">{{ $attempt->correct_answers }} Đ</span>
                                        <span class="text-gray-300">|</span>
                                        <span class="text-rose-600 font-bold" title="Câu sai">{{ $attempt->wrong_answers }} S</span>
                                        <span class="text-gray-300">|</span>
                                        <span class="text-amber-600 font-bold" title="Chưa làm">{{ $attempt->unanswered }} B</span>
                                    </div>
                                @else
                                    <span class="text-sm text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($attempt->cheat_warnings > 0)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800">
                                        {{ $attempt->cheat_warnings }} lần
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400 font-medium">0</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center text-xs">
                                @if($attempt->out_of_screen_time > 0)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-amber-50 text-amber-700 font-bold border border-amber-200">
                                        {{ $attempt->out_of_screen_time }}s
                                    </span>
                                @else
                                    <span class="text-gray-400">0s</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button type="button" onclick="event.stopPropagation(); openStudentBehaviorModal({{ $attempt->id }});" class="inline-flex items-center px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold text-xs rounded-xl transition-colors border border-blue-200 shadow-sm">
                                    🔍 Xem hành vi
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                Chưa có sinh viên nào tham gia hoặc nộp bài thi này.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ====================================================
     STUDENT BEHAVIOR & PROCTOR SNAPSHOTS MODAL
     ==================================================== -->
<div id="behaviorModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" onclick="closeBehaviorModal()"></div>
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-12 text-center sm:p-0">
        <div class="relative bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-4xl w-full p-6 sm:p-8 space-y-6 max-h-[90vh] flex flex-col z-10">
            
            <!-- Modal Header -->
            <div class="flex items-start justify-between pb-4 border-b border-gray-100 shrink-0">
                <div class="flex items-center gap-4">
                    <div id="modalStudentAvatar" class="w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-700 text-white flex items-center justify-center font-black text-xl shadow-md overflow-hidden">
                        ?
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 id="modalStudentName" class="text-xl font-black text-gray-900">Đang tải...</h3>
                            <span id="modalAttemptStatus" class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-700">Đã nộp</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-0.5">
                            Mã SV: <span id="modalStudentCode" class="font-bold text-gray-700">...</span> • 
                            Email: <span id="modalStudentEmail" class="text-gray-700">...</span>
                        </p>
                    </div>
                </div>
                <button type="button" onclick="closeBehaviorModal()" class="text-gray-400 hover:text-gray-600 p-2 rounded-xl hover:bg-gray-100 transition-colors">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- 3 Face Comparison Photos (Hồ sơ gốc vs Quét vào thi vs Chụp lúc thi) -->
            <div class="bg-slate-50/80 rounded-2xl p-4 border border-slate-200/80 shrink-0">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 flex items-center gap-2">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Đối soát Khuôn mặt 3 Chiều (Hồ sơ gốc - Xác thực thi - Ảnh lúc làm bài)
                    </h4>
                    <span id="faceMatchScoreBadge" class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-500">
                        Đang đối chiếu
                    </span>
                </div>
                
                <div class="grid grid-cols-3 gap-3">
                    <!-- Photo 1: Enrolled -->
                    <div class="bg-white rounded-xl p-3 border border-slate-200 text-center flex flex-col items-center">
                        <div class="w-24 h-24 rounded-lg bg-slate-100 overflow-hidden border border-slate-200 shadow-sm mb-2 flex items-center justify-center">
                            <img id="compEnrolledImg" class="w-full h-full object-cover hidden" alt="Ảnh hồ sơ">
                            <span id="compEnrolledPlaceholder" class="text-xs text-slate-400">Chưa có Face ID</span>
                        </div>
                        <span class="text-[11px] font-bold text-slate-800">1. Hồ sơ Gốc</span>
                        <span class="text-[10px] text-slate-500">Đăng ký tài khoản</span>
                    </div>

                    <!-- Photo 2: Verification -->
                    <div class="bg-white rounded-xl p-3 border border-slate-200 text-center flex flex-col items-center">
                        <div class="w-24 h-24 rounded-lg bg-slate-100 overflow-hidden border border-slate-200 shadow-sm mb-2 flex items-center justify-center relative">
                            <img id="compVerifyImg" class="w-full h-full object-cover hidden" alt="Ảnh quét vào thi">
                            <span id="compVerifyPlaceholder" class="text-xs text-slate-400">Chưa quét</span>
                            <div id="compVerifyBadge" class="hidden absolute top-1 right-1 px-1.5 py-0.5 rounded text-[9px] font-bold bg-emerald-500 text-white">
                                Khớp
                            </div>
                        </div>
                        <span class="text-[11px] font-bold text-slate-800">2. Xác thực Vào thi</span>
                        <span id="compVerifyTime" class="text-[10px] text-slate-500">Trước khi bắt đầu</span>
                    </div>

                    <!-- Photo 3: Latest Snapshot -->
                    <div class="bg-white rounded-xl p-3 border border-slate-200 text-center flex flex-col items-center">
                        <div class="w-24 h-24 rounded-lg bg-slate-100 overflow-hidden border border-slate-200 shadow-sm mb-2 flex items-center justify-center relative">
                            <img id="compLatestImg" class="w-full h-full object-cover hidden" alt="Ảnh mới nhất">
                            <span id="compLatestPlaceholder" class="text-xs text-slate-400">Chưa có snapshot</span>
                            <div id="compLatestStatusBadge" class="hidden absolute top-1 right-1 px-1.5 py-0.5 rounded text-[9px] font-bold bg-emerald-500 text-white">
                                Bình thường
                            </div>
                        </div>
                        <span class="text-[11px] font-bold text-slate-800">3. Ảnh Chụp lúc thi</span>
                        <span id="compLatestTime" class="text-[10px] text-slate-500">Camera phòng thi</span>
                    </div>
                </div>
            </div>

            <!-- Stats Overview Cards -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 shrink-0">
                <div class="bg-rose-50/70 border border-rose-100 p-3 rounded-2xl text-center">
                    <p class="text-[10px] font-bold uppercase text-rose-600 tracking-wider">Cảnh báo vi phạm</p>
                    <p id="modalCheatWarnings" class="text-xl font-black text-rose-700 mt-0.5">0</p>
                </div>
                <div class="bg-amber-50/70 border border-amber-100 p-3 rounded-2xl text-center">
                    <p class="text-[10px] font-bold uppercase text-amber-600 tracking-wider">Phát hiện điện thoại</p>
                    <p id="modalPhoneViolations" class="text-xl font-black text-amber-700 mt-0.5">0</p>
                </div>
                <div class="bg-purple-50/70 border border-purple-100 p-3 rounded-2xl text-center">
                    <p class="text-[10px] font-bold uppercase text-purple-600 tracking-wider">Nhiều người / Vắng mặt</p>
                    <p id="modalPersonViolations" class="text-xl font-black text-purple-700 mt-0.5">0</p>
                </div>
                <div class="bg-blue-50/70 border border-blue-100 p-3 rounded-2xl text-center">
                    <p class="text-[10px] font-bold uppercase text-blue-600 tracking-wider">Rời màn hình</p>
                    <p id="modalOutOfScreen" class="text-xl font-black text-blue-700 mt-0.5">0s</p>
                </div>
            </div>

            <!-- Nav Tabs between Snapshots and Timeline -->
            <div class="flex items-center justify-between border-b border-gray-100 pb-3 shrink-0">
                <div class="flex items-center gap-2">
                    <button type="button" onclick="switchBehaviorTab('snapshots')" id="tabBtnSnapshots" class="px-4 py-1.5 text-xs font-bold rounded-xl bg-blue-600 text-white shadow-sm flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path></svg>
                        <span>Bộ sưu tập Ảnh AI (<span id="snapshotsCountBadge">0</span>)</span>
                    </button>
                    <button type="button" onclick="switchBehaviorTab('timeline')" id="tabBtnTimeline" class="px-4 py-1.5 text-xs font-bold rounded-xl bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>Nhật ký Thao tác (Timeline)</span>
                    </button>
                </div>

                <div id="timelineFilterButtons" class="hidden flex items-center gap-1.5">
                    <button type="button" onclick="filterTimeline('all')" class="timeline-tab px-3 py-1 text-xs font-semibold rounded-lg bg-blue-600 text-white" data-filter="all">Tất cả</button>
                    <button type="button" onclick="filterTimeline('violations')" class="timeline-tab px-3 py-1 text-xs font-semibold rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200" data-filter="violations">Vi phạm</button>
                    <button type="button" onclick="filterTimeline('screen')" class="timeline-tab px-3 py-1 text-xs font-semibold rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200" data-filter="screen">Rời màn hình</button>
                </div>
            </div>

            <!-- Content Area 1: Snapshots Gallery -->
            <div id="snapshotsContainer" class="flex-1 overflow-y-auto pr-1">
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3.5" id="snapshotsGrid">
                    <div class="col-span-full text-center py-8 text-gray-400 text-sm">Đang tải ảnh giám sát...</div>
                </div>
            </div>

            <!-- Content Area 2: Timeline Event List -->
            <div id="timelineContainer" class="hidden flex-1 overflow-y-auto pr-1 space-y-4">
                <div class="text-center py-8 text-gray-400 text-sm">Đang tải nhật ký thao tác...</div>
            </div>

            <!-- Modal Footer -->
            <div class="pt-3 border-t border-gray-100 flex items-center justify-between shrink-0">
                <span class="text-xs text-gray-400">Nhấp vào bất kỳ ảnh nào để phóng to và xem hộp nhận diện AI (Bounding boxes).</span>
                <button type="button" onclick="closeBehaviorModal()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-xl transition-colors">
                    Đóng cửa sổ
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ====================================================
     SNAPSHOT BOUNDING BOX INSPECTION LIGHTBOX
     ==================================================== -->
<div id="snapshotLightbox" class="fixed inset-0 z-[60] hidden">
    <div class="absolute inset-0 bg-black/85 backdrop-blur-md" onclick="closeSnapshotLightbox()"></div>
    <div class="flex items-center justify-center min-h-screen p-4 sm:p-6">
        <div class="relative bg-slate-900 text-white rounded-3xl overflow-hidden shadow-2xl max-w-3xl w-full p-5 space-y-4 z-10 border border-slate-700">
            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                <div>
                    <h4 class="font-black text-sm text-slate-200">Chi tiết Phân tích AI & Bounding Boxes</h4>
                    <p id="lightboxTime" class="text-xs text-slate-400 mt-0.5">--:--:--</p>
                </div>
                <button type="button" onclick="closeSnapshotLightbox()" class="text-slate-400 hover:text-white p-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Image Viewport with Overlaid Bounding Boxes -->
            <div class="relative aspect-[4/3] w-full max-h-[55vh] bg-black rounded-2xl overflow-hidden flex items-center justify-center">
                <img id="lightboxImg" class="w-full h-full object-contain" alt="Snapshot Phóng to">
                <div id="lightboxBoxLayer" class="absolute inset-0 pointer-events-none"></div>
            </div>

            <!-- Details Description & Detected Labels -->
            <div class="bg-slate-800/80 rounded-xl p-3 border border-slate-700 text-xs space-y-2">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-slate-300">Kết quả phân tích:</span>
                    <span id="lightboxStatusBadge" class="px-2.5 py-0.5 rounded text-[10px] font-bold bg-emerald-500 text-white">Bình thường</span>
                </div>
                <p id="lightboxDetails" class="text-slate-400 leading-relaxed"></p>
                <div id="lightboxLabelsList" class="flex flex-wrap gap-1.5 pt-1"></div>
            </div>
        </div>
    </div>
</div>

<script>
    const examId = '{{ $exam->id }}';
    let currentAttemptId = null;
    let currentLogsData = [];
    let currentSnapshotsData = [];
    let currentActiveFilter = 'all';
    let currentTab = 'snapshots';

    function openStudentBehaviorModal(attemptId) {
        currentAttemptId = attemptId;
        document.getElementById('behaviorModal').classList.remove('hidden');
        switchBehaviorTab('snapshots');
        loadStudentBehaviorData(attemptId);
    }

    function closeBehaviorModal() {
        document.getElementById('behaviorModal').classList.add('hidden');
        currentAttemptId = null;
    }

    function switchBehaviorTab(tab) {
        currentTab = tab;
        const btnSnap = document.getElementById('tabBtnSnapshots');
        const btnTime = document.getElementById('tabBtnTimeline');
        const snapContainer = document.getElementById('snapshotsContainer');
        const timeContainer = document.getElementById('timelineContainer');
        const filterBtns = document.getElementById('timelineFilterButtons');

        if (tab === 'snapshots') {
            btnSnap.className = 'px-4 py-1.5 text-xs font-bold rounded-xl bg-blue-600 text-white shadow-sm flex items-center gap-1.5';
            btnTime.className = 'px-4 py-1.5 text-xs font-bold rounded-xl bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors flex items-center gap-1.5';
            snapContainer.classList.remove('hidden');
            timeContainer.classList.add('hidden');
            filterBtns.classList.add('hidden');
        } else {
            btnTime.className = 'px-4 py-1.5 text-xs font-bold rounded-xl bg-blue-600 text-white shadow-sm flex items-center gap-1.5';
            btnSnap.className = 'px-4 py-1.5 text-xs font-bold rounded-xl bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors flex items-center gap-1.5';
            timeContainer.classList.remove('hidden');
            snapContainer.classList.add('hidden');
            filterBtns.classList.remove('hidden');
        }
    }

    function loadStudentBehaviorData(attemptId) {
        fetch(`/giang-vien/de-thi/${examId}/sinh-vien/${attemptId}/hanh-vi`)
            .then(res => res.json())
            .then(data => {
                if (data.error) {
                    alert(data.error);
                    return;
                }

                // Fill Student Info
                const avatarEl = document.getElementById('modalStudentAvatar');
                if (data.student.enrolled_image_url) {
                    avatarEl.innerHTML = `<img src="${data.student.enrolled_image_url}" class="w-full h-full object-cover">`;
                } else {
                    avatarEl.textContent = data.student.initial || '?';
                }
                document.getElementById('modalStudentName').textContent = data.student.name;
                document.getElementById('modalStudentCode').textContent = data.student.code;
                document.getElementById('modalStudentEmail').textContent = data.student.email;
                document.getElementById('modalAttemptStatus').textContent = data.attempt.status_text;
                
                // Fill Stats Overview
                document.getElementById('modalCheatWarnings').textContent = data.stats.cheat_warnings;
                document.getElementById('modalPhoneViolations').textContent = data.stats.phone_violations || 0;
                document.getElementById('modalPersonViolations').textContent = (data.stats.multiple_persons || 0) + (data.stats.face_absent || 0);
                document.getElementById('modalOutOfScreen').textContent = `${data.stats.out_of_screen_time}s`;

                // Fill 3 Comparison Photos
                // 1. Enrolled
                if (data.student.enrolled_image_url) {
                    document.getElementById('compEnrolledImg').src = data.student.enrolled_image_url;
                    document.getElementById('compEnrolledImg').classList.remove('hidden');
                    document.getElementById('compEnrolledPlaceholder').classList.add('hidden');
                } else {
                    document.getElementById('compEnrolledImg').classList.add('hidden');
                    document.getElementById('compEnrolledPlaceholder').classList.remove('hidden');
                }

                // 2. Verification
                if (data.attempt.verification_image_url) {
                    document.getElementById('compVerifyImg').src = data.attempt.verification_image_url;
                    document.getElementById('compVerifyImg').classList.remove('hidden');
                    document.getElementById('compVerifyPlaceholder').classList.add('hidden');
                    document.getElementById('compVerifyBadge').classList.remove('hidden');
                    document.getElementById('compVerifyBadge').innerText = `${data.attempt.face_similarity ?? 70}% Khớp`;
                    document.getElementById('compVerifyTime').innerText = data.attempt.face_verified_at || 'Đã xác thực';
                    
                    document.getElementById('faceMatchScoreBadge').className = 'px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800';
                    document.getElementById('faceMatchScoreBadge').innerText = `Trùng khớp ${data.attempt.face_similarity ?? 70}%`;
                } else {
                    document.getElementById('compVerifyImg').classList.add('hidden');
                    document.getElementById('compVerifyPlaceholder').classList.remove('hidden');
                    document.getElementById('compVerifyBadge').classList.add('hidden');
                    document.getElementById('compVerifyTime').innerText = 'Chưa quét';

                    document.getElementById('faceMatchScoreBadge').className = 'px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-500';
                    document.getElementById('faceMatchScoreBadge').innerText = 'Chưa quét Face ID';
                }

                // 3. Latest in-exam snapshot
                currentSnapshotsData = data.snapshots || [];
                document.getElementById('snapshotsCountBadge').innerText = currentSnapshotsData.length;

                if (currentSnapshotsData.length > 0) {
                    const latest = currentSnapshotsData[0];
                    document.getElementById('compLatestImg').src = latest.image_url;
                    document.getElementById('compLatestImg').classList.remove('hidden');
                    document.getElementById('compLatestPlaceholder').classList.add('hidden');
                    
                    const badge = document.getElementById('compLatestStatusBadge');
                    badge.classList.remove('hidden');
                    if (latest.status === 'violation') {
                        badge.className = 'absolute top-1 right-1 px-1.5 py-0.5 rounded text-[9px] font-bold bg-rose-500 text-white';
                        badge.innerText = 'Vi phạm';
                    } else {
                        badge.className = 'absolute top-1 right-1 px-1.5 py-0.5 rounded text-[9px] font-bold bg-emerald-500 text-white';
                        badge.innerText = 'Bình thường';
                    }
                    document.getElementById('compLatestTime').innerText = latest.captured_time || latest.captured_at;
                } else {
                    document.getElementById('compLatestImg').classList.add('hidden');
                    document.getElementById('compLatestPlaceholder').classList.remove('hidden');
                    document.getElementById('compLatestStatusBadge').classList.add('hidden');
                    document.getElementById('compLatestTime').innerText = 'Chưa có ảnh';
                }

                // Render Snapshots Gallery & Timeline
                renderSnapshotsGrid();
                currentLogsData = data.logs || [];
                renderTimeline();
            })
            .catch(err => {
                console.error(err);
            });
    }

    function renderSnapshotsGrid() {
        const grid = document.getElementById('snapshotsGrid');
        grid.innerHTML = '';

        if (currentSnapshotsData.length === 0) {
            grid.innerHTML = `
                <div class="col-span-full bg-gray-50 rounded-2xl p-8 text-center border border-gray-100">
                    <p class="text-xs text-gray-500">Chưa có ảnh chụp giám sát nào trong bài thi này.</p>
                </div>
            `;
            return;
        }

        currentSnapshotsData.forEach((snap, idx) => {
            const isViolation = snap.status === 'violation';
            const card = document.createElement('div');
            card.className = `bg-white rounded-2xl p-2.5 border ${isViolation ? 'border-rose-300 ring-2 ring-rose-100' : 'border-gray-200'} shadow-sm hover:shadow-md transition-all cursor-pointer flex flex-col justify-between`;
            card.onclick = () => openSnapshotLightbox(idx);

            let tagHtml = `<span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">Hợp lệ</span>`;
            if (isViolation) {
                tagHtml = `<span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-800">Vi phạm AI</span>`;
            }

            card.innerHTML = `
                <div class="relative aspect-[4/3] rounded-xl overflow-hidden bg-slate-900 mb-2">
                    <img src="${snap.image_url}" class="w-full h-full object-cover">
                    <div class="absolute top-1.5 right-1.5">${tagHtml}</div>
                    <div class="absolute bottom-1.5 left-1.5 px-1.5 py-0.5 bg-black/60 rounded text-[9px] font-mono text-white">
                        ${snap.captured_time || snap.captured_at}
                    </div>
                </div>
                <p class="text-[11px] text-gray-600 line-clamp-2 leading-tight">${snap.details || 'Khung hình bình thường.'}</p>
            `;
            grid.appendChild(card);
        });
    }

    function openSnapshotLightbox(idx) {
        const snap = currentSnapshotsData[idx];
        if (!snap) return;

        document.getElementById('lightboxImg').src = snap.image_url;
        document.getElementById('lightboxTime').innerText = `Chụp lúc: ${snap.captured_at || ''}`;
        document.getElementById('lightboxDetails').innerText = snap.details || 'Không ghi nhận vi phạm.';
        
        const badge = document.getElementById('lightboxStatusBadge');
        if (snap.status === 'violation') {
            badge.className = 'px-2.5 py-0.5 rounded text-[10px] font-bold bg-rose-500 text-white';
            badge.innerText = 'Phát hiện vi phạm (AI Flag)';
        } else {
            badge.className = 'px-2.5 py-0.5 rounded text-[10px] font-bold bg-emerald-500 text-white';
            badge.innerText = 'Bình thường';
        }

        // Draw Bounding Boxes
        const boxLayer = document.getElementById('lightboxBoxLayer');
        boxLayer.innerHTML = '';

        const labelsList = document.getElementById('lightboxLabelsList');
        labelsList.innerHTML = '';

        if (snap.detections && snap.detections.length > 0) {
            snap.detections.forEach(det => {
                const isCheat = ['cell phone', 'book'].includes(det.label);
                const isSecondaryPerson = det.label === 'person';
                const boxColor = isCheat ? '#ef4444' : (isSecondaryPerson ? '#f59e0b' : '#10b981');

                const tag = document.createElement('span');
                tag.className = `px-2 py-0.5 rounded text-[10px] font-bold text-white`;
                tag.style.backgroundColor = boxColor;
                tag.innerText = `${det.label} (${det.confidence}%)`;
                labelsList.appendChild(tag);

                if (det.normalized_box) {
                    const [nx1, ny1, nx2, ny2] = det.normalized_box;
                    const boxEl = document.createElement('div');
                    boxEl.style.position = 'absolute';
                    boxEl.style.left = `${nx1 * 100}%`;
                    boxEl.style.top = `${ny1 * 100}%`;
                    boxEl.style.width = `${(nx2 - nx1) * 100}%`;
                    boxEl.style.height = `${(ny2 - ny1) * 100}%`;
                    boxEl.style.border = `2px solid ${boxColor}`;
                    boxEl.style.borderRadius = '6px';
                    boxEl.style.boxShadow = `0 0 8px ${boxColor}88`;

                    const labelEl = document.createElement('span');
                    labelEl.style.position = 'absolute';
                    labelEl.style.top = '-20px';
                    labelEl.style.left = '0';
                    labelEl.style.backgroundColor = boxColor;
                    labelEl.style.color = '#fff';
                    labelEl.style.fontSize = '10px';
                    labelEl.style.fontWeight = 'bold';
                    labelEl.style.padding = '1px 4px';
                    labelEl.style.borderRadius = '4px';
                    labelEl.style.whiteSpace = 'nowrap';
                    labelEl.innerText = `${det.label} ${det.confidence}%`;
                    boxEl.appendChild(labelEl);

                    boxLayer.appendChild(boxEl);
                }
            });
        }

        document.getElementById('snapshotLightbox').classList.remove('hidden');
    }

    function closeSnapshotLightbox() {
        document.getElementById('snapshotLightbox').classList.add('hidden');
    }

    function filterTimeline(filter) {
        currentActiveFilter = filter;
        document.querySelectorAll('.timeline-tab').forEach(tab => {
            if (tab.dataset.filter === filter) {
                tab.className = 'timeline-tab px-3 py-1 text-xs font-semibold rounded-lg bg-blue-600 text-white';
            } else {
                tab.className = 'timeline-tab px-3 py-1 text-xs font-semibold rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200';
            }
        });
        renderTimeline();
    }

    function renderTimeline() {
        const container = document.getElementById('timelineContainer');
        container.innerHTML = '';

        let filteredLogs = currentLogsData;
        if (currentActiveFilter === 'violations') {
            filteredLogs = currentLogsData.filter(l => l.severity === 'high' || l.severity === 'medium');
        } else if (currentActiveFilter === 'screen') {
            filteredLogs = currentLogsData.filter(l => ['fullscreen_exit', 'fullscreen_enter', 'tab_switch', 'window_blur', 'window_focus'].includes(l.event_type));
        }

        if (filteredLogs.length === 0) {
            container.innerHTML = `
                <div class="bg-gray-50 rounded-2xl p-8 text-center border border-gray-100">
                    <h5 class="font-bold text-gray-800 text-sm">Không có sự kiện nào</h5>
                    <p class="text-xs text-gray-500 mt-1">Chưa ghi nhận sự kiện vi phạm nào theo bộ lọc này.</p>
                </div>
            `;
            return;
        }

        const timelineList = document.createElement('div');
        timelineList.className = 'relative border-l-2 border-gray-200 ml-4 space-y-5 py-2';

        filteredLogs.forEach(log => {
            let dotColor = 'bg-blue-500 ring-4 ring-blue-100';
            if (log.severity === 'high') dotColor = 'bg-rose-500 ring-4 ring-rose-100';
            else if (log.severity === 'medium') dotColor = 'bg-amber-500 ring-4 ring-amber-100';

            const item = document.createElement('div');
            item.className = 'relative pl-6';
            item.innerHTML = `
                <span class="absolute -left-2 top-1.5 w-3.5 h-3.5 rounded-full ${dotColor}"></span>
                <div class="bg-gray-50/80 hover:bg-gray-100/80 transition-colors p-3.5 rounded-2xl border border-gray-200/80 shadow-sm">
                    <div class="flex flex-wrap items-center justify-between gap-2 mb-1.5">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-gray-900">${log.title}</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold border ${log.badge}">${log.event_type}</span>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-mono font-bold text-gray-700">${log.occurred_time || log.occurred_at}</span>
                        </div>
                    </div>
                    <p class="text-xs text-gray-600 leading-relaxed">${log.description}</p>
                    ${log.snapshot_url ? `
                        <div class="mt-2.5 flex items-center gap-3">
                            <a href="${log.snapshot_url}" target="_blank" class="block w-20 h-14 rounded-xl overflow-hidden border border-slate-300 hover:border-blue-500 shadow-sm transition-all group shrink-0">
                                <img src="${log.snapshot_url}" class="w-full h-full object-cover group-hover:scale-105 transition-transform" alt="Snapshot">
                            </a>
                            <a href="${log.snapshot_url}" target="_blank" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 hover:underline">
                                Xem ảnh chụp bằng chứng vi phạm
                            </a>
                        </div>
                    ` : ''}
                    ${log.duration_seconds ? `<div class="mt-2 text-xs font-bold text-amber-700 bg-amber-50 inline-block px-2 py-0.5 rounded-md border border-amber-200">Thời gian vi phạm: ${log.duration_seconds} giây</div>` : ''}
                </div>
            `;
            timelineList.appendChild(item);
        });

        container.appendChild(timelineList);
    }
</script>
@endsection
