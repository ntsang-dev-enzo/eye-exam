@extends('layouts.teacher')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Giám sát phòng thi</h1>
            <p class="text-slate-500 mt-1">Đề thi: <span class="font-bold text-indigo-600">{{ $exam->title }}</span></p>
        </div>
        <a href="{{ route('teacher.exams.index') }}" class="px-4 py-2 bg-white border border-slate-200 text-slate-600 rounded-lg hover:bg-slate-50 transition-colors font-medium text-sm">
            Quay lại
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
        <div class="flex items-center gap-2 mb-6 text-sm text-slate-600">
            <span class="relative flex h-3 w-3">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
              <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
            </span>
            Đang trực tiếp giám sát (Tự động cập nhật mỗi 5 giây)
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-y border-slate-200">
                        <th class="px-4 py-3 text-sm font-semibold text-slate-600">Sinh viên</th>
                        <th class="px-4 py-3 text-sm font-semibold text-slate-600">Thời gian bắt đầu</th>
                        <th class="px-4 py-3 text-sm font-semibold text-slate-600">Số lần cảnh báo (Cheat)</th>
                        <th class="px-4 py-3 text-sm font-semibold text-slate-600">Thời gian thoát (giây)</th>
                        <th class="px-4 py-3 text-sm font-semibold text-slate-600">Trạng thái</th>
                    </tr>
                </thead>
                <tbody id="monitorTable" class="divide-y divide-slate-100">
                    <tr><td colspan="5" class="px-4 py-8 text-center text-slate-500">Đang tải dữ liệu...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    const examId = '{{ $exam->id }}';
    const monitorUrl = '{{ route("teacher.exams.api-monitor", $exam->id) }}';
    
    function fetchMonitorData() {
        fetch(monitorUrl)
            .then(res => res.json())
            .then(data => {
                const tbody = document.getElementById('monitorTable');
                tbody.innerHTML = '';
                
                if(data.attempts.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="5" class="px-4 py-8 text-center text-slate-500">Hiện không có sinh viên nào đang làm bài.</td></tr>';
                    return;
                }
                
                data.attempts.forEach(attempt => {
                    // Cảnh báo màu đỏ nếu thời gian thoát > 10s hoặc số lần cheat > 0
                    const isWarning = attempt.out_of_screen_time > 10 || attempt.cheat_warnings > 0;
                    const rowClass = isWarning ? 'bg-rose-50/50' : '';
                    const timeClass = attempt.out_of_screen_time > 10 ? 'text-rose-600 font-bold' : 'text-slate-700';
                    const cheatClass = attempt.cheat_warnings > 0 ? 'text-rose-600 font-bold' : 'text-slate-700';
                    const statusText = isWarning ? '<span class="px-2 py-1 bg-rose-100 text-rose-700 rounded-md text-xs font-bold">Cảnh báo</span>' : '<span class="px-2 py-1 bg-emerald-100 text-emerald-700 rounded-md text-xs font-bold">Bình thường</span>';
                    
                    const tr = document.createElement('tr');
                    tr.className = `hover:bg-slate-50 transition-colors ${rowClass}`;
                    tr.innerHTML = `
                        <td class="px-4 py-3">
                            <div class="font-medium text-slate-800">${attempt.student_name}</div>
                            <div class="text-xs text-slate-500">${attempt.student_code}</div>
                        </td>
                        <td class="px-4 py-3 text-sm text-slate-600">${attempt.started_at}</td>
                        <td class="px-4 py-3 text-sm ${cheatClass}">${attempt.cheat_warnings}</td>
                        <td class="px-4 py-3 text-sm ${timeClass}">${attempt.out_of_screen_time} s</td>
                        <td class="px-4 py-3">${statusText}</td>
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
