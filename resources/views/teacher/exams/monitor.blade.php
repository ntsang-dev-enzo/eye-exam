@extends('layouts.teacher')

@section('title', 'Giám sát phòng thi: ' . $exam->title)

@section('content')
<div class="space-y-6 pb-12">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('teacher.exams.index') }}" class="text-sm text-gray-500 hover:text-blue-600 font-medium">Kỳ thi</a>
                <span class="text-gray-400">/</span>
                <span class="text-sm font-semibold text-gray-900">Giám sát trực tiếp</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 mt-1">Phòng Giám sát Kỳ thi: <span class="text-blue-600">{{ $exam->title }}</span></h1>
            <p class="text-sm text-gray-500 mt-1">Mã đề: <span class="font-bold text-gray-700">{{ $exam->code }}</span> | Môn: <span class="font-bold text-gray-700">{{ $exam->subject->name ?? 'N/A' }}</span> | Chu kỳ chụp AI: <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200/80">{{ $exam->proctor_interval_seconds ?? 120 }}s / lần</span></p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('teacher.exams.results', $exam) }}" class="px-4 py-2 bg-indigo-50 border border-indigo-200 text-indigo-700 rounded-xl hover:bg-indigo-100 transition-colors font-semibold text-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                Xem bảng điểm & kết quả
            </a>
            <a href="{{ route('teacher.exams.index') }}" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors font-medium text-sm">
                Quay lại
            </a>
        </div>
    </div>

    <!-- Live Status Banner -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <span class="relative flex h-3.5 w-3.5">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-3.5 w-3.5 bg-emerald-500"></span>
            </span>
            <div>
                <span class="text-sm font-bold text-gray-900">Giám sát phòng thi thời gian thực</span>
            </div>
        </div>

        <div class="flex items-center gap-4 text-xs font-semibold text-gray-600">
            <div class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                <span>Bình thường</span>
            </div>
            <div class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                <span>Cảnh báo nhẹ</span>
            </div>
            <div class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-full bg-rose-500"></span>
                <span>Vi phạm nghiêm trọng</span>
            </div>
        </div>
    </div>

    <!-- Students Monitor Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-bold text-gray-900 text-base">Danh sách thí sinh đang trong phòng thi</h3>
            <span id="activeCountBadge" class="px-3 py-1 bg-blue-50 text-blue-700 text-xs font-bold rounded-full">0 thí sinh</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/60 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <th class="px-5 py-3.5 border-b border-gray-100">Thí sinh</th>
                        <th class="px-4 py-3.5 border-b border-gray-100 text-center">Xác thực Face ID</th>
                        <th class="px-4 py-3.5 border-b border-gray-100 text-center">Camera AI</th>
                        <th class="px-4 py-3.5 border-b border-gray-100 text-center">Bắt đầu lúc</th>
                        <th class="px-4 py-3.5 border-b border-gray-100 text-center">Cảnh báo vi phạm</th>
                        <th class="px-4 py-3.5 border-b border-gray-100 text-center">Rời màn hình</th>
                        <th class="px-5 py-3.5 border-b border-gray-100">Hành vi gần nhất</th>
                        <th class="px-4 py-3.5 border-b border-gray-100 text-center">Trạng thái</th>
                        <th class="px-5 py-3.5 border-b border-gray-100 text-right">Chi tiết</th>
                    </tr>
                </thead>
                <tbody id="monitorTable" class="divide-y divide-gray-100">
                    <tr><td colspan="9" class="px-6 py-12 text-center text-gray-500">Đang tải danh sách thí sinh...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ====================================================
     STUDENT BEHAVIOR & FACE COMPARISON MODAL
     ==================================================== -->
