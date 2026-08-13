@extends('layouts.teacher')

@section('title', 'Giao đề thi cho lớp')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <div class="flex items-center gap-2 mb-2">
        <a href="{{ route('teacher.exams.index') }}" class="text-sm font-medium text-gray-500 hover:text-blue-600 transition-colors">Quản lý kỳ thi</a>
        <span class="text-gray-400">/</span>
        <span class="text-sm font-medium text-gray-900">Giao đề thi</span>
    </div>
    
    <h2 class="text-2xl font-bold text-gray-900">Giao đề thi cho sinh viên</h2>

    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-4 rounded-r-lg">
            <p class="text-sm text-green-700 font-medium">{{ session('success') }}</p>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-4 rounded-r-lg">
            <p class="text-sm text-red-700 font-medium">{{ session('error') }}</p>
        </div>
    @endif
    @if($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-4 rounded-r-lg">
            <ul class="list-disc pl-5 text-sm text-red-700 font-medium">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm" x-data="assignmentForm()">
        
        <!-- Select Class Form (GET to reload students) -->
        <form method="GET" action="{{ route('teacher.assignments.create') }}" class="mb-8 border-b border-gray-100 pb-8" id="classSelectForm">
            <div class="max-w-md">
                <label class="block text-sm font-semibold text-gray-700 mb-2">1. Chọn lớp học</label>
                <select name="class_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm" onchange="document.getElementById('classSelectForm').submit()">
                    <option value="">-- Chọn một lớp --</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ $selectedClassId == $c->id ? 'selected' : '' }}>
                            {{ $c->code }} - {{ $c->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>

        @if($selectedClassId && count($students) > 0)
        <!-- Assignment Form -->
        <form method="POST" action="{{ route('teacher.assignments.store') }}" id="assignForm">
            @csrf
            <input type="hidden" name="class_id" value="{{ $selectedClassId }}">

            <!-- Select Exams -->
            <div class="mb-8 border-b border-gray-100 pb-8">
                <label class="block text-sm font-semibold text-gray-700 mb-2">2. Chọn các đề thi muốn giao (Có thể chọn nhiều)</label>
                <p class="text-xs text-gray-500 mb-3">Nhấn giữ Ctrl (hoặc Cmd trên Mac) để chọn nhiều đề.</p>
                
                <select name="exams[]" multiple required class="w-full h-48 rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-sm">
                    @foreach($exams as $exam)
                        <option value="{{ $exam->id }}" class="py-2 px-3 border-b border-gray-50 hover:bg-blue-50">
                            [{{ $exam->code }}] {{ $exam->title }} ({{ $exam->total_questions }} câu - {{ $exam->duration_minutes }} phút) - {{ $exam->status === 'published' ? 'Mở' : 'Đóng' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Select Students -->
            <div class="mb-8 border-b border-gray-100 pb-8">
                <div class="flex justify-between items-center mb-4">
                    <label class="block text-sm font-semibold text-gray-700">3. Chọn sinh viên để giao</label>
                    <label class="inline-flex items-center text-sm">
                        <input type="checkbox" x-model="selectAll" @change="toggleAll" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 w-4 h-4">
                        <span class="ml-2 font-medium text-gray-700">Chọn tất cả</span>
                    </label>
                </div>
                
                <div class="max-h-80 overflow-y-auto border border-gray-200 rounded-lg">
                    <table class="w-full text-left text-sm text-gray-600">
                        <thead class="bg-gray-50 border-b border-gray-100 sticky top-0 z-10">
                            <tr>
                                <th class="px-4 py-3 w-12 text-center">Chọn</th>
                                <th class="px-4 py-3 w-16 text-center">STT</th>
                                <th class="px-4 py-3">Mã SV</th>
                                <th class="px-4 py-3">Họ và tên</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @foreach($students as $index => $student)
                                <tr class="hover:bg-gray-50/50 cursor-pointer" @click="toggleStudent('{{ $student->id }}')">
                                    <td class="px-4 py-3 text-center">
                                        <input type="checkbox" name="students[]" value="{{ $student->id }}" x-model="selectedStudents" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 w-4 h-4 pointer-events-none">
                                    </td>
                                    <td class="px-4 py-3 text-center text-gray-400">{{ $index + 1 }}</td>
                                    <td class="px-4 py-3 font-medium">{{ $student->code ?? 'N/A' }}</td>
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $student->name }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <p class="text-sm text-gray-500 mt-2">Đã chọn: <span class="font-bold text-blue-600" x-text="selectedStudents.length"></span> sinh viên.</p>
            </div>

            <!-- Actions -->
            <div class="flex gap-4">
                <button type="submit" name="assign_type" value="all" class="flex-1 bg-white border-2 border-blue-600 text-blue-600 hover:bg-blue-50 font-bold py-3 px-4 rounded-xl shadow-sm transition-colors text-center">
                    Giao tất cả đề cho mỗi sinh viên
                </button>
                <button type="submit" name="assign_type" value="sole" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-xl shadow-sm transition-colors text-center" title="Phân phát các đề thi luân phiên cho danh sách sinh viên">
                    Giao SOLE (chia chéo các đề thi)
                </button>
            </div>
            <p class="text-xs text-gray-500 mt-3 text-center">Giao SOLE: Nếu bạn chọn 3 đề, sinh viên 1 nhận Đề 1, sv 2 nhận Đề 2, sv 3 nhận Đề 3, sv 4 nhận Đề 1...</p>
        </form>
        @elseif($selectedClassId)
            <div class="text-center py-8 text-gray-500 bg-gray-50 rounded-lg">
                Lớp này chưa có sinh viên nào.
            </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('assignmentForm', () => ({
            selectAll: false,
            allStudentIds: {!! json_encode(collect($students ?? [])->pluck('id')) !!},
            selectedStudents: [],
            
            toggleAll() {
                if (this.selectAll) {
                    this.selectedStudents = [...this.allStudentIds];
                } else {
                    this.selectedStudents = [];
                }
            },
            
            toggleStudent(id) {
                id = parseInt(id);
                const index = this.selectedStudents.indexOf(id);
                if (index > -1) {
                    this.selectedStudents.splice(index, 1);
                } else {
                    this.selectedStudents.push(id);
                }
                
                this.selectAll = this.selectedStudents.length === this.allStudentIds.length && this.allStudentIds.length > 0;
            }
        }));
    });
</script>
@endsection
