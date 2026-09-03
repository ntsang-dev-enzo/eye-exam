@extends('layouts.teacher')

@section('title', 'Tạo Đề Thi Mới')

@section('content')
    <div class="max-w-5xl mx-auto pb-12">
        <form id="examForm" action="{{ route('teacher.exams.store') }}" method="POST" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 space-y-8 relative">
            @csrf

            <!-- 1. Thông tin chung -->
            <div>
                <div class="flex items-center justify-between border-b border-gray-100 pb-3 mb-5">
                    <h3 class="text-lg font-semibold text-gray-900">1. Thông tin chung</h3>
                    <span class="text-xs text-gray-500">Thiết lập cấu hình thời gian & thông tin đề thi</span>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Môn học <span class="text-red-500">*</span></label>
                        <select name="subject_id" id="subject_id" required class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 px-4 py-2.5 bg-gray-50 text-sm">
                            <option value="">-- Chọn môn học --</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Danh mục đề thi (Tùy chọn)</label>
                        <select name="category_id" id="exam_category_id" class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 px-4 py-2.5 bg-gray-50 text-sm">
                            <option value="">-- Không phân loại / Chọn danh mục --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" data-subject="{{ $cat->subject_id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tên đề thi <span class="text-red-500">*</span></label>
                        <input type="text" name="title" value="{{ old('title') }}" required class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 px-4 py-2.5 bg-gray-50 text-sm" placeholder="VD: Giữa kỳ Toán Rời Rạc - Học kỳ 1">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Mô tả thêm (Tùy chọn)</label>
                        <textarea name="description" rows="2" class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 px-4 py-2.5 bg-gray-50 text-sm" placeholder="Hướng dẫn làm bài, lưu ý chống gian lận...">{{ old('description') }}</textarea>
                    </div>

                    <!-- Thời gian mở & đóng đề thi -->
                    <div class="p-4 bg-blue-50/50 rounded-2xl border border-blue-100/80 md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-800 mb-1.5 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                Thời gian mở đề (Bắt đầu)
                            </label>
                            <input type="datetime-local" id="start_at" name="start_at" value="{{ old('start_at') }}" class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 px-4 py-2.5 bg-white text-sm">
                            <p class="text-[11px] text-gray-500 mt-1">Thời điểm sớm nhất sinh viên có thể vào thi</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-800 mb-1.5 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                                Thời gian đóng đề (Kết thúc)
                            </label>
                            <input type="datetime-local" id="end_at" name="end_at" value="{{ old('end_at') }}" class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 px-4 py-2.5 bg-white text-sm">
                            <p class="text-[11px] text-gray-500 mt-1">Thời điểm đề thi tự động khóa</p>
                        </div>
                    </div>

                    <!-- Duration -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-sm font-medium text-gray-700">Thời gian làm bài (phút) <span class="text-red-500">*</span></label>
                            <button type="button" id="btnAutoCalcDuration" class="text-xs text-blue-600 hover:text-blue-800 font-semibold hover:underline flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                Tính theo giờ mở/đóng
                            </button>
                        </div>
                        <input type="number" id="duration_minutes" name="duration_minutes" value="{{ old('duration_minutes', 45) }}" min="1" required class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 px-4 py-2.5 bg-gray-50 text-sm font-semibold text-gray-900">
                        <div id="durationAutoNotice" class="text-xs text-emerald-700 font-medium hidden mt-1.5 p-2 bg-emerald-50 rounded-lg border border-emerald-100 flex items-center gap-1.5">
                            <!-- Populated by JS -->
                        </div>
                        <div class="flex items-center gap-1.5 mt-2 flex-wrap">
                            <span class="text-[11px] text-gray-500">Mẫu nhanh:</span>
                            <button type="button" onclick="setDuration(15)" class="px-2 py-0.5 rounded text-xs bg-gray-100 hover:bg-gray-200 text-gray-700">15p</button>
                            <button type="button" onclick="setDuration(45)" class="px-2 py-0.5 rounded text-xs bg-gray-100 hover:bg-gray-200 text-gray-700">45p</button>
                            <button type="button" onclick="setDuration(60)" class="px-2 py-0.5 rounded text-xs bg-gray-100 hover:bg-gray-200 text-gray-700">60p</button>
                            <button type="button" onclick="setDuration(90)" class="px-2 py-0.5 rounded text-xs bg-gray-100 hover:bg-gray-200 text-gray-700">90p</button>
                            <button type="button" onclick="setDuration(120)" class="px-2 py-0.5 rounded text-xs bg-gray-100 hover:bg-gray-200 text-gray-700">120p</button>
                        </div>
                    </div>

                    <!-- Max Attempts & Unlimited -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-800 mb-2">Số lần thi tối đa</label>
                        <div class="space-y-2">
                            <input type="number" id="max_attempts_input" name="max_attempts" value="{{ old('max_attempts', 1) }}" min="1" class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 px-4 py-2.5 bg-gray-50 text-sm">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" id="unlimited_attempts_cb" name="unlimited_attempts" value="1" {{ old('unlimited_attempts') ? 'checked' : '' }} onchange="toggleUnlimitedAttempts(this.checked)" class="text-blue-600 focus:ring-blue-500 rounded text-sm w-4 h-4">
                                <span class="text-xs text-gray-700 font-bold">Không giới hạn số lần làm bài (Vô hạn lần)</span>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Anti-cheat Configuration Card -->
                    <div class="md:col-span-2 bg-gradient-to-br from-slate-50 to-indigo-50/40 rounded-2xl p-5 border border-indigo-100/80 space-y-4">
                        <div class="flex items-center justify-between border-b border-indigo-100/60 pb-3 flex-wrap gap-2">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-bold text-xs shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-gray-900">Tùy chọn Chống Gian Lận (Anti-Cheat Rules)</h4>
                                    <p class="text-xs text-gray-500">Tùy chỉnh các lớp bảo vệ và quy tắc giám sát cho kỳ thi này</p>
                                </div>
                            </div>
                            <label class="flex items-center gap-2 cursor-pointer bg-white px-3 py-1.5 rounded-xl border border-indigo-200 shadow-sm">
                                <input type="checkbox" id="enable_anti_cheat" name="enable_anti_cheat" value="1" checked onchange="toggleAllAntiCheat(this.checked)" class="text-indigo-600 focus:ring-indigo-500 rounded text-sm w-4 h-4">
                                <span class="text-xs font-bold text-indigo-900">Bật Giám sát Chống Gian lận</span>
                            </label>
                        </div>

                        <div id="antiCheatSubOptions" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5 pt-1">
                            <label class="flex items-start p-3 bg-white rounded-xl border border-gray-200 hover:border-indigo-300 transition-colors cursor-pointer gap-2.5">
                                <input type="checkbox" name="require_fullscreen" value="1" checked class="anti-cheat-sub mt-0.5 text-indigo-600 focus:ring-indigo-500 rounded text-sm w-4 h-4">
                                <div>
                                    <span class="block text-xs font-bold text-gray-800">Bắt buộc Toàn màn hình</span>
                                    <span class="block text-[11px] text-gray-500 mt-0.5 leading-tight">Yêu cầu Fullscreen và khóa màn hình khi thoát</span>
                                </div>
                            </label>

                            <label class="flex items-start p-3 bg-white rounded-xl border border-gray-200 hover:border-indigo-300 transition-colors cursor-pointer gap-2.5">
                                <input type="checkbox" name="prevent_tab_switch" value="1" checked class="anti-cheat-sub mt-0.5 text-indigo-600 focus:ring-indigo-500 rounded text-sm w-4 h-4">
                                <div>
                                    <span class="block text-xs font-bold text-gray-800">Chặn chuyển Tab / Alt+Tab</span>
                                    <span class="block text-[11px] text-gray-500 mt-0.5 leading-tight">Phát hiện mất tiêu điểm và tính thời gian rời màn hình</span>
                                </div>
                            </label>

                            <label class="flex items-start p-3 bg-white rounded-xl border border-gray-200 hover:border-indigo-300 transition-colors cursor-pointer gap-2.5">
                                <input type="checkbox" name="prevent_copy_paste" value="1" checked class="anti-cheat-sub mt-0.5 text-indigo-600 focus:ring-indigo-500 rounded text-sm w-4 h-4">
                                <div>
                                    <span class="block text-xs font-bold text-gray-800">Chặn Copy / Paste / Cut</span>
                                    <span class="block text-[11px] text-gray-500 mt-0.5 leading-tight">Vô hiệu hóa sao chép đề và dán câu trả lời</span>
                                </div>
                            </label>

                            <label class="flex items-start p-3 bg-white rounded-xl border border-gray-200 hover:border-indigo-300 transition-colors cursor-pointer gap-2.5">
                                <input type="checkbox" name="prevent_right_click" value="1" checked class="anti-cheat-sub mt-0.5 text-indigo-600 focus:ring-indigo-500 rounded text-sm w-4 h-4">
                                <div>
                                    <span class="block text-xs font-bold text-gray-800">Chặn Chuột phải (Context Menu)</span>
                                    <span class="block text-[11px] text-gray-500 mt-0.5 leading-tight">Vô hiệu hóa menu ngữ cảnh chuột phải</span>
                                </div>
                            </label>

                            <label class="flex items-start p-3 bg-white rounded-xl border border-gray-200 hover:border-indigo-300 transition-colors cursor-pointer gap-2.5">
                                <input type="checkbox" name="prevent_screen_capture" value="1" checked class="anti-cheat-sub mt-0.5 text-indigo-600 focus:ring-indigo-500 rounded text-sm w-4 h-4">
                                <div>
                                    <span class="block text-xs font-bold text-gray-800">Chặn Chụp màn hình & DevTools</span>
                                    <span class="block text-[11px] text-gray-500 mt-0.5 leading-tight">Chặn phím PrtSc, Win+Shift+S, F12, Ctrl+P, Ctrl+U</span>
                                </div>
                            </label>

                            <label class="flex items-start p-3 bg-white rounded-xl border border-indigo-200 hover:border-indigo-400 transition-colors cursor-pointer gap-2.5">
                                <input type="checkbox" name="require_face_verification" value="1" checked class="anti-cheat-sub mt-0.5 text-indigo-600 focus:ring-indigo-500 rounded text-sm w-4 h-4">
                                <div>
                                    <span class="block text-xs font-bold text-indigo-900">Xác thực Khuôn mặt (Face ID ArcFace)</span>
                                    <span class="block text-[11px] text-gray-500 mt-0.5 leading-tight">Bắt buộc quét khuôn mặt &ge; 70% trước khi cấp đề thi</span>
                                </div>
                            </label>

                            <label class="flex items-start p-3 bg-white rounded-xl border border-indigo-200 hover:border-indigo-400 transition-colors cursor-pointer gap-2.5">
                                <input type="checkbox" name="enable_proctor_camera" id="toggle_proctor_camera" value="1" checked onchange="document.getElementById('proctor_interval_box').style.display = this.checked ? 'block' : 'none'" class="anti-cheat-sub mt-0.5 text-indigo-600 focus:ring-indigo-500 rounded text-sm w-4 h-4">
                                <div>
                                    <span class="block text-xs font-bold text-indigo-900">Giám sát Camera AI định kỳ (YOLO + ArcFace)</span>
                                    <span class="block text-[11px] text-gray-500 mt-0.5 leading-tight">Chụp ảnh định kỳ tự động phân tích vật thể & đối soát hồ sơ gốc</span>
                                </div>
                            </label>

                            <!-- Thiết lập thời gian chụp ảnh mỗi lần -->
                            <div id="proctor_interval_box" class="sm:col-span-2 p-3.5 bg-gradient-to-r from-indigo-50/90 to-blue-50/90 rounded-xl border border-indigo-200 shadow-xs transition-all">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                    <div>
                                        <label for="proctor_interval_seconds" class="block text-xs font-bold text-indigo-950 flex items-center gap-1.5">
                                            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            Thời gian chụp ảnh mỗi lần (Chu kỳ chụp)
                                        </label>
                                        <p class="text-[11px] text-indigo-700/80 mt-0.5">Khoảng thời gian hệ thống tự động chụp webcam thí sinh để YOLO phân tích & InsightFace đối soát khuôn mặt</p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <div class="relative w-36">
                                            <input type="number" id="proctor_interval_seconds" name="proctor_interval_seconds" min="15" max="1800" step="5" value="{{ old('proctor_interval_seconds', 120) }}" class="w-full px-3 py-1.5 text-xs font-bold text-indigo-900 bg-white border border-indigo-300 rounded-lg focus:ring-2 focus:ring-indigo-500 text-right pr-10 shadow-xs">
                                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-indigo-500 font-semibold pointer-events-none">giây</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1.5 mt-2.5 pt-2 border-t border-indigo-200/60 flex-wrap">
                                    <span class="text-[11px] font-bold text-indigo-700 mr-1">Chọn nhanh:</span>
                                    <button type="button" onclick="document.getElementById('proctor_interval_seconds').value=30" class="px-2.5 py-1 text-[11px] font-semibold bg-white hover:bg-indigo-600 hover:text-white text-indigo-700 rounded-md border border-indigo-200 shadow-2xs transition-all cursor-pointer">30 giây</button>
                                    <button type="button" onclick="document.getElementById('proctor_interval_seconds').value=60" class="px-2.5 py-1 text-[11px] font-semibold bg-white hover:bg-indigo-600 hover:text-white text-indigo-700 rounded-md border border-indigo-200 shadow-2xs transition-all cursor-pointer">1 phút (60s)</button>
                                    <button type="button" onclick="document.getElementById('proctor_interval_seconds').value=90" class="px-2.5 py-1 text-[11px] font-semibold bg-white hover:bg-indigo-600 hover:text-white text-indigo-700 rounded-md border border-indigo-200 shadow-2xs transition-all cursor-pointer">1.5 phút (90s)</button>
                                    <button type="button" onclick="document.getElementById('proctor_interval_seconds').value=120" class="px-2.5 py-1 text-[11px] font-semibold bg-white hover:bg-indigo-600 hover:text-white text-indigo-700 rounded-md border border-indigo-200 shadow-2xs transition-all cursor-pointer">2 phút (120s)</button>
                                    <button type="button" onclick="document.getElementById('proctor_interval_seconds').value=180" class="px-2.5 py-1 text-[11px] font-semibold bg-white hover:bg-indigo-600 hover:text-white text-indigo-700 rounded-md border border-indigo-200 shadow-2xs transition-all cursor-pointer">3 phút (180s)</button>
                                    <button type="button" onclick="document.getElementById('proctor_interval_seconds').value=300" class="px-2.5 py-1 text-[11px] font-semibold bg-white hover:bg-indigo-600 hover:text-white text-indigo-700 rounded-md border border-indigo-200 shadow-2xs transition-all cursor-pointer">5 phút (300s)</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="md:col-span-2 flex flex-wrap items-center gap-6 pt-2 border-t border-gray-100">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="shuffle_questions" value="1" class="text-blue-600 focus:ring-blue-500 rounded text-sm w-4 h-4">
                            <span class="text-sm text-gray-700 font-medium">Trộn thứ tự câu hỏi (Đảo đề)</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="shuffle_answers" value="1" class="text-blue-600 focus:ring-blue-500 rounded text-sm w-4 h-4">
                            <span class="text-sm text-gray-700 font-medium">Trộn thứ tự đáp án</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="allow_review" value="1" class="text-blue-600 focus:ring-blue-500 rounded text-sm w-4 h-4" checked>
                            <span class="text-sm text-gray-700 font-medium">Cho phép sinh viên xem lại bài thi sau khi nộp</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- 2. Thiết lập câu hỏi -->
            <div>
                <div class="flex items-center justify-between border-b border-gray-100 pb-3 mb-5">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">2. Thiết lập câu hỏi</h3>
                        <p class="text-xs text-gray-500">Chọn câu hỏi theo danh mục / chuyên đề hoặc tạo câu hỏi mới</p>
                    </div>
                    <div class="flex gap-2">
                        <button type="button" id="btnOpenBankModal" class="px-4 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-sm font-medium rounded-xl transition-colors flex items-center shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            Chọn từ ngân hàng theo danh mục
                        </button>
                        <button type="button" id="btnOpenManualModal" class="px-4 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-sm font-medium rounded-xl transition-colors flex items-center shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Tạo câu hỏi thủ công
                        </button>
                    </div>
                </div>

                <!-- Danh sách câu hỏi đã chọn -->
                <div id="selectedQuestionsContainer" class="hidden">
                    <div class="overflow-x-auto border border-gray-200 rounded-xl mb-4">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    <th class="px-4 py-3 border-b w-12 text-center">STT</th>
                                    <th class="px-4 py-3 border-b">Nội dung câu hỏi</th>
                                    <th class="px-4 py-3 border-b w-36">Danh mục</th>
                                    <th class="px-4 py-3 border-b w-24 text-center">Loại</th>
                                    <th class="px-4 py-3 border-b w-28">Điểm</th>
                                    <th class="px-4 py-3 border-b w-16 text-center">Xóa</th>
                                </tr>
                            </thead>
                            <tbody id="selectedQuestionsList" class="divide-y divide-gray-100 bg-white">
                                <!-- Các câu hỏi sẽ được append bằng JS -->
                            </tbody>
                        </table>
                    </div>
                    <div class="flex justify-between items-center text-sm font-medium text-gray-700 bg-gray-50 p-4 rounded-xl border border-gray-100">
                        <span class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                            Tổng số câu hỏi: <span id="totalQuestionsCount" class="font-bold text-blue-700">0</span>
                        </span>
                        <span class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
                            Tổng điểm: <span id="totalPointsCount" class="font-bold text-emerald-700">0</span>
                        </span>
                    </div>
                </div>

                <!-- Empty State -->
                <div class="bg-gray-50 border border-dashed border-gray-300 rounded-xl p-8 text-center" id="emptyQuestions">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Đề thi chưa có câu hỏi nào</h3>
                    <p class="mt-1 text-sm text-gray-500">Hãy bắt đầu bằng việc chọn từ ngân hàng đề (theo danh mục) hoặc tạo câu hỏi mới.</p>
                </div>
            </div>

            <!-- Submit -->
            <div class="pt-4 flex justify-end gap-3 border-t border-gray-100">
                <a href="{{ route('teacher.exams.index') }}" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors">
                    Hủy bỏ
                </a>
                <button type="button" id="btnSubmitForm" class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 transition-colors shadow-sm shadow-blue-200">
                    Lưu đề thi
                </button>
            </div>
        </form>
    </div>

    <!-- Modal: Chọn từ Ngân hàng (Xếp theo danh mục) -->
    <div id="bankModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" onclick="closeModal('bankModal')"></div>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="relative bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-4xl w-full flex flex-col max-h-[90vh]">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Ngân hàng câu hỏi theo Danh mục</h3>
                        <p class="text-xs text-gray-500">Chọn câu hỏi theo từng chuyên đề/danh mục để thêm vào đề thi</p>
                    </div>
                    <button type="button" onclick="closeModal('bankModal')" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <div class="p-6 overflow-y-auto flex-1 space-y-4">
                    <div id="bankLoading" class="text-center py-8 hidden">
                        <svg class="animate-spin h-8 w-8 text-blue-600 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <p class="mt-2 text-sm text-gray-500">Đang tải câu hỏi theo danh mục...</p>
                    </div>
                    
                    <div id="bankContent" class="space-y-4">
                        <p class="text-sm text-amber-600 bg-amber-50 p-3 rounded-lg border border-amber-100" id="bankWarning">Vui lòng chọn Môn học ở form trước khi chọn câu hỏi từ ngân hàng.</p>
                        
                        <!-- Filter in bank modal -->
                        <div id="bankFilters" class="flex flex-wrap items-center gap-3 hidden">
                            <div class="relative flex-1 min-w-[200px]">
                                <input type="text" id="bankSearchInput" placeholder="Tìm nội dung câu hỏi..." class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
                                <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </div>
                            </div>
                            <select id="bankCategoryFilter" class="text-sm rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                <option value="">-- Tất cả danh mục --</option>
                            </select>
                        </div>

                        <!-- Questions container grouped by category -->
                        <div id="bankQuestionsContainer" class="space-y-4 hidden">
                            <!-- Populated by JS grouped by category -->
                        </div>
                    </div>
                </div>
                
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex items-center justify-between">
                    <span class="text-sm text-gray-600">Đã chọn: <span id="bankSelectedCount" class="font-bold text-blue-600">0</span> câu</span>
                    <div class="flex gap-3">
                        <button type="button" onclick="closeModal('bankModal')" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">Đóng</button>
                        <button type="button" id="btnAddSelectedQuestions" class="px-4 py-2 bg-blue-600 rounded-lg text-sm font-medium text-white hover:bg-blue-700 disabled:opacity-50 shadow-sm shadow-blue-200">Thêm vào Đề thi</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Tạo thủ công -->
    <div id="manualModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" onclick="closeModal('manualModal')"></div>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="relative bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-3xl w-full flex flex-col max-h-[90vh]">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                    <h3 class="text-lg font-bold text-gray-900">Tạo câu hỏi thủ công</h3>
                    <button type="button" onclick="closeModal('manualModal')" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <div class="p-6 overflow-y-auto flex-1">
                    <form id="manualQuestionForm" class="space-y-4">
                        <!-- Category, Type & Difficulty -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Danh mục câu hỏi</label>
                                <select name="category_id" id="manualCategorySelect" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                                    <option value="">-- Chưa phân loại --</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Loại câu hỏi</label>
                                <select name="type" id="manualType" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                                    <option value="multiple_choice">Trắc nghiệm</option>
                                    <option value="essay">Tự luận</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Độ khó</label>
                                <select name="difficulty" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                                    <option value="easy">Dễ</option>
                                    <option value="medium" selected>Trung bình</option>
                                    <option value="hard">Khó</option>
                                </select>
                            </div>
                        </div>

                        <!-- Content -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nội dung câu hỏi <span class="text-red-500">*</span></label>
                            <textarea name="content" required rows="3" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm" placeholder="Nhập nội dung câu hỏi..."></textarea>
                        </div>

                        <!-- Answers (MC only) -->
                        <div id="manualAnswersSection" class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <label class="block text-sm font-medium text-gray-700 mb-3">Đáp án (Chọn radio cho đáp án đúng)</label>
                            <div id="manualAnswersList" class="space-y-3">
                                @for($i = 0; $i < 4; $i++)
                                    <div class="flex items-center gap-3">
                                        <input type="radio" name="correct_answer" value="{{ $i }}" {{ $i == 0 ? 'checked' : '' }} class="w-4 h-4 text-emerald-600 focus:ring-emerald-500">
                                        <span class="text-sm font-bold w-6">{{ chr(65 + $i) }}</span>
                                        <input type="text" name="answers[]" class="flex-1 rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm py-1.5" placeholder="Nhập đáp án {{ chr(65 + $i) }}...">
                                    </div>
                                @endfor
                            </div>
                        </div>
                    </form>
                </div>
                
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex justify-end gap-3">
                    <button type="button" onclick="closeModal('manualModal')" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">Hủy</button>
                    <button type="button" id="btnSaveManualQuestion" class="px-4 py-2 bg-emerald-600 rounded-lg text-sm font-medium text-white hover:bg-emerald-700 relative shadow-sm shadow-emerald-200">
                        <span id="btnSaveManualText">Lưu & Thêm vào đề</span>
                        <svg id="btnSaveManualSpinner" class="animate-spin h-5 w-5 absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript cho xử lý Đề thi, Tự động tính thời gian, & Xếp câu hỏi theo Danh mục -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        // State
        let selectedQuestions = []; // Array of {id, content, type, point, category_name}
        let bankQuestions = [];
        
        // Elements
        const subjectSelect = document.getElementById('subject_id');
        const startInput = document.getElementById('start_at');
        const endInput = document.getElementById('end_at');
        const durationInput = document.getElementById('duration_minutes');
        const durationNotice = document.getElementById('durationAutoNotice');
        const emptyState = document.getElementById('emptyQuestions');
        const container = document.getElementById('selectedQuestionsContainer');
        const tbody = document.getElementById('selectedQuestionsList');
        const totalQ = document.getElementById('totalQuestionsCount');
        const totalP = document.getElementById('totalPointsCount');
        const mainForm = document.getElementById('examForm');

        // 1. TỰ ĐỘNG TÍNH SỐ PHÚT LÀM BÀI KHI CHỌN NGÀY GIỜ MỞ/ĐÓNG
        function calculateDurationFromDates() {
            if (startInput.value && endInput.value) {
                const start = new Date(startInput.value);
                const end = new Date(endInput.value);
                const diffMs = end - start;
                
                if (diffMs > 0) {
                    const diffMins = Math.round(diffMs / (1000 * 60));
                    durationInput.value = diffMins;
                    
                    const hours = Math.floor(diffMins / 60);
                    const mins = diffMins % 60;
                    let timeDesc = `${diffMins} phút`;
                    if (hours > 0) {
                        timeDesc += ` (${hours} giờ ${mins > 0 ? mins + ' phút' : ''})`;
                    }
                    
                    durationNotice.innerHTML = `
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>Đã tự động tính thời gian làm bài: <strong>${timeDesc}</strong></span>
                    `;
                    durationNotice.className = "text-xs text-emerald-700 font-medium mt-1.5 p-2 bg-emerald-50 rounded-lg border border-emerald-100 flex items-center gap-1.5";
                } else {
                    durationNotice.innerHTML = `
                        <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        <span>Thời gian đóng đề phải sau thời gian mở đề!</span>
                    `;
                    durationNotice.className = "text-xs text-rose-700 font-medium mt-1.5 p-2 bg-rose-50 rounded-lg border border-rose-100 flex items-center gap-1.5";
                }
            } else {
                durationNotice.classList.add('hidden');
            }
        }

        startInput.addEventListener('change', calculateDurationFromDates);
        endInput.addEventListener('change', calculateDurationFromDates);
        document.getElementById('btnAutoCalcDuration').addEventListener('click', calculateDurationFromDates);

        function setDuration(mins) {
            durationInput.value = mins;
            durationNotice.classList.add('hidden');
        }

        // Filter category by subject
        subjectSelect.addEventListener('change', function() {
            const subjId = this.value;
            const catSelect = document.getElementById('exam_category_id');
            Array.from(catSelect.options).forEach(opt => {
                if (!opt.value) return;
                const optSubj = opt.getAttribute('data-subject');
                if (!optSubj || optSubj === subjId) {
                    opt.style.display = 'block';
                } else {
                    opt.style.display = 'none';
                }
            });
        });

        // 2. RENDER SELECTED QUESTIONS
        function renderSelectedQuestions() {
            if (selectedQuestions.length === 0) {
                emptyState.classList.remove('hidden');
                container.classList.add('hidden');
                totalQ.textContent = 0;
                totalP.textContent = 0;
                return;
            }

            emptyState.classList.add('hidden');
            container.classList.remove('hidden');
            
            tbody.innerHTML = '';
            let tPoints = 0;

            selectedQuestions.forEach((q, index) => {
                tPoints += parseFloat(q.point || 1);
                
                const typeBadge = q.type === 'multiple_choice' 
                    ? '<span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-50 text-indigo-700">Trắc nghiệm</span>'
                    : '<span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-50 text-purple-700">Tự luận</span>';

                const catBadge = q.category_name 
                    ? `<span class="px-2 py-0.5 rounded text-[11px] font-medium bg-purple-50 text-purple-700 border border-purple-100 truncate max-w-[130px] inline-block" title="${q.category_name}">${q.category_name}</span>`
                    : '<span class="text-xs text-gray-400 italic">Chưa phân loại</span>';

                const tr = document.createElement('tr');
                tr.className = 'border-b border-gray-50 hover:bg-gray-50/50 transition-colors';
                tr.innerHTML = `
                    <td class="px-4 py-3 text-center text-sm font-medium text-gray-500">${index + 1}</td>
                    <td class="px-4 py-3 text-sm text-gray-900 line-clamp-2" title="${q.content}">${q.content}</td>
                    <td class="px-4 py-3 text-sm">${catBadge}</td>
                    <td class="px-4 py-3 text-center">${typeBadge}</td>
                    <td class="px-4 py-3">
                        <input type="number" step="0.25" min="0" value="${q.point || 1}" class="w-full text-sm border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 point-input" data-idx="${index}">
                    </td>
                    <td class="px-4 py-3 text-center">
                        <button type="button" class="text-gray-400 hover:text-red-600 btn-remove-q p-1 rounded hover:bg-red-50 transition-colors" data-idx="${index}">
                            <svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </td>
                `;
                tbody.appendChild(tr);
            });

            totalQ.textContent = selectedQuestions.length;
            totalP.textContent = tPoints.toFixed(2);

            // Bind events
            document.querySelectorAll('.btn-remove-q').forEach(btn => {
                btn.addEventListener('click', function() {
                    selectedQuestions.splice(this.getAttribute('data-idx'), 1);
                    renderSelectedQuestions();
                });
            });

            document.querySelectorAll('.point-input').forEach(inp => {
                inp.addEventListener('change', function() {
                    selectedQuestions[this.getAttribute('data-idx')].point = parseFloat(this.value) || 0;
                    renderSelectedQuestions();
                });
            });
        }

        // Modal Helpers
        function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
        function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

        // 3. BANK MODAL GROUPED BY CATEGORY ("CÂU HỎI ĐƯỢC XẾP THEO DANH MỤC")
        document.getElementById('btnOpenBankModal').addEventListener('click', () => {
            const subjId = subjectSelect.value;
            if (!subjId) {
                document.getElementById('bankWarning').classList.remove('hidden');
                document.getElementById('bankFilters').classList.add('hidden');
                document.getElementById('bankQuestionsContainer').classList.add('hidden');
            } else {
                document.getElementById('bankWarning').classList.add('hidden');
                document.getElementById('bankFilters').classList.add('hidden');
                document.getElementById('bankQuestionsContainer').classList.add('hidden');
                document.getElementById('bankLoading').classList.remove('hidden');
                
                axios.get(`{{ route('teacher.api.questions.index') }}?subject_id=${subjId}`)
                    .then(res => {
                        document.getElementById('bankLoading').classList.add('hidden');
                        document.getElementById('bankFilters').classList.remove('hidden');
                        document.getElementById('bankQuestionsContainer').classList.remove('hidden');
                        
                        bankQuestions = res.data;
                        buildCategoryFilterOptions(bankQuestions);
                        renderBankGroupedQuestions();
                    })
                    .catch(err => {
                        console.error(err);
                        alert('Lỗi tải ngân hàng câu hỏi');
                    });
            }
            openModal('bankModal');
        });

        function buildCategoryFilterOptions(questions) {
            const filter = document.getElementById('bankCategoryFilter');
            filter.innerHTML = '<option value="">-- Tất cả danh mục --</option>';
            const cats = {};
            questions.forEach(q => {
                if (q.category) {
                    cats[q.category.id] = q.category.name;
                }
            });
            Object.keys(cats).forEach(id => {
                filter.innerHTML += `<option value="${id}">${cats[id]}</option>`;
            });
            filter.innerHTML += '<option value="uncategorized">Chưa phân loại</option>';
        }

        function renderBankGroupedQuestions() {
            const container = document.getElementById('bankQuestionsContainer');
            container.innerHTML = '';
            
            const searchVal = document.getElementById('bankSearchInput').value.toLowerCase().trim();
            const catVal = document.getElementById('bankCategoryFilter').value;

            // Filter questions
            let filtered = bankQuestions.filter(q => {
                const matchSearch = !searchVal || q.content.toLowerCase().includes(searchVal);
                let matchCat = true;
                if (catVal === 'uncategorized') {
                    matchCat = !q.category_id;
                } else if (catVal) {
                    matchCat = q.category_id == catVal;
                }
                return matchSearch && matchCat;
            });

            if (filtered.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-10 bg-gray-50 rounded-xl border border-gray-200">
                        <p class="text-gray-500 text-sm">Không tìm thấy câu hỏi phù hợp.</p>
                    </div>
                `;
                updateBankSelectedCount();
                return;
            }

            // Group questions by category
            const groups = {};
            filtered.forEach(q => {
                const groupName = q.category ? q.category.name : 'Chưa phân loại';
                const groupId = q.category ? q.category.id : 0;
                if (!groups[groupId]) {
                    groups[groupId] = {
                        id: groupId,
                        name: groupName,
                        questions: []
                    };
                }
                groups[groupId].questions.push(q);
            });

            Object.values(groups).forEach(group => {
                const groupDiv = document.createElement('div');
                groupDiv.className = 'border border-gray-200 rounded-xl overflow-hidden shadow-sm';
                
                const groupHeader = document.createElement('div');
                groupHeader.className = 'px-4 py-3 bg-gray-50 border-b border-gray-200 flex items-center justify-between';
                groupHeader.innerHTML = `
                    <div class="flex items-center gap-3">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" class="category-group-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500" data-group="${group.id}">
                            <span class="font-bold text-sm text-gray-800">${group.name}</span>
                        </label>
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">${group.questions.length} câu</span>
                    </div>
                    <span class="text-xs text-gray-400">Chọn tất cả danh mục</span>
                `;
                groupDiv.appendChild(groupHeader);

                const table = document.createElement('table');
                table.className = 'w-full text-left border-collapse';
                table.innerHTML = `
                    <tbody class="divide-y divide-gray-100 bg-white">
                    </tbody>
                `;
                const tbody = table.querySelector('tbody');

                group.questions.forEach(q => {
                    const isSelected = selectedQuestions.some(sq => sq.id === q.id);
                    const typeBadge = q.type === 'multiple_choice' ? 'Trắc nghiệm' : 'Tự luận';
                    const diffBadge = q.difficulty === 'easy' ? 'Dễ' : (q.difficulty === 'medium' ? 'TB' : 'Khó');

                    const tr = document.createElement('tr');
                    tr.className = isSelected ? 'bg-blue-50/40' : 'hover:bg-gray-50/80 cursor-pointer';
                    tr.innerHTML = `
                        <td class="px-4 py-3 w-10 text-center">
                            <input type="checkbox" value="${q.id}" data-group="${group.id}" class="bank-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500" ${isSelected ? 'checked disabled' : ''}>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900 line-clamp-2">${q.content}</td>
                        <td class="px-4 py-3 w-28 text-center text-xs">
                            <span class="px-2 py-0.5 rounded ${q.type === 'multiple_choice' ? 'bg-indigo-50 text-indigo-700' : 'bg-purple-50 text-purple-700'}">${typeBadge}</span>
                        </td>
                        <td class="px-4 py-3 w-20 text-center text-xs">
                            <span class="px-2 py-0.5 rounded ${q.difficulty === 'easy' ? 'bg-emerald-50 text-emerald-700' : (q.difficulty === 'medium' ? 'bg-amber-50 text-amber-700' : 'bg-rose-50 text-rose-700')}">${diffBadge}</span>
                        </td>
                    `;
                    
                    if (!isSelected) {
                        tr.addEventListener('click', function(e) {
                            if (e.target.type !== 'checkbox') {
                                const cb = this.querySelector('.bank-checkbox');
                                cb.checked = !cb.checked;
                                updateBankSelectedCount();
                            }
                        });
                    }
                    tbody.appendChild(tr);
                });

                groupDiv.appendChild(table);
                container.appendChild(groupDiv);
            });

            // Group checkbox behavior
            document.querySelectorAll('.category-group-checkbox').forEach(gcb => {
                gcb.addEventListener('change', function() {
                    const gId = this.getAttribute('data-group');
                    document.querySelectorAll(`.bank-checkbox[data-group="${gId}"]:not(:disabled)`).forEach(cb => {
                        cb.checked = this.checked;
                    });
                    updateBankSelectedCount();
                });
            });

            document.querySelectorAll('.bank-checkbox').forEach(cb => {
                cb.addEventListener('change', updateBankSelectedCount);
            });

            updateBankSelectedCount();
        }

        function updateBankSelectedCount() {
            const count = document.querySelectorAll('.bank-checkbox:checked:not(:disabled)').length;
            document.getElementById('bankSelectedCount').textContent = count;
        }

        document.getElementById('bankSearchInput').addEventListener('input', renderBankGroupedQuestions);
        document.getElementById('bankCategoryFilter').addEventListener('change', renderBankGroupedQuestions);

        // Add selected questions from bank
        document.getElementById('btnAddSelectedQuestions').addEventListener('click', () => {
            const checkedBoxes = document.querySelectorAll('.bank-checkbox:checked:not(:disabled)');
            checkedBoxes.forEach(cb => {
                const qId = parseInt(cb.value);
                const qData = bankQuestions.find(q => q.id === qId);
                if (qData && !selectedQuestions.some(sq => sq.id === qId)) {
                    selectedQuestions.push({
                        id: qData.id,
                        content: qData.content,
                        type: qData.type,
                        point: 1,
                        category_name: qData.category ? qData.category.name : null
                    });
                }
            });
            renderSelectedQuestions();
            closeModal('bankModal');
        });

        // 4. MANUAL QUESTION MODAL
        document.getElementById('btnOpenManualModal').addEventListener('click', () => {
            if (!subjectSelect.value) {
                alert('Vui lòng chọn môn học trước khi tạo câu hỏi.');
                return;
            }
            document.getElementById('manualQuestionForm').reset();
            document.getElementById('manualAnswersSection').style.display = 'block';
            openModal('manualModal');
        });

        document.getElementById('manualType').addEventListener('change', function() {
            document.getElementById('manualAnswersSection').style.display = this.value === 'multiple_choice' ? 'block' : 'none';
        });

        document.getElementById('btnSaveManualQuestion').addEventListener('click', function() {
            const form = document.getElementById('manualQuestionForm');
            if (!form.reportValidity()) return;

            const subjId = subjectSelect.value;
            const formData = new FormData(form);
            formData.append('subject_id', subjId);

            this.disabled = true;
            document.getElementById('btnSaveManualText').classList.add('opacity-0');
            document.getElementById('btnSaveManualSpinner').classList.remove('hidden');

            axios.post('{{ route('teacher.api.questions.store') }}', formData)
                .then(res => {
                    if (res.data.success) {
                        const newQ = res.data.question;
                        selectedQuestions.push({
                            id: newQ.id,
                            content: newQ.content,
                            type: newQ.type,
                            point: 1,
                            category_name: newQ.category ? newQ.category.name : null
                        });
                        renderSelectedQuestions();
                        closeModal('manualModal');
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Lỗi tạo câu hỏi. Vui lòng kiểm tra lại dữ liệu.');
                })
                .finally(() => {
                    this.disabled = false;
                    document.getElementById('btnSaveManualText').classList.remove('opacity-0');
                    document.getElementById('btnSaveManualSpinner').classList.add('hidden');
                });
        });

        // 5. SUBMIT FORM
        document.getElementById('btnSubmitForm').addEventListener('click', function() {
            if (!mainForm.reportValidity()) return;
            
            if (selectedQuestions.length === 0) {
                alert('Vui lòng thêm ít nhất một câu hỏi vào đề thi!');
                return;
            }

            // Remove existing dynamic inputs
            document.querySelectorAll('.dynamic-form-input').forEach(e => e.remove());

            // Append hidden inputs for questions and points
            selectedQuestions.forEach(q => {
                const inpQ = document.createElement('input');
                inpQ.type = 'hidden';
                inpQ.name = 'questions[]';
                inpQ.value = q.id;
                inpQ.className = 'dynamic-form-input';
                
                const inpP = document.createElement('input');
                inpP.type = 'hidden';
                inpP.name = `points[${q.id}]`;
                inpP.value = q.point;
                inpP.className = 'dynamic-form-input';

                mainForm.appendChild(inpQ);
                mainForm.appendChild(inpP);
            });

            mainForm.submit();
        });

        // 6. TOGGLE UNLIMITED ATTEMPTS & ANTI-CHEAT SETTINGS
        function toggleUnlimitedAttempts(isUnlimited) {
            const input = document.getElementById('max_attempts_input');
            if (isUnlimited) {
                input.value = '';
                input.disabled = true;
                input.classList.add('bg-gray-100', 'text-gray-400');
            } else {
                input.disabled = false;
                if (!input.value) input.value = '1';
                input.classList.remove('bg-gray-100', 'text-gray-400');
            }
        }

        function toggleAllAntiCheat(isEnabled) {
            const container = document.getElementById('antiCheatSubOptions');
            const checkboxes = container.querySelectorAll('.anti-cheat-sub');
            checkboxes.forEach(cb => {
                cb.disabled = !isEnabled;
                if (!isEnabled) {
                    cb.checked = false;
                } else {
                    cb.checked = true;
                }
            });
            if (isEnabled) {
                container.classList.remove('opacity-40', 'pointer-events-none');
            } else {
                container.classList.add('opacity-40', 'pointer-events-none');
            }
        }
    </script>
@endsection
