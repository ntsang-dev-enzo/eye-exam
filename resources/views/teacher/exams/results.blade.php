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
                    <p class="text-[11px] font-bold uppercase text-blue-600 tracking-wider">Điểm bài thi</p>
                    <p id="modalScoreValue" class="text-2xl font-black text-blue-700 mt-0.5">--</p>
                </div>
            </div>

            <!-- Behavior Filter Tabs -->
            <div class="flex items-center justify-between gap-2 border-b border-gray-100 pb-3 shrink-0">
                <h4 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Nhật ký Hành vi Chống gian lận (Timeline)
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
                <span class="text-xs text-gray-400">💡 Nhật ký được lưu trữ chính xác theo thời gian thực để đối soát kết quả.</span>
                <button type="button" onclick="closeBehaviorModal()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-xl transition-colors">
                    Đóng cửa sổ
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    const examId = '{{ $exam->id }}';
    let currentAttemptId = null;
    let currentLogsData = [];
    let currentActiveFilter = 'all';

    function openStudentBehaviorModal(attemptId) {
        currentAttemptId = attemptId;
        document.getElementById('behaviorModal').classList.remove('hidden');
        loadStudentBehaviorData(attemptId);
    }

    function closeBehaviorModal() {
        document.getElementById('behaviorModal').classList.add('hidden');
        currentAttemptId = null;
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
                document.getElementById('modalScoreValue').textContent = data.attempt.score !== null ? `${data.attempt.score} đ` : '--';

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
                    <h5 class="font-bold text-gray-800 text-sm">Không có sự kiện vi phạm nào</h5>
                    <p class="text-xs text-gray-500 mt-1">Thí sinh làm bài nghiêm túc, không ghi nhận hành vi bất thường.</p>
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