<div id="behaviorModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" onclick="closeBehaviorModal()"></div>
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-12 text-center sm:p-0">
        <div class="relative bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-4xl w-full p-6 sm:p-8 space-y-6 max-h-[92vh] flex flex-col z-10">
            
            <!-- Modal Header -->
            <div class="flex items-start justify-between pb-4 border-b border-gray-100 shrink-0">
                <div class="flex items-center gap-4">
                    <div id="modalStudentAvatar" class="w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-700 text-white flex items-center justify-center font-black text-xl shadow-md overflow-hidden">
                        ?
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 id="modalStudentName" class="text-xl font-black text-gray-900">Đang tải...</h3>
                            <span id="modalAttemptStatus" class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-700">Đang thi</span>
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

            <!-- Before/After Face Comparison Section -->
            <div class="bg-slate-50/80 rounded-2xl p-4 border border-slate-200/80 shrink-0">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-2">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        Đối chiếu danh tính khuôn mặt Trước / Trong giờ thi (Face Verification)
                    </h4>
                    <span id="faceMatchScoreBadge" class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800">
                        Đang tải...
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <!-- Photo 1: Enrolled Profile -->
                    <div class="bg-white rounded-xl p-3 border border-slate-200 text-center flex flex-col items-center">
                        <div class="w-24 h-24 rounded-lg bg-slate-100 overflow-hidden border border-slate-200 shadow-sm mb-2 flex items-center justify-center">
                            <img id="compEnrolledImg" class="w-full h-full object-cover hidden" alt="Ảnh hồ sơ gốc">
                            <span id="compEnrolledPlaceholder" class="text-xs text-slate-400">Chưa có ảnh</span>
                        </div>
                        <span class="text-[11px] font-bold text-slate-800">1. Ảnh Hồ sơ gốc</span>
                        <span class="text-[10px] text-slate-500">Đăng ký ban đầu</span>
                    </div>

                    <!-- Photo 2: Pre-exam Verification Scan -->
                    <div class="bg-white rounded-xl p-3 border border-slate-200 text-center flex flex-col items-center">
                        <div class="w-24 h-24 rounded-lg bg-slate-100 overflow-hidden border border-slate-200 shadow-sm mb-2 flex items-center justify-center relative">
                            <img id="compVerifyImg" class="w-full h-full object-cover hidden" alt="Ảnh quét trước thi">
                            <span id="compVerifyPlaceholder" class="text-xs text-slate-400">Chưa quét</span>
                            <div id="compVerifyBadge" class="hidden absolute bottom-1 right-1 px-1.5 py-0.5 bg-emerald-600 text-white rounded text-[9px] font-bold">
                                92%
                            </div>
                        </div>
                        <span class="text-[11px] font-bold text-slate-800">2. Ảnh Quét trước thi</span>
                        <span id="compVerifyTime" class="text-[10px] text-slate-500">Trước khi vào phòng</span>
                    </div>

                    <!-- Photo 3: Latest Exam Snapshot -->
                    <div class="bg-white rounded-xl p-3 border border-slate-200 text-center flex flex-col items-center">
                        <div class="w-24 h-24 rounded-lg bg-slate-100 overflow-hidden border border-slate-200 shadow-sm mb-2 flex items-center justify-center relative">
                            <img id="compLatestImg" class="w-full h-full object-cover hidden" alt="Ảnh mới nhất">
                            <span id="compLatestPlaceholder" class="text-xs text-slate-400">Chưa có snapshot</span>
                            <div id="compLatestStatusBadge" class="hidden absolute top-1 right-1 px-1.5 py-0.5 rounded text-[9px] font-bold bg-emerald-500 text-white">
                                Bình thường
                            </div>
                        </div>
                        <span class="text-[11px] font-bold text-slate-800">3. Ảnh Mới nhất</span>
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

            <!-- Nav Tabs between Timeline and Proctor Snapshots -->
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
                <span class="text-xs text-gray-400">💡 Gợi ý: Nhấp vào bất kỳ ảnh nào để phóng to và xem các hộp nhận diện YOLO (Bounding boxes).</span>
                <button type="button" onclick="refreshCurrentStudentBehavior()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-xl transition-colors flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    Làm mới dữ liệu
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
    const monitorUrl = '{{ route("teacher.exams.api-monitor", $exam->id) }}';
    let currentAttemptId = null;
    let currentLogsData = [];
    let currentSnapshotsData = [];
    let currentActiveFilter = 'all';
    let currentTab = 'snapshots';
    
    function fetchMonitorData() {
        fetch(monitorUrl)
            .then(res => res.json())
            .then(data => {
                const tbody = document.getElementById('monitorTable');
                const badge = document.getElementById('activeCountBadge');
                tbody.innerHTML = '';
                
                const attempts = data.attempts || [];
                badge.textContent = `${attempts.length} thí sinh`;

                if(attempts.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="9" class="px-6 py-12 text-center text-gray-500">Hiện không có thí sinh nào đang làm bài thi này.</td></tr>';
                    return;
                }
                
                attempts.forEach(attempt => {
                    const isSevere = attempt.cheat_warnings >= 3 || attempt.out_of_screen_time > 30;
                    const isWarning = attempt.cheat_warnings > 0 || attempt.out_of_screen_time > 10;
                    
                    let rowClass = 'hover:bg-blue-50/40 cursor-pointer';
                    let statusBadge = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">Bình thường</span>';
                    
                    if (isSevere) {
                        rowClass = 'bg-rose-50/60 hover:bg-rose-100/60 cursor-pointer';
                        statusBadge = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800 animate-pulse">Vi phạm nhiều</span>';
                    } else if (isWarning) {
                        rowClass = 'bg-amber-50/40 hover:bg-amber-100/40 cursor-pointer';
                        statusBadge = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800">Cảnh báo</span>';
                    }

                    // Face Verification Badge
                    let faceBadge = '<span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-gray-100 text-gray-500">Chưa quét</span>';
                    if (attempt.face_verified) {
                        faceBadge = `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">Đạt ${attempt.face_similarity ?? 70}%</span>`;
                    }

                    // Latest Camera Snapshot Thumbnail
                    let cameraCol = '<span class="text-xs text-gray-400 italic">Chưa có</span>';
                    if (attempt.latest_snapshot_url) {
                        const borderC = attempt.latest_snapshot_status === 'violation' ? 'border-rose-500 ring-2 ring-rose-200' : 'border-emerald-500';
                        cameraCol = `
                            <div class="relative w-12 h-10 rounded-lg overflow-hidden border ${borderC} mx-auto shadow-xs group">
                                <img src="${attempt.latest_snapshot_url}" class="w-full h-full object-cover">
                            </div>
                        `;
                    }
                    
                    const tr = document.createElement('tr');
                    tr.className = `transition-colors ${rowClass}`;
                    tr.onclick = () => openStudentBehaviorModal(attempt.id);
                    tr.innerHTML = `
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-sm shrink-0 overflow-hidden">
                                    ${attempt.enrolled_image_url ? `<img src="${attempt.enrolled_image_url}" class="w-full h-full object-cover">` : attempt.student_name.charAt(0)}
                                </div>
                                <div>
                                    <div class="font-bold text-gray-900">${attempt.student_name}</div>
                                    <div class="text-xs text-gray-500 font-mono">${attempt.student_code}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4 text-center">${faceBadge}</td>
                        <td class="px-4 py-4 text-center">${cameraCol}</td>
                        <td class="px-4 py-4 text-center text-sm font-mono text-gray-600">${attempt.started_at}</td>
                        <td class="px-4 py-4 text-center text-sm">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md ${attempt.cheat_warnings > 0 ? 'bg-rose-100 text-rose-700 font-bold' : 'text-gray-500'}">
                                ${attempt.cheat_warnings} lần
                            </span>
                        </td>
                        <td class="px-4 py-4 text-center text-sm">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md ${attempt.out_of_screen_time > 0 ? 'bg-amber-100 text-amber-700 font-bold' : 'text-gray-500'}">
                                ${attempt.out_of_screen_time}s
                            </span>
                        </td>
                        <td class="px-5 py-4 text-xs text-gray-600">
                            ${attempt.last_event ? `<span class="font-medium text-gray-800">${attempt.last_event}</span> <span class="text-gray-400">(${attempt.last_event_time})</span>` : '<span class="italic text-gray-400">Chưa ghi nhận</span>'}
                        </td>
                        <td class="px-4 py-4 text-center">${statusBadge}</td>
                        <td class="px-5 py-4 text-right">
                            <button type="button" onclick="event.stopPropagation(); openStudentBehaviorModal(${attempt.id});" class="inline-flex items-center px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold text-xs rounded-xl transition-colors border border-blue-200 shadow-sm">
                                🔍 So sánh & Hành vi
                            </button>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
            })
            .catch(err => console.error('Monitor fetch error:', err));
    }
    
    fetchMonitorData();
    setInterval(fetchMonitorData, 5000);

    /* ====================================================
       BEHAVIOR MODAL & FACE COMPARISON
       ==================================================== */
    function openStudentBehaviorModal(attemptId) {
        currentAttemptId = attemptId;
        document.getElementById('behaviorModal').classList.remove('hidden');
        loadStudentBehaviorData(attemptId);
    }

    function closeBehaviorModal() {
        document.getElementById('behaviorModal').classList.add('hidden');
        currentAttemptId = null;
    }

    function refreshCurrentStudentBehavior() {
        if (currentAttemptId) {
            loadStudentBehaviorData(currentAttemptId);
        }
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

                // Fill Comparison 3 Photos
                // 1. Enrolled
                if (data.student.enrolled_image_url) {
                    document.getElementById('compEnrolledImg').src = data.student.enrolled_image_url;
                    document.getElementById('compEnrolledImg').classList.remove('hidden');
                    document.getElementById('compEnrolledPlaceholder').classList.add('hidden');
                } else {
                    document.getElementById('compEnrolledImg').classList.add('hidden');
                    document.getElementById('compEnrolledPlaceholder').classList.remove('hidden');
                }

                // 2. Pre-exam verified
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
                document.getElementById('snapshotsCountBadge').textContent = currentSnapshotsData.length;

                if (currentSnapshotsData.length > 0) {
                    const latest = currentSnapshotsData[0];
                    document.getElementById('compLatestImg').src = latest.image_url;
                    document.getElementById('compLatestImg').classList.remove('hidden');
                    document.getElementById('compLatestPlaceholder').classList.add('hidden');
                    document.getElementById('compLatestTime').innerText = latest.captured_time || latest.captured_at;

                    const statusBadge = document.getElementById('compLatestStatusBadge');
                    statusBadge.classList.remove('hidden');
                    const simText = latest.face_similarity ? ` • ${latest.face_similarity}% Khớp` : '';
                    if (latest.status === 'violation') {
                        statusBadge.className = 'absolute top-1 right-1 px-1.5 py-0.5 rounded text-[9px] font-bold bg-rose-500 text-white';
                        statusBadge.innerText = 'Cảnh báo AI' + simText;
                    } else {
                        statusBadge.className = 'absolute top-1 right-1 px-1.5 py-0.5 rounded text-[9px] font-bold bg-emerald-500 text-white';
                        statusBadge.innerText = latest.face_similarity ? `${latest.face_similarity}% Khớp` : 'Bình thường';
                    }
                } else {
                    document.getElementById('compLatestImg').classList.add('hidden');
                    document.getElementById('compLatestPlaceholder').classList.remove('hidden');
                    document.getElementById('compLatestStatusBadge').classList.add('hidden');
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
                tagHtml = `<span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-800 animate-pulse">Vi phạm AI</span>`;
            }
            const simBadge = snap.face_similarity ? `<span class="px-1.5 py-0.5 rounded text-[9px] font-bold ${snap.face_similarity >= 50 ? 'bg-indigo-600' : 'bg-rose-600'} text-white shadow-2xs">${snap.face_similarity}% Khớp</span>` : '';

            card.innerHTML = `
                <div class="relative aspect-[4/3] rounded-xl overflow-hidden bg-slate-900 mb-2">
                    <img src="${snap.image_url}" class="w-full h-full object-cover">
                    <div class="absolute top-1.5 right-1.5 flex items-center gap-1">${simBadge}${tagHtml}</div>
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

        if (snap.face_similarity) {
            const faceTag = document.createElement('span');
            faceTag.className = 'px-2 py-0.5 rounded text-[10px] font-bold text-white bg-indigo-600';
            faceTag.innerText = `Face ID: ${snap.face_similarity}% Khớp`;
            labelsList.appendChild(faceTag);
        }

        if (snap.detections && snap.detections.length > 0) {
            snap.detections.forEach(det => {
                const isCheat = ['cell phone', 'book'].includes(det.label);
                const isSecondaryPerson = det.label === 'person';
                const boxColor = isCheat ? '#ef4444' : (isSecondaryPerson ? '#f59e0b' : '#10b981');

                // Label tag
                const tag = document.createElement('span');
                tag.className = `px-2 py-0.5 rounded text-[10px] font-bold text-white`;
                tag.style.backgroundColor = boxColor;
                tag.innerText = `${det.label} (${det.confidence}%)`;
                labelsList.appendChild(tag);

                // Normalized box [x1, y1, x2, y2]
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
                    labelEl.style.padding = '1px 6px';
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
                </div>
            `;
            timelineList.appendChild(item);
        });

        container.appendChild(timelineList);
    }
</script>
@endsection
