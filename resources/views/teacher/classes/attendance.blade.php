@extends('layouts.teacher')

@section('title', 'Điểm danh Lớp học')

@section('content')
<div class="max-w-6xl mx-auto space-y-6" x-data="attendanceSheet()">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('teacher.classes.index') }}" class="text-sm font-medium text-gray-500 hover:text-indigo-600 transition-colors">Quản lý lớp</a>
                <span class="text-gray-400">/</span>
                <a href="{{ route('teacher.classes.show', $class) }}" class="text-sm font-medium text-gray-500 hover:text-indigo-600 transition-colors">{{ $class->code }}</a>
                <span class="text-gray-400">/</span>
                <span class="text-sm font-medium text-gray-900">Điểm danh</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900">Điểm danh Lớp: {{ $class->name }}</h1>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('teacher.classes.attendance-history', $class) }}" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-sm rounded-xl transition-colors">
                📋 Lịch sử điểm danh
            </a>
            <a href="{{ route('teacher.classes.show', $class) }}" class="px-4 py-2.5 bg-white border border-gray-200 text-gray-700 font-bold text-sm rounded-xl transition-colors">
                Quay lại
            </a>
        </div>
    </div>

    <!-- Date selector & Quick Actions Bar -->
    <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
        <form method="GET" action="{{ route('teacher.classes.attendance', $class) }}" class="flex items-center gap-3 w-full md:w-auto">
            <label class="text-sm font-bold text-gray-700 whitespace-nowrap">Ngày điểm danh:</label>
            <input type="date" name="date" value="{{ $date }}" onchange="this.form.submit()" class="px-4 py-2 rounded-xl border border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 text-sm font-semibold shadow-sm">
        </form>

        <div class="flex flex-wrap items-center gap-2 w-full md:w-auto justify-end">
            <span class="text-xs font-bold text-gray-500 mr-1">Thao tác nhanh:</span>
            <button type="button" @click="setAll('present')" class="px-3 py-1.5 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 font-bold text-xs rounded-lg border border-emerald-200 transition-colors">
                ✓ Tất cả Có mặt
            </button>
            <button type="button" @click="setAll('absent')" class="px-3 py-1.5 bg-rose-50 text-rose-700 hover:bg-rose-100 font-bold text-xs rounded-lg border border-rose-200 transition-colors">
                ✗ Tất cả Vắng mặt
            </button>
        </div>
    </div>

    <!-- Main Attendance Form -->
    <form method="POST" action="{{ route('teacher.classes.store-attendance', $class) }}" class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden space-y-6">
        @csrf
        <input type="hidden" name="date" value="{{ $date }}">

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 border-b border-gray-100 text-xs uppercase text-gray-500 font-semibold">
                    <tr>
                        <th class="px-6 py-4 w-16 text-center">STT</th>
                        <th class="px-6 py-4">Mã SV</th>
                        <th class="px-6 py-4">Họ và tên</th>
                        <th class="px-6 py-4 text-center">Trạng thái Điểm danh</th>
                        <th class="px-6 py-4">Ghi chú</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($students as $index => $student)
                        @php
                            $rec = $existingRecords->get($student->id);
                            $initialStatus = $rec ? $rec->status : 'present';
                            $initialNote = $rec ? $rec->note : '';
                        @endphp
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 text-center text-gray-400 font-medium">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 font-mono font-bold text-gray-900">{{ $student->code ?? 'N/A' }}</td>
                            <td class="px-6 py-4 font-bold text-gray-900">
                                {{ $student->name }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <!-- Present -->
                                    <label class="cursor-pointer">
                                        <input type="radio" name="attendances[{{ $student->id }}]" value="present" x-model="status[{{ $student->id }}]" class="sr-only peer">
                                        <span class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all inline-block peer-checked:bg-emerald-600 peer-checked:text-white bg-gray-100 text-gray-600 hover:bg-gray-200">
                                            Có mặt
                                        </span>
                                    </label>

                                    <!-- Absent -->
                                    <label class="cursor-pointer">
                                        <input type="radio" name="attendances[{{ $student->id }}]" value="absent" x-model="status[{{ $student->id }}]" class="sr-only peer">
                                        <span class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all inline-block peer-checked:bg-rose-600 peer-checked:text-white bg-gray-100 text-gray-600 hover:bg-gray-200">
                                            Vắng mặt
                                        </span>
                                    </label>

                                    <!-- Late -->
                                    <label class="cursor-pointer">
                                        <input type="radio" name="attendances[{{ $student->id }}]" value="late" x-model="status[{{ $student->id }}]" class="sr-only peer">
                                        <span class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all inline-block peer-checked:bg-amber-500 peer-checked:text-white bg-gray-100 text-gray-600 hover:bg-gray-200">
                                            Đi muộn
                                        </span>
                                    </label>

                                    <!-- Excused -->
                                    <label class="cursor-pointer">
                                        <input type="radio" name="attendances[{{ $student->id }}]" value="excused" x-model="status[{{ $student->id }}]" class="sr-only peer">
                                        <span class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all inline-block peer-checked:bg-sky-600 peer-checked:text-white bg-gray-100 text-gray-600 hover:bg-gray-200">
                                            Có phép
                                        </span>
                                    </label>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <input type="text" name="notes[{{ $student->id }}]" value="{{ $initialNote }}" placeholder="Ghi chú (nếu có)..." class="w-full px-3 py-1.5 text-xs rounded-lg border border-gray-200 focus:ring-indigo-500 focus:border-indigo-500">
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                Lớp này chưa có sinh viên nào để điểm danh.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($students->count() > 0)
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                <span class="text-xs text-gray-500 font-medium">Tổng số {{ $students->count() }} sinh viên</span>
                <button type="submit" class="px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm rounded-xl shadow-lg shadow-emerald-500/20 transition-all">
                    💾 Lưu Kết Quả Điểm Danh
                </button>
            </div>
        @endif
    </form>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('attendanceSheet', () => ({
            status: {
                @foreach($students as $st)
                    @php $rec = $existingRecords->get($st->id); @endphp
                    '{{ $st->id }}': '{{ $rec ? $rec->status : "present" }}',
                @endforeach
            },
            
            setAll(val) {
                Object.keys(this.status).forEach(id => {
                    this.status[id] = val;
                });
            }
        }));
    });
</script>
@endsection
