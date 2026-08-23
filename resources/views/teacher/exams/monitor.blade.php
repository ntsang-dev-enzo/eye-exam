@extends('layouts.teacher')

@section('title', 'Giám sát phòng thi: ' . $exam->title)

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('teacher.exams.index') }}" class="text-sm text-gray-500 hover:text-blue-600 font-medium">Kỳ thi</a>
                <span class="text-gray-400">/</span>
                <span class="text-sm font-semibold text-gray-900">Giám sát trực tiếp</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 mt-1">Phòng Giám sát Kỳ thi: <span class="text-blue-600">{{ $exam->title }}</span></h1>
            <p class="text-sm text-gray-500 mt-1">Mã đề: <span class="font-bold text-gray-700">{{ $exam->code }}</span> | Môn: <span class="font-bold text-gray-700">{{ $exam->subject->name ?? 'N/A' }}</span></p>
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
                <span class="text-sm font-bold text-gray-900">Hệ thống AI & Chống gian lận đang giám sát thời gian thực</span>
                <p class="text-xs text-gray-500">Tự động đồng bộ mỗi 5 giây. <span class="font-semibold text-blue-600">Nhấp vào bất kỳ sinh viên nào để xem chi tiết hành vi.</span></p>
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
                        <th class="px-6 py-3.5 border-b border-gray-100">Thí sinh</th>
                        <th class="px-6 py-3.5 border-b border-gray-100 text-center">Bắt đầu lúc</th>
                        <th class="px-6 py-3.5 border-b border-gray-100 text-center">Cảnh báo vi phạm</th>
                        <th class="px-6 py-3.5 border-b border-gray-100 text-center">Rời màn hình (giây)</th>
                        <th class="px-6 py-3.5 border-b border-gray-100">Hành vi gần nhất</th>
                        <th class="px-6 py-3.5 border-b border-gray-100 text-center">Trạng thái</th>
                        <th class="px-6 py-3.5 border-b border-gray-100 text-right">Theo dõi</th>
                    </tr>
                </thead>
                <tbody id="monitorTable" class="divide-y divide-gray-100">
                    <tr><td colspan="7" class="px-6 py-12 text-center text-gray-500">Đang tải danh sách thí sinh...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ====================================================
     STUDENT BEHAVIOR TIMELINE MODAL
     ==================================================== -->
