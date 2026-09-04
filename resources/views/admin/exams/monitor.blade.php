@extends('layouts.admin')

@section('title', 'Giám sát: ' . $exam->title)

@section('content')
<div class="space-y-5">
    <!-- Breadcrumb & Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="space-y-1">
            <div class="flex items-center gap-1.5 text-xs text-slate-500">
                <a href="{{ route('admin.exams.index') }}" class="hover:text-blue-600 transition-colors">Quản lý đề thi</a>
                <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-slate-800 font-medium">Giám sát phòng thi</span>
            </div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                {{ $exam->title }}
                <span class="ml-1 text-xs font-mono font-normal text-slate-500">({{ $exam->code }})</span>
            </h1>
            <p class="text-xs text-slate-500">
                Môn: <strong class="text-slate-700">{{ $exam->subject->name ?? 'N/A' }}</strong>
                <span class="mx-1 text-slate-300">•</span>
                Thời lượng: <strong class="text-slate-700">{{ $exam->duration }} phút</strong>
                <span class="mx-1 text-slate-300">•</span>
                Giảng viên: <strong class="text-slate-700">{{ $exam->creator->name ?? 'N/A' }}</strong>
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.exams.index') }}" class="h-9 px-3.5 inline-flex items-center gap-1.5 text-xs font-medium text-slate-700 hover:text-slate-900 bg-white border border-slate-200 hover:bg-slate-50 rounded-md transition-colors">
                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Quay lại danh sách
            </a>
        </div>
    </div>

    <!-- Live Status Banner -->
    <div class="bg-white border border-slate-200 rounded-lg p-3.5 flex items-center justify-between">
        <div class="flex items-center gap-2 text-xs font-medium text-slate-700">
            <span class="relative flex h-2.5 w-2.5">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
            </span>
            <span>Hệ thống AI đang giám sát trực tiếp</span>
        </div>
        <div class="text-[11px] text-slate-400 font-mono">
            Tự động làm mới mỗi 5 giây
        </div>
    </div>

    <!-- Monitoring Table -->
    <div class="bg-white border border-slate-200 rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/75 border-b border-slate-200 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                        <th class="py-3 px-4">Sinh viên làm bài</th>
                        <th class="py-3 px-4">Bắt đầu làm bài</th>
                        <th class="py-3 px-4 text-center">Cảnh báo gian lận (AI)</th>
                        <th class="py-3 px-4 text-center">Rời màn hình</th>
                        <th class="py-3 px-4 text-right">Trạng thái</th>
                    </tr>
                </thead>
                <tbody id="monitorTable" class="divide-y divide-slate-100 text-sm">
                    <tr>
                        <td colspan="5" class="py-8 px-4 text-center text-xs text-slate-500">
                            Đang kết nối và tải danh sách phòng thi...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    const examId = '{{ $exam->id }}';
    const monitorUrl = '{{ route("admin.exams.api-monitor", $exam->id) }}';
    
    function fetchMonitorData() {
        fetch(monitorUrl)
            .then(res => res.json())
            .then(data => {
                const tbody = document.getElementById('monitorTable');
                tbody.innerHTML = '';
                
                if(!data.attempts || data.attempts.length === 0) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="5" class="py-12 px-4 text-center">
                                <div class="max-w-xs mx-auto text-center">
                                    <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2.5">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                    <div class="text-sm font-medium text-slate-900">Phòng thi đang trống</div>
                                    <p class="text-xs text-slate-500 mt-1">Hiện tại chưa có sinh viên nào bắt đầu làm bài thi này.</p>
                                </div>
                            </td>
                        </tr>
                    `;
                    return;
                }
                
                data.attempts.forEach(attempt => {
                    const isWarning = (attempt.out_of_screen_time > 10) || (attempt.cheat_warnings > 0);
                    const rowBg = isWarning ? 'bg-rose-50/40' : 'hover:bg-slate-50/75';
                    const cheatBadge = attempt.cheat_warnings > 0 
                        ? `<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-mono font-semibold bg-rose-100 text-rose-700">${attempt.cheat_warnings} lần</span>`
                        : `<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-mono font-normal text-slate-500 bg-slate-100">0</span>`;
                    
                    const timeBadge = attempt.out_of_screen_time > 10
                        ? `<span class="font-mono text-xs font-bold text-rose-600">${attempt.out_of_screen_time} s</span>`
                        : `<span class="font-mono text-xs text-slate-600">${attempt.out_of_screen_time} s</span>`;
                    
                    const statusBadge = isWarning 
                        ? `<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium bg-rose-50 text-rose-700 border border-rose-200"><span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>Cảnh báo</span>`
                        : `<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Bình thường</span>`;
                    
                    const tr = document.createElement('tr');
                    tr.className = `transition-colors ${rowBg}`;
                    tr.innerHTML = `
                        <td class="py-3 px-4">
                            <div class="font-medium text-slate-900">${attempt.student_name}</div>
                            <div class="text-xs font-mono text-slate-500">${attempt.student_code || '-'}</div>
                        </td>
                        <td class="py-3 px-4 text-xs font-mono text-slate-600">${attempt.started_at}</td>
                        <td class="py-3 px-4 text-center">${cheatBadge}</td>
                        <td class="py-3 px-4 text-center">${timeBadge}</td>
                        <td class="py-3 px-4 text-right">${statusBadge}</td>
                    `;
                    tbody.appendChild(tr);
                });
            })
            .catch(err => console.error('Monitor fetch error:', err));
    }
    
    // Fetch immediately
    fetchMonitorData();
    
    // Fetch every 5 seconds
    setInterval(fetchMonitorData, 5000);
</script>
@endsection
