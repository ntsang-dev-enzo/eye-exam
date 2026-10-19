@extends('layouts.teacher')

@section('title', 'Giao đề thi cho lớp & sinh viên')

@section('content')
<div class="max-w-6xl mx-auto space-y-6 pb-12">
    <!-- Breadcrumb & Header -->
    <div class="flex items-center justify-between gap-4">
        <div class="space-y-1">
            <div class="flex items-center gap-2">
                <a href="{{ route('teacher.exams.index') }}" class="text-sm font-medium text-gray-500 hover:text-blue-600 transition-colors">Quản lý kỳ thi</a>
                <span class="text-gray-400">/</span>
                <span class="text-sm font-semibold text-gray-900">Giao đề thi cho sinh viên</span>
            </div>
            <h2 class="text-2xl font-black text-gray-900 tracking-tight">Phân phối & Giao đề thi cho lớp học</h2>
            <p class="text-xs text-gray-500">Hỗ trợ chọn nhiều lớp, lọc sinh viên theo phân phối SOLE 1-2, 1-2-3, 1-2-3-4, chọn từng sinh viên và phân bổ mã đề chéo tự động.</p>
        </div>
        <a href="{{ route('teacher.exams.index') }}" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors font-medium text-sm shadow-2xs">
            Quay lại
        </a>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-xl shadow-xs flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <p class="text-sm text-emerald-800 font-semibold">{{ session('success') }}</p>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-rose-50 border-l-4 border-rose-500 p-4 rounded-r-xl shadow-xs flex items-center gap-2">
            <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            <p class="text-sm text-rose-800 font-semibold">{{ session('error') }}</p>
        </div>
    @endif
    @if($errors->any())
        <div class="bg-rose-50 border-l-4 border-rose-500 p-4 rounded-r-xl shadow-xs">
            <ul class="list-disc pl-5 text-sm text-rose-700 font-medium space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white p-6 sm:p-8 rounded-2xl border border-gray-100 shadow-sm space-y-8" x-data="assignmentForm()">
        
        <!-- Bước 1: Chọn lớp học (Hỗ trợ chọn nhiều lớp) -->
        <div class="border-b border-gray-100 pb-6">
            <div class="flex items-center justify-between mb-3">
                <label class="block text-sm font-bold text-gray-900 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-blue-600 text-white text-xs font-bold flex items-center justify-center">1</span>
                    Chọn lớp học muốn giao đề (Có thể chọn nhiều lớp)
                </label>
                <span class="text-xs text-gray-400">Đã chọn: <strong class="text-blue-600" x-text="selectedClasses.length"></strong> lớp</span>
            </div>
            
            <form method="GET" action="{{ route('teacher.assignments.create') }}" id="classMultiFilterForm" class="space-y-3">
                @if($preselectedExamId)
                    <input type="hidden" name="exam_id" value="{{ $preselectedExamId }}">
                @endif
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2.5 max-h-48 overflow-y-auto p-1 bg-gray-50/70 rounded-xl border border-gray-200/80">
                    @forelse($classes as $c)
                        <label class="flex items-center gap-2.5 p-2.5 rounded-lg border cursor-pointer transition-all text-xs font-semibold select-none"
                            :class="selectedClasses.includes({{ $c->id }}) ? 'bg-blue-50 border-blue-500 text-blue-800 ring-1 ring-blue-500' : 'bg-white border-gray-200 text-gray-700 hover:border-gray-300'">
                            <input type="checkbox" name="class_ids[]" value="{{ $c->id }}" 
                                :checked="selectedClasses.includes({{ $c->id }})"
                                @change="toggleClass({{ $c->id }})"
                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 w-4 h-4">
                            <span class="truncate">{{ $c->name }} ({{ $c->code }})</span>
                        </label>
                    @empty
                        <div class="col-span-full py-4 text-center text-xs text-gray-400">Bạn chưa được phân công lớp học nào.</div>
                    @endforelse
                </div>
                <div class="flex items-center justify-between pt-1">
                    <div class="flex items-center gap-2 text-xs">
                        <button type="button" @click="selectAllClasses()" class="text-blue-600 hover:underline font-semibold cursor-pointer">Chọn tất cả lớp</button>
                        <span class="text-gray-300">•</span>
                        <button type="button" @click="clearAllClasses()" class="text-gray-500 hover:underline cursor-pointer">Bỏ chọn hết</button>
                    </div>
                    <button type="submit" class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-bold transition-colors shadow-xs cursor-pointer">
                        Tải danh sách sinh viên các lớp
                    </button>
                </div>
            </form>
        </div>

        @if(count($selectedClassIds) > 0 && count($students) > 0)
        <!-- Bước 2 & 3 & 4: Assignment Form -->
        <form method="POST" action="{{ route('teacher.assignments.store') }}" id="assignSubmitForm" class="space-y-8">
            @csrf
            @foreach($selectedClassIds as $cId)
                <input type="hidden" name="class_ids[]" value="{{ $cId }}">
            @endforeach

            <!-- 2. Chọn các đề thi muốn giao -->
            <div class="border-b border-gray-100 pb-6">
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-sm font-bold text-gray-900 flex items-center gap-2">
                        <span class="w-6 h-6 rounded-lg bg-blue-600 text-white text-xs font-bold flex items-center justify-center">2</span>
                        Chọn đề thi muốn giao (Chọn 1 hoặc nhiều đề để giao SOLE chéo)
                    </label>
                    <span class="text-xs text-gray-400">Đã chọn: <strong class="text-blue-600" x-text="selectedExams.length"></strong> đề</span>
                </div>
                <p class="text-xs text-gray-500 mb-3">Nếu bạn chọn từ 2 đề trở lên, hệ thống sẽ hỗ trợ tính năng chia SOLE 1-2, 1-2-3, 1-2-3-4 luân phiên để tránh quay cóp.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 max-h-56 overflow-y-auto p-1">
                    @forelse($exams as $exam)
                        <label class="flex items-start gap-3 p-3.5 rounded-xl border cursor-pointer transition-all text-xs"
                            :class="selectedExams.includes({{ $exam->id }}) ? 'bg-indigo-50/70 border-indigo-600 ring-1 ring-indigo-600' : 'bg-gray-50/60 border-gray-200 hover:border-blue-300'">
                            <input type="checkbox" name="exams[]" value="{{ $exam->id }}"
                                :checked="selectedExams.includes({{ $exam->id }})"
                                @change="toggleExam({{ $exam->id }})"
                                class="mt-0.5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 w-4 h-4">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="px-1.5 py-0.5 rounded font-mono font-bold text-[10px] bg-gray-200 text-gray-800">{{ $exam->code }}</span>
                                    <span class="font-bold text-gray-900 truncate">{{ $exam->title }}</span>
                                </div>
                                <p class="text-[11px] text-gray-500 mt-1">Môn: {{ $exam->subject->name ?? 'N/A' }} • {{ $exam->total_questions }} câu • {{ $exam->duration_minutes }}p</p>
                                <span class="inline-flex items-center gap-1 mt-1 text-[10px] font-semibold {{ $exam->status === 'published' ? 'text-emerald-600' : 'text-gray-500' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $exam->status === 'published' ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                                    <span>{{ $exam->status === 'published' ? 'Đang mở' : 'Đang đóng (sẽ tự mở khi giao)' }}</span>
                                </span>
                            </div>
                        </label>
                    @empty
                        <div class="col-span-full py-6 text-center text-xs text-gray-400">Bạn chưa tạo đề thi nào.</div>
                    @endforelse
                </div>
            </div>

            <!-- 3. Chọn sinh viên: Nhóm SOLE hoặc Chọn từng sinh viên -->
            <div class="border-b border-gray-100 pb-6 space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <label class="block text-sm font-bold text-gray-900 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-lg bg-blue-600 text-white text-xs font-bold flex items-center justify-center">3</span>
                            Chọn danh sách sinh viên được thi
                        </label>
                        <p class="text-xs text-gray-500 mt-0.5">Sử dụng công cụ chọn nhanh theo nhóm SOLE hoặc tích chọn từng sinh viên trong bảng bên dưới.</p>
                    </div>
                    <div class="text-xs text-gray-500 shrink-0">
                        Đang chọn <strong class="text-blue-600 font-bold" x-text="selectedStudents.length"></strong> / <span x-text="allStudentList.length"></span> sinh viên
                    </div>
                </div>

                <!-- SOLE QUICK SELECT & SEARCH TOOLBAR -->
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200/80 space-y-3">
                    <div class="flex items-center justify-between flex-wrap gap-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-700 flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                            Công cụ chọn sinh viên:
                        </span>
                        <div class="flex items-center gap-1.5 text-xs">
                            <button type="button" @click="selectAll()" class="px-3 py-1 bg-white hover:bg-slate-100 border border-slate-300 font-semibold rounded-lg transition-colors cursor-pointer">
                                Chọn tất cả
                            </button>
                            <button type="button" @click="clearAll()" class="px-3 py-1 bg-white hover:bg-rose-50 text-rose-600 border border-slate-300 hover:border-rose-300 font-semibold rounded-lg transition-colors cursor-pointer">
                                Bỏ chọn hết
                            </button>
                        </div>
                    </div>

                    <!-- Sole Pattern Buttons -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 pt-1 text-xs">
                        <!-- SOLE 1-2 (Chẵn / Lẻ) -->
                        <div class="bg-white p-2.5 rounded-xl border border-slate-200 shadow-2xs space-y-1.5">
                            <span class="text-[11px] font-bold text-amber-700 block">SOLE 1-2 (2 nhóm):</span>
                            <div class="flex gap-1.5">
                                <button type="button" @click="selectSolePattern(2, 0)" class="flex-1 py-1 px-2 rounded bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-200 font-semibold text-center transition-colors cursor-pointer" title="Chọn sinh viên STT lẻ: 1, 3, 5, 7, 9...">
                                    Nhóm 1 (Lẻ)
                                </button>
                                <button type="button" @click="selectSolePattern(2, 1)" class="flex-1 py-1 px-2 rounded bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-200 font-semibold text-center transition-colors cursor-pointer" title="Chọn sinh viên STT chẵn: 2, 4, 6, 8, 10...">
                                    Nhóm 2 (Chẵn)
                                </button>
                            </div>
                        </div>

                        <!-- SOLE 1-2-3 (3 nhóm) -->
                        <div class="bg-white p-2.5 rounded-xl border border-slate-200 shadow-2xs space-y-1.5">
                            <span class="text-[11px] font-bold text-indigo-700 block">SOLE 1-2-3 (3 nhóm):</span>
                            <div class="flex gap-1">
                                <button type="button" @click="selectSolePattern(3, 0)" class="flex-1 py-1 px-1.5 rounded bg-indigo-50 hover:bg-indigo-100 text-indigo-900 border border-indigo-200 font-semibold text-center transition-colors cursor-pointer" title="STT: 1, 4, 7, 10...">
                                    Nhóm 1
                                </button>
                                <button type="button" @click="selectSolePattern(3, 1)" class="flex-1 py-1 px-1.5 rounded bg-indigo-50 hover:bg-indigo-100 text-indigo-900 border border-indigo-200 font-semibold text-center transition-colors cursor-pointer" title="STT: 2, 5, 8, 11...">
                                    Nhóm 2
                                </button>
                                <button type="button" @click="selectSolePattern(3, 2)" class="flex-1 py-1 px-1.5 rounded bg-indigo-50 hover:bg-indigo-100 text-indigo-900 border border-indigo-200 font-semibold text-center transition-colors cursor-pointer" title="STT: 3, 6, 9, 12...">
                                    Nhóm 3
                                </button>
                            </div>
                        </div>

                        <!-- SOLE 1-2-3-4 (4 nhóm) -->
                        <div class="bg-white p-2.5 rounded-xl border border-slate-200 shadow-2xs space-y-1.5">
                            <span class="text-[11px] font-bold text-emerald-700 block">SOLE 1-2-3-4 (4 nhóm):</span>
                            <div class="grid grid-cols-4 gap-1">
                                <button type="button" @click="selectSolePattern(4, 0)" class="py-1 px-1 rounded bg-emerald-50 hover:bg-emerald-100 text-emerald-900 border border-emerald-200 font-semibold text-center transition-colors cursor-pointer" title="STT: 1, 5, 9, 13...">
                                    N1
                                </button>
                                <button type="button" @click="selectSolePattern(4, 1)" class="py-1 px-1 rounded bg-emerald-50 hover:bg-emerald-100 text-emerald-900 border border-emerald-200 font-semibold text-center transition-colors cursor-pointer" title="STT: 2, 6, 10, 14...">
                                    N2
                                </button>
                                <button type="button" @click="selectSolePattern(4, 2)" class="py-1 px-1 rounded bg-emerald-50 hover:bg-emerald-100 text-emerald-900 border border-emerald-200 font-semibold text-center transition-colors cursor-pointer" title="STT: 3, 7, 11, 15...">
                                    N3
                                </button>
                                <button type="button" @click="selectSolePattern(4, 3)" class="py-1 px-1 rounded bg-emerald-50 hover:bg-emerald-100 text-emerald-900 border border-emerald-200 font-semibold text-center transition-colors cursor-pointer" title="STT: 4, 8, 12, 16...">
                                    N4
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Search Input for choosing individual students -->
                    <div class="pt-2 border-t border-slate-200/70">
                        <div class="relative">
                            <input type="text" x-model="searchQuery" 
                                placeholder="Tìm kiếm theo tên hoặc mã sinh viên để chọn từng người..." 
                                class="w-full text-xs rounded-xl border-slate-300 py-2 pl-9 pr-4 bg-white placeholder-slate-400 focus:border-blue-500 focus:ring-blue-500">
                            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                    </div>
                </div>

                <!-- Bảng danh sách sinh viên: Tích chọn từng người hoặc chọn hàng loạt -->
                <div class="max-h-96 overflow-y-auto border border-gray-200 rounded-xl shadow-inner">
                    <table class="w-full text-left text-xs text-gray-600">
                        <thead class="bg-gray-100/90 border-b border-gray-200 sticky top-0 z-10 text-gray-700 font-bold uppercase tracking-wider text-[11px]">
                            <tr>
                                <th class="px-4 py-3 w-12 text-center">
                                    <input type="checkbox" :checked="isAllSelected()" @change="toggleAllCheckbox()" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 w-4 h-4 cursor-pointer">
                                </th>
                                <th class="px-4 py-3 w-14 text-center">STT</th>
                                <th class="px-4 py-3">Mã SV</th>
                                <th class="px-4 py-3">Họ và tên</th>
                                <th class="px-4 py-3">Lớp học</th>
                                <th class="px-4 py-3 text-center">Trạng thái</th>
                                <th class="px-4 py-3 text-center">Vị trí SOLE mẫu</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @foreach($students as $index => $student)
                                <tr class="hover:bg-blue-50/50 cursor-pointer transition-colors" 
                                    x-show="matchesSearch({{ $student->id }})"
                                    :class="selectedStudents.includes({{ $student->id }}) ? 'bg-blue-50/30 font-medium' : ''"
                                    @click="toggleStudent({{ $student->id }})">
                                    <td class="px-4 py-3 text-center" @click.stop>
                                        <input type="checkbox" name="students[]" value="{{ $student->id }}" 
                                            :checked="selectedStudents.includes({{ $student->id }})"
                                            @change="toggleStudent({{ $student->id }})"
                                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 w-4 h-4 cursor-pointer">
                                    </td>
                                    <td class="px-4 py-3 text-center font-mono font-bold text-gray-400">{{ $index + 1 }}</td>
                                    <td class="px-4 py-3 font-mono font-bold text-gray-900">{{ $student->code ?? 'N/A' }}</td>
                                    <td class="px-4 py-3 text-gray-900 font-semibold">{{ $student->name }}</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-gray-100 text-gray-700 border border-gray-200">
                                            {{ $student->class_name ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <template x-if="selectedStudents.includes({{ $student->id }})">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-800">
                                                <svg class="w-3 h-3 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                                Đã chọn
                                            </span>
                                        </template>
                                        <template x-if="!selectedStudents.includes({{ $student->id }})">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-gray-100 text-gray-500">
                                                Chưa chọn
                                            </span>
                                        </template>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-block px-1.5 py-0.5 rounded text-[10px] font-mono {{ ($index % 2 === 0) ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-700' }}" title="Vị trí Sole 1-2">
                                            1-2: N{{ ($index % 2) + 1 }}
                                        </span>
                                        <span class="inline-block px-1.5 py-0.5 rounded text-[10px] font-mono ml-1 bg-indigo-50 text-indigo-700" title="Vị trí Sole 1-2-3">
                                            1-2-3: N{{ ($index % 3) + 1 }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Bước 4: Tùy chọn chế độ phân bổ đề thi & 1 NÚT GIAO ĐỀ DUY NHẤT -->
            <div class="space-y-5">
                <div class="flex items-center justify-between">
                    <label class="block text-sm font-bold text-gray-900 flex items-center gap-2">
                        <span class="w-6 h-6 rounded-lg bg-blue-600 text-white text-xs font-bold flex items-center justify-center">4</span>
                        Chọn chế độ phân bổ đề thi
                    </label>
                    <span class="text-xs text-gray-500">Chọn 1 tùy chọn bên dưới, sau đó bấm nút Giao đề</span>
                </div>

                <!-- Hidden input submitting selected assignType -->
                <input type="hidden" name="assign_type" :value="assignType">

                <!-- Interactive Radio Cards for assignType -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5">
                    
                    <!-- 1. Giao cho các sinh viên được chọn -->
                    <div @click="assignType = 'all'" 
                        :class="assignType === 'all' ? 'border-blue-600 bg-blue-50/70 ring-2 ring-blue-500 shadow-sm' : 'border-gray-200 bg-white hover:border-blue-300 hover:bg-gray-50/60'"
                        class="p-4 rounded-2xl border-2 transition-all cursor-pointer flex flex-col justify-between relative group select-none">
                        <div class="flex items-start justify-between gap-2 mb-2">
                            <div class="flex items-center gap-2">
                                <span class="w-7 h-7 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-xs shrink-0">ALL</span>
                                <h4 class="text-sm font-bold text-gray-900 group-hover:text-blue-700">Giao cho sinh viên được chọn</h4>
                            </div>
                            <span class="w-4 h-4 rounded-full border flex items-center justify-center shrink-0"
                                :class="assignType === 'all' ? 'border-blue-600 bg-blue-600 text-white' : 'border-gray-300 bg-white'">
                                <svg x-show="assignType === 'all'" class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 leading-relaxed">
                            Mỗi sinh viên được chọn (hoặc chọn từng người ở Bước 3) sẽ nhận toàn bộ các đề thi đã đánh dấu.
                        </p>
                    </div>

                    <!-- 2. SOLE 1-2 -->
                    <div @click="assignType = 'sole_1_2'" 
                        :class="assignType === 'sole_1_2' ? 'border-amber-500 bg-amber-50/70 ring-2 ring-amber-400 shadow-sm' : 'border-gray-200 bg-white hover:border-amber-300 hover:bg-gray-50/60'"
                        class="p-4 rounded-2xl border-2 transition-all cursor-pointer flex flex-col justify-between relative group select-none">
                        <div class="flex items-start justify-between gap-2 mb-2">
                            <div class="flex items-center gap-2">
                                <span class="w-7 h-7 rounded-lg bg-amber-100 text-amber-800 flex items-center justify-center font-bold text-xs shrink-0">1-2</span>
                                <h4 class="text-sm font-bold text-gray-900 group-hover:text-amber-800">Phân phối SOLE 1-2 (2 đề)</h4>
                            </div>
                            <span class="w-4 h-4 rounded-full border flex items-center justify-center shrink-0"
                                :class="assignType === 'sole_1_2' ? 'border-amber-500 bg-amber-500 text-white' : 'border-gray-300 bg-white'">
                                <svg x-show="assignType === 'sole_1_2'" class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 leading-relaxed">
                            Chia xen kẽ chẵn/lẻ giữa 2 đề thi cho danh sách sinh viên được chọn (SV1 Đề 1, SV2 Đề 2...).
                        </p>
                    </div>

                    <!-- 3. SOLE 1-2-3 -->
                    <div @click="assignType = 'sole_1_2_3'" 
                        :class="assignType === 'sole_1_2_3' ? 'border-indigo-500 bg-indigo-50/70 ring-2 ring-indigo-400 shadow-sm' : 'border-gray-200 bg-white hover:border-indigo-300 hover:bg-gray-50/60'"
                        class="p-4 rounded-2xl border-2 transition-all cursor-pointer flex flex-col justify-between relative group select-none">
                        <div class="flex items-start justify-between gap-2 mb-2">
                            <div class="flex items-center gap-2">
                                <span class="w-7 h-7 rounded-lg bg-indigo-100 text-indigo-800 flex items-center justify-center font-bold text-xs shrink-0">1-3</span>
                                <h4 class="text-sm font-bold text-gray-900 group-hover:text-indigo-800">Phân phối SOLE 1-2-3 (3 đề)</h4>
                            </div>
                            <span class="w-4 h-4 rounded-full border flex items-center justify-center shrink-0"
                                :class="assignType === 'sole_1_2_3' ? 'border-indigo-500 bg-indigo-500 text-white' : 'border-gray-300 bg-white'">
                                <svg x-show="assignType === 'sole_1_2_3'" class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 leading-relaxed">
                            Chia xen kẽ xoay vòng giữa 3 đề thi cho sinh viên (SV1 Đề 1, SV2 Đề 2, SV3 Đề 3...).
                        </p>
                    </div>

                    <!-- 4. SOLE 1-2-3-4 -->
                    <div @click="assignType = 'sole_1_2_3_4'" 
                        :class="assignType === 'sole_1_2_3_4' ? 'border-emerald-500 bg-emerald-50/70 ring-2 ring-emerald-400 shadow-sm' : 'border-gray-200 bg-white hover:border-emerald-300 hover:bg-gray-50/60'"
                        class="p-4 rounded-2xl border-2 transition-all cursor-pointer flex flex-col justify-between relative group select-none">
                        <div class="flex items-start justify-between gap-2 mb-2">
                            <div class="flex items-center gap-2">
                                <span class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-xs shrink-0">1-4</span>
                                <h4 class="text-sm font-bold text-gray-900 group-hover:text-emerald-800">Phân phối SOLE 1-2-3-4 (4 đề)</h4>
                            </div>
                            <span class="w-4 h-4 rounded-full border flex items-center justify-center shrink-0"
                                :class="assignType === 'sole_1_2_3_4' ? 'border-emerald-500 bg-emerald-500 text-white' : 'border-gray-300 bg-white'">
                                <svg x-show="assignType === 'sole_1_2_3_4'" class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 leading-relaxed">
                            Chia xen kẽ xoay vòng giữa 4 đề thi cho sinh viên (SV1-SV4 nhận lần lượt Đề 1 đến Đề 4).
                        </p>
                    </div>

                    <!-- 5. SOLE tự động theo số đề -->
                    <div @click="assignType = 'sole'" 
                        :class="assignType === 'sole' ? 'border-teal-500 bg-teal-50/70 ring-2 ring-teal-400 shadow-sm' : 'border-gray-200 bg-white hover:border-teal-300 hover:bg-gray-50/60'"
                        class="p-4 rounded-2xl border-2 transition-all cursor-pointer flex flex-col justify-between relative group select-none">
                        <div class="flex items-start justify-between gap-2 mb-2">
                            <div class="flex items-center gap-2">
                                <span class="w-7 h-7 rounded-lg bg-teal-100 text-teal-800 flex items-center justify-center font-bold text-xs shrink-0">AUTO</span>
                                <h4 class="text-sm font-bold text-gray-900 group-hover:text-teal-800">Phân phối SOLE tự động</h4>
                            </div>
                            <span class="w-4 h-4 rounded-full border flex items-center justify-center shrink-0"
                                :class="assignType === 'sole' ? 'border-teal-500 bg-teal-500 text-white' : 'border-gray-300 bg-white'">
                                <svg x-show="assignType === 'sole'" class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 leading-relaxed">
                            Tự động chia đều luân phiên theo toàn bộ số đề thi bạn đã đánh dấu ở Bước 2.
                        </p>
                    </div>

                </div>

                <!-- CHỈ CÓ 1 NÚT GIAO ĐỀ DUY NHẤT -->
                <div class="pt-2">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 bg-gradient-to-r from-slate-50 via-blue-50/30 to-indigo-50/30 p-5 rounded-2xl border border-blue-200/80 shadow-2xs">
                        <div>
                            <div class="text-sm font-bold text-gray-900 flex items-center gap-2 flex-wrap">
                                <span>Tổng kết trước khi giao:</span>
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800 font-mono" x-text="selectedStudents.length + ' sinh viên'"></span>
                                <span class="text-gray-300">•</span>
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800 font-mono" x-text="selectedExams.length + ' đề thi'"></span>
                            </div>
                            <p class="text-xs text-gray-600 mt-1" x-text="getAssignSummary()"></p>
                        </div>

                        <button type="submit" id="btnSubmitAssign"
                            :disabled="selectedExams.length === 0 || selectedStudents.length === 0"
                            :class="(selectedExams.length === 0 || selectedStudents.length === 0) ? 'opacity-50 cursor-not-allowed bg-gray-400' : 'bg-blue-600 hover:bg-blue-700 active:bg-blue-800 shadow-md shadow-blue-600/25 active:scale-98 cursor-pointer'"
                            class="w-full sm:w-auto px-8 py-3.5 text-white font-extrabold text-sm rounded-xl transition-all flex items-center justify-center gap-2">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>Xác nhận giao đề thi</span>
                        </button>
                    </div>
                </div>

            </div>
        </form>
        @elseif(count($selectedClassIds) > 0)
            <div class="text-center py-12 text-gray-500 bg-gray-50 rounded-2xl border border-gray-100">
                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                <p class="font-bold text-gray-700">Các lớp được chọn chưa có sinh viên nào.</p>
                <p class="text-xs text-gray-400 mt-1">Vui lòng vào Quản lý Lớp học để thêm sinh viên trước khi giao đề thi.</p>
            </div>
        @else
            <div class="text-center py-12 text-gray-500 bg-gray-50 rounded-2xl border border-gray-100">
                <svg class="w-12 h-12 text-blue-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                <p class="font-bold text-gray-700">Vui lòng chọn ít nhất một lớp học ở Bước 1.</p>
                <p class="text-xs text-gray-400 mt-1">Hệ thống sẽ tải danh sách sinh viên tương ứng để bạn lựa chọn và áp dụng phân bổ đề thi.</p>
            </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('assignmentForm', () => ({
            selectedClasses: {!! json_encode(array_values($selectedClassIds ?? [])) !!},
            allClassIds: {!! json_encode($classes->pluck('id')) !!},
            selectedExams: {!! json_encode($preselectedExamId ? [$preselectedExamId] : []) !!},
            allStudentList: {!! json_encode(collect($students ?? [])->map(fn($s, $idx) => [
                'id' => $s->id,
                'name' => $s->name,
                'code' => $s->code ?? '',
                'index' => $idx
            ])) !!},
            selectedStudents: {!! json_encode(collect($students ?? [])->pluck('id')) !!},
            assignType: 'all',
            searchQuery: '',

            toggleClass(id) {
                id = parseInt(id);
                const idx = this.selectedClasses.indexOf(id);
                if (idx > -1) {
                    this.selectedClasses.splice(idx, 1);
                } else {
                    this.selectedClasses.push(id);
                }
            },

            selectAllClasses() {
                this.selectedClasses = [...this.allClassIds];
            },

            clearAllClasses() {
                this.selectedClasses = [];
            },

            toggleExam(id) {
                id = parseInt(id);
                const idx = this.selectedExams.indexOf(id);
                if (idx > -1) {
                    this.selectedExams.splice(idx, 1);
                } else {
                    this.selectedExams.push(id);
                }
            },

            toggleStudent(id) {
                id = parseInt(id);
                const idx = this.selectedStudents.indexOf(id);
                if (idx > -1) {
                    this.selectedStudents.splice(idx, 1);
                } else {
                    this.selectedStudents.push(id);
                }
            },

            selectAll() {
                this.selectedStudents = this.allStudentList.map(s => s.id);
            },

            clearAll() {
                this.selectedStudents = [];
            },

            isAllSelected() {
                return this.selectedStudents.length === this.allStudentList.length && this.allStudentList.length > 0;
            },

            toggleAllCheckbox() {
                if (this.isAllSelected()) {
                    this.clearAll();
                } else {
                    this.selectAll();
                }
            },

            // Sole Pattern Selector (1-2, 1-2-3, 1-2-3-4)
            selectSolePattern(modulo, remainder) {
                this.selectedStudents = this.allStudentList
                    .filter(s => (s.index % modulo) === remainder)
                    .map(s => s.id);
            },

            // Filter students by search input for choosing individual students
            matchesSearch(studentId) {
                if (!this.searchQuery || !this.searchQuery.trim()) return true;
                const query = this.searchQuery.toLowerCase().trim();
                const st = this.allStudentList.find(s => s.id === studentId);
                if (!st) return false;
                return (st.name && st.name.toLowerCase().includes(query)) ||
                       (st.code && st.code.toLowerCase().includes(query));
            },

            // Summary text under the single submit button
            getAssignSummary() {
                if (this.selectedStudents.length === 0) return 'Vui lòng tích chọn ít nhất 1 sinh viên để giao đề.';
                if (this.selectedExams.length === 0) return 'Vui lòng đánh dấu ít nhất 1 đề thi để giao.';

                if (this.assignType === 'all') {
                    return `Sẽ giao ${this.selectedExams.length} đề thi cho ${this.selectedStudents.length} sinh viên đã chọn.`;
                } else if (this.assignType === 'sole_1_2') {
                    return `Sẽ phân bổ xen kẽ 2 đề thi (chẵn/lẻ) luân phiên cho ${this.selectedStudents.length} sinh viên.`;
                } else if (this.assignType === 'sole_1_2_3') {
                    return `Sẽ phân bổ xoay vòng 3 đề thi luân phiên cho ${this.selectedStudents.length} sinh viên.`;
                } else if (this.assignType === 'sole_1_2_3_4') {
                    return `Sẽ phân bổ xoay vòng 4 đề thi luân phiên cho ${this.selectedStudents.length} sinh viên.`;
                } else if (this.assignType === 'sole') {
                    return `Sẽ phân bổ chia đều ${this.selectedExams.length} đề thi luân phiên cho ${this.selectedStudents.length} sinh viên.`;
                }
                return '';
            }
        }));
    });
</script>
@endsection