<div id="behaviorModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" onclick="closeBehaviorModal()"></div>
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-12 text-center sm:p-0">
        <div class="relative bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-3xl w-full p-6 sm:p-8 space-y-6 max-h-[90vh] flex flex-col z-10">
            
            <!-- Modal Header -->
            <div class="flex items-start justify-between pb-4 border-b border-gray-100 shrink-0">
                <div class="flex items-center gap-4">
                    <div id="modalStudentAvatar" class="w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-700 text-white flex items-center justify-center font-black text-xl shadow-md">
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

            <!-- Stats Overview Cards -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 shrink-0">
                <div class="bg-rose-50/70 border border-rose-100 p-3.5 rounded-2xl text-center">
                    <p class="text-[11px] font-bold uppercase text-rose-600 tracking-wider">Cảnh báo vi phạm</p>
                    <p id="modalCheatWarnings" class="text-2xl font-black text-rose-700 mt-0.5">0</p>
                </div>
                <div class="bg-amber-50/70 border border-amber-100 p-3.5 rounded-2xl text-center">
                    <p class="text-[11px] font-bold uppercase text-amber-600 tracking-wider">Rời màn hình</p>
                    <p id="modalOutOfScreen" class="text-2xl font-black text-amber-700 mt-0.5">0s</p>
                </div>
                <div class="bg-purple-50/70 border border-purple-100 p-3.5 rounded-2xl text-center">
                    <p class="text-[11px] font-bold uppercase text-purple-600 tracking-wider">Sao chép / Dán</p>
                    <p id="modalCopyPastes" class="text-2xl font-black text-purple-700 mt-0.5">0</p>
                </div>
                <div class="bg-blue-50/70 border border-blue-100 p-3.5 rounded-2xl text-center">
                    <p class="text-[11px] font-bold uppercase text-blue-600 tracking-wider">Bắt đầu lúc</p>
                    <p id="modalStartedAt" class="text-sm font-black text-blue-700 mt-1.5 truncate">--:--:--</p>
                </div>
            </div>

            <!-- Behavior Filter Tabs -->
            <div class="flex items-center justify-between gap-2 border-b border-gray-100 pb-3 shrink-0">
                <h4 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Nhật ký Hành vi Chi tiết (Timeline)
                </h4>
                <div class="flex items-center gap-1.5">
                    <button type="button" onclick="filterTimeline('all')" class="timeline-tab px-3 py-1 text-xs font-semibold rounded-lg bg-blue-600 text-white" data-filter="all">Tất cả</button>
                    <button type="button" onclick="filterTimeline('violations')" class="timeline-tab px-3 py-1 text-xs font-semibold rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200" data-filter="violations">Vi phạm</button>
                    <button type="button" onclick="filterTimeline('screen')" class="timeline-tab px-3 py-1 text-xs font-semibold rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200" data-filter="screen">Rời màn hình</button>
                </div>
            </div>

            <!-- Timeline Event List -->
            <div class="flex-1 overflow-y-auto pr-1 space-y-4" id="timelineContainer">
                <div class="text-center py-8 text-gray-400 text-sm">Đang tải dữ liệu nhật ký...</div>
            </div>

            <!-- Modal Footer -->
            <div class="pt-3 border-t border-gray-100 flex items-center justify-between shrink-0">
                <span class="text-xs text-gray-400">💡 Gợi ý: Bạn có thể lưu lại bằng chứng nhật ký nếu thí sinh có hành vi gian lận.</span>
                <button type="button" onclick="refreshCurrentStudentBehavior()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-xl transition-colors flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    Làm mới nhật ký
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    const examId = '{{ $exam->id }}';
    const monitorUrl = '{{ route("teacher.exams.api-monitor", $exam->id) }}';
    let currentAttemptId = null;
    let currentLogsData = [];
    let currentActiveFilter = 'all';
    
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
                    tbody.innerHTML = '<tr><td colspan="7" class="px-6 py-12 text-center text-gray-500">Hiện không có thí sinh nào đang làm bài thi này.</td></tr>';
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
                    
                    const cheatClass = attempt.cheat_warnings > 0 ? 'text-rose-600 font-bold' : 'text-gray-600';
                    const timeClass = attempt.out_of_screen_time > 0 ? 'text-amber-600 font-bold' : 'text-gray-600';
                    
                    const tr = document.createElement('tr');
                    tr.className = `transition-colors ${rowClass}`;
                    tr.onclick = () => openStudentBehaviorModal(attempt.id);
                    tr.innerHTML = `
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-sm shrink-0">
                                    ${attempt.student_name.charAt(0)}
                                </div>
                                <div>
                                    <div class="font-bold text-gray-900">${attempt.student_name}</div>
                                    <div class="text-xs text-gray-500 font-mono">${attempt.student_code}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center text-sm font-mono text-gray-600">${attempt.started_at}</td>
                        <td class="px-6 py-4 text-center text-sm ${cheatClass}">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md ${attempt.cheat_warnings > 0 ? 'bg-rose-100 text-rose-700 font-bold' : 'text-gray-500'}">
                                ${attempt.cheat_warnings} lần
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center text-sm ${timeClass}">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md ${attempt.out_of_screen_time > 0 ? 'bg-amber-100 text-amber-700 font-bold' : 'text-gray-500'}">
                                ${attempt.out_of_screen_time}s
                            </span>
                        </td>
                        <td class="px-6 py-4 text-xs text-gray-600">
                            ${attempt.last_event ? `<span class="font-medium text-gray-800">${attempt.last_event}</span> <span class="text-gray-400">(${attempt.last_event_time})</span>` : '<span class="italic text-gray-400">Chưa ghi nhận</span>'}
                        </td>
                        <td class="px-6 py-4 text-center">${statusBadge}</td>
                        <td class="px-6 py-4 text-right">
                            <button type="button" onclick="event.stopPropagation(); openStudentBehaviorModal(${attempt.id});" class="inline-flex items-center px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold text-xs rounded-xl transition-colors border border-blue-200 shadow-sm">
                                🔍 Xem hành vi
                            </button>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
            })
            .catch(err => console.error('Monitor fetch error:', err));
    }
    
    // Fetch immediately & poll every 5 seconds
    fetchMonitorData();
    setInterval(fetchMonitorData, 5000);

    /* ====================================================
       BEHAVIOR MODAL LOGIC
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

    function loadStudentBehaviorData(attemptId) {
        const container = document.getElementById('timelineContainer');
        container.innerHTML = '<div class="text-center py-8 text-gray-400 text-sm">Đang tải thông tin hành vi...</div>';

        fetch(`/giang-vien/de-thi/${examId}/sinh-vien/${attemptId}/hanh-vi`)
            .then(res => res.json())
            .then(data => {
                if (data.error) {
                    container.innerHTML = `<div class="text-center py-8 text-rose-500">${data.error}</div>`;
                    return;
                }

                // Fill Student Info
                document.getElementById('modalStudentAvatar').textContent = data.student.initial || '?';
                document.getElementById('modalStudentName').textContent = data.student.name;
                document.getElementById('modalStudentCode').textContent = data.student.code;
                document.getElementById('modalStudentEmail').textContent = data.student.email;
                document.getElementById('modalAttemptStatus').textContent = data.attempt.status_text;
                
                // Fill Stats
                document.getElementById('modalCheatWarnings').textContent = data.stats.cheat_warnings;
                document.getElementById('modalOutOfScreen').textContent = `${data.stats.out_of_screen_time}s`;
                document.getElementById('modalCopyPastes').textContent = data.stats.copy_pastes;
                document.getElementById('modalStartedAt').textContent = data.attempt.started_at;

                currentLogsData = data.logs || [];
                renderTimeline();
            })
            .catch(err => {
                console.error(err);
                container.innerHTML = '<div class="text-center py-8 text-rose-500">Lỗi khi tải dữ liệu hành vi.</div>';
            });
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
                    <div class="w-12 h-12 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <h5 class="font-bold text-gray-800 text-sm">Không có sự kiện nào</h5>
                    <p class="text-xs text-gray-500 mt-1">Chưa ghi nhận sự kiện vi phạm nào theo bộ lọc này.</p>
                </div>
            `;
            return;
        }

        const timelineList = document.createElement('div');
        timelineList.className = 'relative border-l-2 border-gray-200 ml-4 space-y-6 py-2';

        filteredLogs.forEach(log => {
            let dotColor = 'bg-blue-500 ring-4 ring-blue-100';
            if (log.severity === 'high') dotColor = 'bg-rose-500 ring-4 ring-rose-100';
            else if (log.severity === 'medium') dotColor = 'bg-amber-500 ring-4 ring-amber-100';
            else if (log.event_type.includes('enter') || log.event_type.includes('focus')) dotColor = 'bg-emerald-500 ring-4 ring-emerald-100';

            const item = document.createElement('div');
            item.className = 'relative pl-6';
            item.innerHTML = `
                <span class="absolute -left-2 top-1.5 w-3.5 h-3.5 rounded-full ${dotColor}"></span>
                <div class="bg-gray-50/80 hover:bg-gray-100/80 transition-colors p-4 rounded-2xl border border-gray-200/80 shadow-sm">
                    <div class="flex flex-wrap items-center justify-between gap-2 mb-1.5">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-gray-900">${log.title}</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold border ${log.badge}">${log.event_type}</span>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-mono font-bold text-gray-700">${log.occurred_time || log.occurred_at}</span>
                            <span class="text-[10px] text-gray-400 ml-1">(${log.time_diff || ''})</span>
                        </div>
                    </div>
                    <p class="text-xs text-gray-600 leading-relaxed">${log.description}</p>
                    ${log.duration_seconds ? `<div class="mt-2 text-xs font-bold text-amber-700 bg-amber-50 inline-block px-2 py-0.5 rounded-md border border-amber-200">Thời gian vi phạm: ${log.duration_seconds} giây</div>` : ''}
                </div>
            `;
            timelineList.appendChild(item);
        });

        container.appendChild(timelineList);
    }
</script>
@endsection
