@extends('layouts.teacher')

@section('title', 'Ngân hàng Câu hỏi')

@section('content')
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <!-- Header & Filters -->
        <div class="px-6 py-5 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <h3 class="font-semibold text-gray-800">Danh sách câu hỏi</h3>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                    {{ $questions->total() }} câu
                </span>
            </div>
            
            <div class="flex items-center gap-3">
                <form action="{{ route('teacher.questions.index') }}" method="GET" class="flex gap-2">
                    <select name="subject_id" class="text-sm rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" onchange="this.form.submit()">
                        <option value="">-- Tất cả môn học --</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                                {{ $subject->name }}
                            </option>
                        @endforeach
                    </select>
                </form>
                
                <a href="{{ route('teacher.questions.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tạo câu hỏi
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="m-6 p-4 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-100">
                {{ session('success') }}
            </div>
        @endif

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <th class="px-6 py-4 border-b border-gray-100 w-16">ID</th>
                        <th class="px-6 py-4 border-b border-gray-100">Nội dung câu hỏi</th>
                        <th class="px-6 py-4 border-b border-gray-100">Loại</th>
                        <th class="px-6 py-4 border-b border-gray-100">Độ khó</th>
                        <th class="px-6 py-4 border-b border-gray-100 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($questions as $question)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-sm text-gray-500">#{{ $question->id }}</td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-medium text-gray-900 line-clamp-2">{{ $question->content }}</p>
                                <p class="text-xs text-gray-500 mt-1">Môn: {{ $question->subject->name ?? 'N/A' }}</p>
                            </td>
                            <td class="px-6 py-4">
                                @if($question->type === 'multiple_choice')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700">Trắc nghiệm</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-50 text-purple-700">Tự luận</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($question->difficulty === 'easy')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700">Dễ</span>
                                @elseif($question->difficulty === 'medium')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-700">TB</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-rose-50 text-rose-700">Khó</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button class="p-1.5 text-gray-400 hover:text-blue-600 rounded-lg hover:bg-blue-50 transition-colors" title="Sửa">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    </button>
                                    <button class="p-1.5 text-gray-400 hover:text-red-600 rounded-lg hover:bg-red-50 transition-colors" title="Xóa">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                Chưa có câu hỏi nào. <a href="{{ route('teacher.questions.create') }}" class="text-blue-600 hover:underline">Tạo mới ngay</a>.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $questions->links() }}
        </div>
    </div>
@endsection
