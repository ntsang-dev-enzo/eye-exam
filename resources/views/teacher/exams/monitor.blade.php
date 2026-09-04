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
            <div class="text-sm text-gray-500 mt-1.5 flex items-center gap-2 flex-wrap">
                <span>Mã đề: <span class="font-bold text-gray-700">{{ $exam->code }}</span></span>
                <span>•</span>
                <span>Môn: <span class="font-bold text-gray-700">{{ $exam->subject->name ?? 'N/A' }}</span></span>
                <span>•</span>
                <span id="quickProctorCameraTag" class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold {{ ($exam->enable_proctor_camera ?? true) ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-gray-100 text-gray-500 border border-gray-200' }}">
                    <span id="quickProctorCameraDot" class="w-1.5 h-1.5 rounded-full {{ ($exam->enable_proctor_camera ?? true) ? 'bg-emerald-500 animate-pulse' : 'bg-gray-400' }}"></span>
                    <span id="quickProctorCameraText">Camera AI: {{ ($exam->enable_proctor_camera ?? true) ? 'Đang bật' : 'Đã tắt' }}</span>
                </span>
                <span id="quickFaceVerifyTag" class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold {{ ($exam->require_face_verification ?? true) ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'bg-gray-100 text-gray-500 border border-gray-200' }}">
                    <span id="quickFaceVerifyText">Face ID vào thi: {{ ($exam->require_face_verification ?? true) ? 'Bắt buộc' : 'Không yêu cầu' }}</span>
                </span>
                <span id="quickIntervalTag" class="{{ ($exam->enable_proctor_camera ?? true) ? 'inline-flex' : 'hidden' }} items-center px-2 py-0.5 rounded-md text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200/80">
                    Chu kỳ: <strong id="quickIntervalText" class="ml-1">{{ $exam->proctor_interval_seconds ?? 120 }}s / lần</strong>
                </span>
            </div>
        </div>
        <div class="flex items-center gap-2.5 flex-wrap">
            <button type="button" onclick="openQuickSettingsModal()" class="px-3.5 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-xl shadow-xs font-bold text-sm flex items-center gap-2 transition-all cursor-pointer" title="Bật / Tắt Camera giám sát và Nhận diện khuôn mặt">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                <span>Bật/Tắt Giám sát AI</span>
            </button>
            <a href="{{ route('teacher.exams.results', $exam) }}" class="px-3.5 py-2 bg-indigo-50 border border-indigo-200 text-indigo-700 rounded-xl hover:bg-indigo-100 transition-colors font-semibold text-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                Xem kết quả
            </a>
            <a href="{{ route('teacher.exams.index') }}" class="px-3.5 py-2 bg-white border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors font-medium text-sm">
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
    <div class="flex items-center justify-center min-h-screen px-2 sm:px-4 py-3 sm:py-5 text-center">
        <div class="relative bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all w-[96vw] max-w-7xl max-h-[96vh] h-[93vh] p-4 sm:p-6 space-y-3.5 flex flex-col z-10">
            
            <!-- Modal Top Bar -->
            <div class="flex items-center justify-between pb-3 border-b border-gray-100 shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-3 h-3 rounded-full bg-blue-600 animate-pulse"></div>
                    <h3 class="text-base sm:text-lg font-black text-gray-900">Chi tiết Giám sát AI & Nhật ký Thí sinh</h3>
                    <span id="modalAttemptStatus" class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-700">Đang thi</span>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="toggleTopOverview()" id="btnToggleTopOverview" class="text-xs font-bold text-slate-600 hover:text-blue-600 px-3 py-1.5 rounded-xl border border-slate-200 hover:bg-slate-50 flex items-center gap-1.5 transition-all shadow-2xs" title="Thu gọn / Mở rộng thông tin sinh viên để tăng không gian">
                        <svg id="toggleTopIcon" class="w-3.5 h-3.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                        <span id="toggleTopText">Thu gọn thông tin</span>
                    </button>
                    <button type="button" onclick="closeBehaviorModal()" class="text-gray-400 hover:text-gray-600 p-1.5 rounded-xl hover:bg-gray-100 transition-colors">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            </div>

            <!-- Unified Compact Header Bar (Student Identity + Quick Stats + Face ID Comparison) -->
            <div id="modalTopOverview" class="bg-gradient-to-r from-slate-50 via-indigo-50/20 to-blue-50/30 border border-slate-200/80 rounded-2xl p-3 sm:p-3.5 shrink-0 transition-all duration-300">
                <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-3 sm:gap-4">
                    <!-- Left: Student Info & Stat Badges -->
                    <div class="flex items-center gap-3.5 min-w-0">
                        <div id="modalStudentAvatar" class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-700 text-white flex items-center justify-center font-black text-lg shadow-md overflow-hidden shrink-0">
                            ?
                        </div>
                        <div class="min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <h3 id="modalStudentName" class="text-base sm:text-lg font-black text-gray-900 truncate">Đang tải...</h3>
                            </div>
                            <p class="text-[11px] text-gray-500 mt-0.5 truncate">
                                Mã SV: <span id="modalStudentCode" class="font-bold text-gray-700">...</span> • 
                                Email: <span id="modalStudentEmail" class="text-gray-700">...</span>
                            </p>
                            <!-- Quick Compact Stat Badges -->
                            <div class="flex items-center gap-2 mt-2 flex-wrap">
                                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-rose-50 border border-rose-200 rounded-lg text-rose-700 text-xs font-bold shadow-2xs">
                                    <span>🚨 Cảnh báo:</span>
                                    <span id="modalCheatWarnings" class="font-black text-rose-800">0</span>
                                </div>
                                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-amber-50 border border-amber-200 rounded-lg text-amber-700 text-xs font-bold shadow-2xs">
                                    <span>📱 Điện thoại:</span>
                                    <span id="modalPhoneViolations" class="font-black text-amber-800">0</span>
                                </div>
                                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-purple-50 border border-purple-200 rounded-lg text-purple-700 text-xs font-bold shadow-2xs">
                                    <span>👥 Khuôn mặt:</span>
                                    <span id="modalPersonViolations" class="font-black text-purple-800">0</span>
                                </div>
                                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-blue-50 border border-blue-200 rounded-lg text-blue-700 text-xs font-bold shadow-2xs">
                                    <span>⏱️ Rời màn hình:</span>
                                    <span id="modalOutOfScreen" class="font-black text-blue-800">0s</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: 3 Face Comparison Photos (Phần Đối soát Face ID rộng rãi, hiển thị đầy đủ các span) -->
                    <div class="bg-white/95 backdrop-blur-xs border border-slate-200/90 rounded-2xl p-3.5 sm:p-4 shadow-xs shrink-0 flex items-center gap-3 sm:gap-5 justify-around sm:justify-start">
                        <div class="hidden sm:flex flex-col items-center justify-center pr-3.5 border-r border-slate-200 text-center shrink-0 min-w-[95px]">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Đối soát</span>
                            <span class="text-xs font-black text-indigo-600">Face ID</span>
                            <span id="faceMatchScoreBadge" class="mt-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-indigo-100 text-indigo-800 text-center shadow-2xs whitespace-nowrap">
                                --%
                            </span>
                        </div>

                        <!-- 1. Enrolled -->
                        <div class="text-center flex flex-col items-center min-w-[85px] sm:min-w-[95px]">
                            <div class="w-18 h-18 sm:w-20 sm:h-20 rounded-2xl bg-slate-100 overflow-hidden border border-slate-200 shadow-xs flex items-center justify-center relative">
                                <img id="compEnrolledImg" class="w-full h-full object-cover hidden" alt="Ảnh hồ sơ gốc">
                                <span id="compEnrolledPlaceholder" class="text-xs text-slate-400 font-semibold">Gốc</span>
                            </div>
                            <span class="text-xs font-bold text-slate-800 mt-2 block whitespace-nowrap">1. Hồ sơ gốc</span>
                            <span class="text-[10px] text-slate-400 block whitespace-nowrap mt-0.5">Đăng ký ban đầu</span>
                        </div>

                        <!-- 2. Pre-exam Scan -->
                        <div class="text-center flex flex-col items-center min-w-[85px] sm:min-w-[95px]">
                            <div class="w-18 h-18 sm:w-20 sm:h-20 rounded-2xl bg-slate-100 overflow-hidden border border-slate-200 shadow-xs flex items-center justify-center relative">
                                <img id="compVerifyImg" class="w-full h-full object-cover hidden" alt="Ảnh quét trước thi">
                                <span id="compVerifyPlaceholder" class="text-xs text-slate-400 font-semibold">Vào thi</span>
                                <div id="compVerifyBadge" class="hidden absolute bottom-1 right-1 px-1.5 py-0.5 rounded-md bg-emerald-600 text-white text-[9px] font-bold shadow-xs">
                                    92%
                                </div>
                            </div>
                            <span class="text-xs font-bold text-slate-800 mt-2 block whitespace-nowrap">2. Quét vào thi</span>
                            <span id="compVerifyTime" class="text-[10px] text-slate-500 font-medium block whitespace-nowrap mt-0.5">Trước khi vào</span>
                        </div>

                        <!-- 3. Latest In-exam Snapshot -->
                        <div class="text-center flex flex-col items-center min-w-[85px] sm:min-w-[95px]">
                            <div class="w-18 h-18 sm:w-20 sm:h-20 rounded-2xl bg-slate-100 overflow-hidden border border-slate-200 shadow-xs flex items-center justify-center relative">
                                <img id="compLatestImg" class="w-full h-full object-cover hidden" alt="Ảnh mới nhất">
                                <span id="compLatestPlaceholder" class="text-xs text-slate-400 font-semibold">Chưa có</span>
                                <div id="compLatestStatusBadge" class="hidden absolute top-1 right-1 px-1.5 py-0.5 rounded-md text-[9px] font-bold bg-emerald-500 text-white shadow-xs">
                                    OK
                                </div>
                                <div id="compLatestSimBadge" class="hidden absolute bottom-1 right-1 px-1.5 py-0.5 rounded-md text-[9px] font-bold bg-indigo-600 text-white shadow-xs">
                                    --%
                                </div>
                            </div>
                            <span class="text-xs font-bold text-slate-800 mt-2 block whitespace-nowrap">3. Chụp lúc thi</span>
                            <span id="compLatestSimText" class="text-[10px] font-extrabold text-indigo-700 block whitespace-nowrap mt-0.5">Độ khớp: --%</span>
                            <span id="compLatestTime" class="text-[10px] text-slate-500 font-mono block whitespace-nowrap">Camera phòng thi</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Nav Tabs between Snapshots, Timeline, and Split View -->
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-100 pb-2.5 shrink-0">
                <div class="flex items-center gap-1.5 sm:gap-2">
                    <button type="button" onclick="switchBehaviorTab('snapshots')" id="tabBtnSnapshots" class="px-3.5 py-1.5 text-xs font-bold rounded-xl bg-blue-600 text-white shadow-sm flex items-center gap-1.5 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path></svg>
                        <span>Bộ sưu tập Ảnh AI (<span id="snapshotsCountBadge">0</span>)</span>
                    </button>
                    <button type="button" onclick="switchBehaviorTab('timeline')" id="tabBtnTimeline" class="px-3.5 py-1.5 text-xs font-bold rounded-xl bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>Nhật ký Thao tác (Timeline)</span>
                    </button>
                    <button type="button" onclick="switchBehaviorTab('split')" id="tabBtnSplit" class="px-3.5 py-1.5 text-xs font-bold rounded-xl bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors flex items-center gap-1.5" title="Xem song song Ảnh AI bên trái và Nhật ký thao tác bên phải">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"></path></svg>
                        <span class="hidden sm:inline">Xem song song (Ảnh AI + Nhật ký)</span>
                        <span class="sm:hidden">Song song</span>
                    </button>
                </div>

                <!-- Snapshot filters -->
                <div id="snapshotFilterButtons" class="flex items-center gap-1.5">
                    <span class="text-[11px] text-gray-400 font-medium hidden md:inline">Lọc ảnh:</span>
                    <button type="button" onclick="filterSnapshots('all')" class="snapshot-filter-btn px-2.5 py-1 text-xs font-semibold rounded-lg bg-blue-600 text-white shadow-2xs" data-snap-filter="all">Tất cả</button>
                    <button type="button" onclick="filterSnapshots('violations')" class="snapshot-filter-btn px-2.5 py-1 text-xs font-semibold rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200" data-snap-filter="violations">⚠️ Vi phạm AI</button>
                    <button type="button" onclick="filterSnapshots('normal')" class="snapshot-filter-btn px-2.5 py-1 text-xs font-semibold rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200" data-snap-filter="normal">✅ Bình thường</button>
                </div>

                <!-- Timeline filters -->
                <div id="timelineFilterButtons" class="hidden flex items-center gap-1.5">
                    <span class="text-[11px] text-gray-400 font-medium hidden md:inline">Lọc sự kiện:</span>
                    <button type="button" onclick="filterTimeline('all')" class="timeline-tab px-2.5 py-1 text-xs font-semibold rounded-lg bg-blue-600 text-white shadow-2xs" data-filter="all">Tất cả</button>
                    <button type="button" onclick="filterTimeline('violations')" class="timeline-tab px-2.5 py-1 text-xs font-semibold rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200" data-filter="violations">⚠️ Vi phạm</button>
                    <button type="button" onclick="filterTimeline('screen')" class="timeline-tab px-2.5 py-1 text-xs font-semibold rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200" data-filter="screen">🖥️ Rời màn hình</button>
                </div>
            </div>

            <!-- Content Display Area (Expansive Spacious Viewport) -->
            <div id="contentDisplayArea" class="flex-1 min-h-0 overflow-hidden relative">
                <!-- Content Area 1: Snapshots Gallery -->
                <div id="snapshotsContainer" class="h-full overflow-y-auto pr-1">
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3.5 p-1" id="snapshotsGrid">
                        <div class="col-span-full text-center py-12 text-gray-400 text-sm">Đang tải ảnh giám sát...</div>
                    </div>
                </div>

                <!-- Content Area 2: Timeline Event List -->
                <div id="timelineContainer" class="hidden h-full overflow-y-auto pr-2 max-w-4xl mx-auto py-1">
                    <div class="text-center py-12 text-gray-400 text-sm">Đang tải nhật ký thao tác...</div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="pt-2.5 border-t border-gray-100 flex items-center justify-between shrink-0">
                <span class="text-[11px] text-gray-400 hidden sm:inline">💡 Gợi ý: Nhấp vào bất kỳ ảnh nào để phóng to và xem các hộp nhận diện YOLO (Bounding boxes). Dùng "Xem song song" để đối chiếu ảnh và nhật ký cùng lúc.</span>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="refreshCurrentStudentBehavior()" class="px-3.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-xl transition-colors flex items-center gap-1.5 shadow-2xs">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                        Làm mới
                    </button>
                    <button type="button" onclick="closeBehaviorModal()" class="px-4 py-1.5 bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold rounded-xl transition-colors shadow-2xs">
                        Đóng cửa sổ
                    </button>
                </div>
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

<!-- ====================================================
     QUICK PROCTORING SETTINGS MODAL
     ==================================================== -->
<div id="quickSettingsModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-xs transition-opacity" onclick="closeQuickSettingsModal()"></div>
    <div class="flex items-center justify-center min-h-screen px-4 py-6 text-center">
        <div class="relative bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all max-w-lg w-full p-6 space-y-5 z-10 border border-slate-100">
            <!-- Header -->
            <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-600 text-white flex items-center justify-center font-bold shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Cài đặt Giám sát Phòng thi</h3>
                        <p class="text-xs text-gray-500">Bật / Tắt trực tiếp cho kỳ thi hiện tại</p>
                    </div>
                </div>
                <button type="button" onclick="closeQuickSettingsModal()" class="text-gray-400 hover:text-gray-600 p-1.5 rounded-xl hover:bg-gray-100 transition-colors cursor-pointer">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Toggles Form -->
            <div class="space-y-4">
                <!-- 1. Real-time Proctor Camera Toggle -->
                <div class="p-4 rounded-2xl border transition-all" id="quickCameraCard">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-900">Camera gian lận thời gian thực</h4>
                                <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">Bật webcam giám sát xuyên suốt bài thi, AI phát hiện 0/nhiều khuôn mặt, quay mặt quá lâu & tự động chụp bằng chứng.</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer shrink-0 mt-1">
                            <input type="checkbox" id="quickToggleCamera" class="sr-only peer" onchange="onQuickToggleCameraChange(this.checked)">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                        </label>
                    </div>

                    <!-- Periodic Capture Interval Box inside Quick Settings ("vẫn cho giảng viên set chụp định kỳ") -->
                    <div id="quickModalIntervalContainer" class="mt-3.5 pt-3.5 border-t border-slate-200/80 space-y-2.5">
                        <div class="flex items-center justify-between gap-2">
                            <label for="quickModalIntervalInput" class="text-xs font-bold text-gray-800 flex items-center gap-1.5">
                                <span>⏱️ Chu kỳ chụp ảnh định kỳ:</span>
                            </label>
                            <div class="relative w-28">
                                <input type="number" id="quickModalIntervalInput" min="15" max="1800" step="5" class="w-full px-2.5 py-1 text-xs font-bold text-indigo-900 bg-white border border-indigo-300 rounded-lg text-right pr-9 shadow-2xs">
                                <span class="absolute right-2 top-1/2 -translate-y-1/2 text-[11px] text-indigo-500 font-semibold pointer-events-none">giây</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <span class="text-[10px] text-gray-400 font-medium mr-0.5">Chọn nhanh:</span>
                            <button type="button" onclick="document.getElementById('quickModalIntervalInput').value=30" class="px-2 py-0.5 text-[10px] font-bold bg-slate-100 hover:bg-indigo-600 hover:text-white text-slate-700 rounded border border-slate-200 transition-colors cursor-pointer">30s</button>
                            <button type="button" onclick="document.getElementById('quickModalIntervalInput').value=60" class="px-2 py-0.5 text-[10px] font-bold bg-slate-100 hover:bg-indigo-600 hover:text-white text-slate-700 rounded border border-slate-200 transition-colors cursor-pointer">1p</button>
                            <button type="button" onclick="document.getElementById('quickModalIntervalInput').value=90" class="px-2 py-0.5 text-[10px] font-bold bg-slate-100 hover:bg-indigo-600 hover:text-white text-slate-700 rounded border border-slate-200 transition-colors cursor-pointer">1.5p</button>
                            <button type="button" onclick="document.getElementById('quickModalIntervalInput').value=120" class="px-2 py-0.5 text-[10px] font-bold bg-slate-100 hover:bg-indigo-600 hover:text-white text-slate-700 rounded border border-slate-200 transition-colors cursor-pointer">2p</button>
                            <button type="button" onclick="document.getElementById('quickModalIntervalInput').value=180" class="px-2 py-0.5 text-[10px] font-bold bg-slate-100 hover:bg-indigo-600 hover:text-white text-slate-700 rounded border border-slate-200 transition-colors cursor-pointer">3p</button>
                            <button type="button" onclick="document.getElementById('quickModalIntervalInput').value=300" class="px-2 py-0.5 text-[10px] font-bold bg-slate-100 hover:bg-indigo-600 hover:text-white text-slate-700 rounded border border-slate-200 transition-colors cursor-pointer">5p</button>
                        </div>
                    </div>
                </div>

                <!-- 2. Face Verification Entry Toggle -->
                <div class="p-4 rounded-2xl border transition-all" id="quickFaceCard">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-900">Nhận diện khuôn mặt để vào thi (Face ID)</h4>
                                <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">Bắt buộc quét nhận diện khuôn mặt so khớp với hồ sơ gốc trước khi cấp đề vào phòng thi.</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer shrink-0 mt-1">
                            <input type="checkbox" id="quickToggleFace" class="sr-only peer" onchange="onQuickToggleFaceChange(this.checked)">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Footer Action Buttons -->
            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-gray-100">
                <button type="button" onclick="closeQuickSettingsModal()" class="px-4 py-2 text-xs font-bold text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-xl transition-colors cursor-pointer">
                    Hủy
                </button>
                <button type="button" id="btnSaveQuickSettings" onclick="submitQuickSettings()" class="px-5 py-2 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-xl shadow-xs transition-all flex items-center gap-1.5 cursor-pointer">
                    <span>Lưu cài đặt ngay</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    const examId = '{{ $exam->id }}';
    const monitorUrl = '{{ route("teacher.exams.api-monitor", $exam->id) }}';
    let currentExamEnableCamera = {{ ($exam->enable_proctor_camera ?? true) ? 'true' : 'false' }};
    let currentExamRequireFace = {{ ($exam->require_face_verification ?? true) ? 'true' : 'false' }};
    let currentExamInterval = {{ (int) ($exam->proctor_interval_seconds ?? 120) }};
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
                    } else if (!currentExamRequireFace) {
                        faceBadge = '<span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-gray-100 text-gray-500">Không yêu cầu</span>';
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
                    } else if (!currentExamEnableCamera) {
                        cameraCol = '<span class="text-xs text-gray-400 font-medium">Đã tắt</span>';
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
    let currentSnapFilter = 'all';

    function toggleTopOverview() {
        const overview = document.getElementById('modalTopOverview');
        const icon = document.getElementById('toggleTopIcon');
        const text = document.getElementById('toggleTopText');
        if (!overview) return;

        if (overview.classList.contains('hidden')) {
            overview.classList.remove('hidden');
            if (icon) icon.classList.remove('rotate-180');
            if (text) text.innerText = 'Thu gọn thông tin';
        } else {
            overview.classList.add('hidden');
            if (icon) icon.classList.add('rotate-180');
            if (text) text.innerText = 'Mở rộng thông tin';
        }
    }

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

    function filterSnapshots(filter) {
        currentSnapFilter = filter;
        document.querySelectorAll('.snapshot-filter-btn').forEach(btn => {
            if (btn.dataset.snapFilter === filter) {
                btn.className = 'snapshot-filter-btn px-2.5 py-1 text-xs font-semibold rounded-lg bg-blue-600 text-white shadow-2xs';
            } else {
                btn.className = 'snapshot-filter-btn px-2.5 py-1 text-xs font-semibold rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200';
            }
        });
        renderSnapshotsGrid();
    }

    function switchBehaviorTab(tab) {
        currentTab = tab;
        const btnSnap = document.getElementById('tabBtnSnapshots');
        const btnTime = document.getElementById('tabBtnTimeline');
        const btnSplit = document.getElementById('tabBtnSplit');
        const contentArea = document.getElementById('contentDisplayArea');
        const snapContainer = document.getElementById('snapshotsContainer');
        const timeContainer = document.getElementById('timelineContainer');
        const snapGrid = document.getElementById('snapshotsGrid');
        const snapFilters = document.getElementById('snapshotFilterButtons');
        const timeFilters = document.getElementById('timelineFilterButtons');

        if (tab === 'snapshots') {
            btnSnap.className = 'px-3.5 py-1.5 text-xs font-bold rounded-xl bg-blue-600 text-white shadow-sm flex items-center gap-1.5 transition-all';
            btnTime.className = 'px-3.5 py-1.5 text-xs font-bold rounded-xl bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors flex items-center gap-1.5';
            if (btnSplit) btnSplit.className = 'px-3.5 py-1.5 text-xs font-bold rounded-xl bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors flex items-center gap-1.5';

            contentArea.className = 'flex-1 min-h-0 overflow-hidden relative';
            snapContainer.className = 'h-full overflow-y-auto pr-1';
            timeContainer.className = 'hidden h-full overflow-y-auto pr-2 max-w-4xl mx-auto py-1';
            snapGrid.className = 'grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3.5 p-1';

            snapFilters.classList.remove('hidden');
            timeFilters.classList.add('hidden');
        } else if (tab === 'timeline') {
            btnTime.className = 'px-3.5 py-1.5 text-xs font-bold rounded-xl bg-blue-600 text-white shadow-sm flex items-center gap-1.5 transition-all';
            btnSnap.className = 'px-3.5 py-1.5 text-xs font-bold rounded-xl bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors flex items-center gap-1.5';
            if (btnSplit) btnSplit.className = 'px-3.5 py-1.5 text-xs font-bold rounded-xl bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors flex items-center gap-1.5';

            contentArea.className = 'flex-1 min-h-0 overflow-hidden relative';
            snapContainer.className = 'hidden h-full overflow-y-auto pr-1';
            timeContainer.className = 'h-full overflow-y-auto pr-2 max-w-4xl mx-auto py-1';

            snapFilters.classList.add('hidden');
            timeFilters.classList.remove('hidden');
        } else if (tab === 'split') {
            if (btnSplit) btnSplit.className = 'px-3.5 py-1.5 text-xs font-bold rounded-xl bg-blue-600 text-white shadow-sm flex items-center gap-1.5 transition-all';
            btnSnap.className = 'px-3.5 py-1.5 text-xs font-bold rounded-xl bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors flex items-center gap-1.5';
            btnTime.className = 'px-3.5 py-1.5 text-xs font-bold rounded-xl bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors flex items-center gap-1.5';

            contentArea.className = 'flex-1 min-h-0 overflow-hidden grid grid-cols-1 lg:grid-cols-12 gap-3.5';
            snapContainer.className = 'lg:col-span-7 h-full overflow-y-auto pr-2 border-b lg:border-b-0 lg:border-r border-slate-200/80';
            timeContainer.className = 'lg:col-span-5 h-full overflow-y-auto pr-2 py-1';
            snapGrid.className = 'grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-3 p-1';

            snapContainer.classList.remove('hidden');
            timeContainer.classList.remove('hidden');
            snapFilters.classList.remove('hidden');
            timeFilters.classList.remove('hidden');
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
                    
                    const badge = document.getElementById('compLatestStatusBadge');
                    badge.classList.remove('hidden');
                    if (latest.status === 'violation') {
                        badge.className = 'absolute top-1 right-1 px-1.5 py-0.5 rounded text-[9px] font-bold bg-rose-500 text-white shadow-xs';
                        badge.innerText = 'Vi phạm';
                    } else {
                        badge.className = 'absolute top-1 right-1 px-1.5 py-0.5 rounded text-[9px] font-bold bg-emerald-500 text-white shadow-xs';
                        badge.innerText = 'Bình thường';
                    }

                    const simBadge = document.getElementById('compLatestSimBadge');
                    const simText = document.getElementById('compLatestSimText');
                    if (latest.face_similarity !== null && latest.face_similarity !== undefined) {
                        const simVal = Number(latest.face_similarity);
                        if (simBadge) {
                            simBadge.classList.remove('hidden');
                            simBadge.className = `absolute bottom-1 right-1 px-1.5 py-0.5 rounded text-[9px] font-bold text-white shadow-xs ${simVal >= 50 ? 'bg-indigo-600' : 'bg-rose-600'}`;
                            simBadge.innerText = `${simVal}% Khớp`;
                        }
                        if (simText) {
                            simText.className = `text-[10px] font-extrabold ${simVal >= 50 ? 'text-indigo-700' : 'text-rose-600'} block whitespace-nowrap mt-0.5`;
                            simText.innerText = `🎯 Độ khớp lúc thi: ${simVal}%`;
                        }
                    } else {
                        if (simBadge) simBadge.classList.add('hidden');
                        if (simText) {
                            simText.className = 'text-[10px] text-slate-400 block whitespace-nowrap mt-0.5';
                            simText.innerText = 'Chưa so khớp mặt';
                        }
                    }

                    document.getElementById('compLatestTime').innerText = latest.captured_time || latest.captured_at;

                    // Update Top Comparison Badge to show both
                    const vSim = data.attempt.face_similarity ? `${data.attempt.face_similarity}% (Vào thi)` : '';
                    const lSim = latest.face_similarity !== null && latest.face_similarity !== undefined ? `${latest.face_similarity}% (Lúc thi)` : '';
                    const combinedText = [vSim, lSim].filter(Boolean).join(' • ');
                    if (combinedText) {
                        document.getElementById('faceMatchScoreBadge').className = 'mt-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-indigo-100 text-indigo-800 text-center shadow-2xs whitespace-nowrap';
                        document.getElementById('faceMatchScoreBadge').innerText = `Khớp Face ID: ${combinedText}`;
                    }
                } else {
                    document.getElementById('compLatestImg').classList.add('hidden');
                    document.getElementById('compLatestPlaceholder').classList.remove('hidden');
                    document.getElementById('compLatestStatusBadge').classList.add('hidden');
                    const simBadge = document.getElementById('compLatestSimBadge');
                    if (simBadge) simBadge.classList.add('hidden');
                    const simText = document.getElementById('compLatestSimText');
                    if (simText) {
                        simText.className = 'text-[10px] text-slate-400 block whitespace-nowrap mt-0.5';
                        simText.innerText = 'Chưa có ảnh';
                    }
                    document.getElementById('compLatestTime').innerText = 'Chưa có ảnh';
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
                <div class="col-span-full bg-gray-50 rounded-2xl p-10 text-center border border-gray-100 flex flex-col items-center justify-center">
                    <svg class="w-10 h-10 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <p class="text-xs font-semibold text-gray-500">Chưa có ảnh chụp giám sát nào trong bài thi này.</p>
                </div>
            `;
            return;
        }

        let filteredSnaps = currentSnapshotsData;
        if (currentSnapFilter === 'violations') {
            filteredSnaps = currentSnapshotsData.filter(s => s.status === 'violation');
        } else if (currentSnapFilter === 'normal') {
            filteredSnaps = currentSnapshotsData.filter(s => s.status !== 'violation');
        }

        if (filteredSnaps.length === 0) {
            grid.innerHTML = `
                <div class="col-span-full bg-gray-50 rounded-2xl p-10 text-center border border-gray-100 flex flex-col items-center justify-center">
                    <svg class="w-10 h-10 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                    <p class="text-xs font-semibold text-gray-500">Không có ảnh nào khớp với bộ lọc "${currentSnapFilter === 'violations' ? 'Vi phạm AI' : 'Bình thường'}".</p>
                </div>
            `;
            return;
        }

        filteredSnaps.forEach((snap) => {
            const originalIdx = currentSnapshotsData.indexOf(snap);
            const isViolation = snap.status === 'violation';
            const card = document.createElement('div');
            card.className = `group bg-white rounded-2xl p-2.5 border ${isViolation ? 'border-rose-300 ring-2 ring-rose-100 shadow-rose-100/50' : 'border-slate-200'} shadow-xs hover:shadow-md hover:border-blue-400 transition-all cursor-pointer flex flex-col justify-between`;
            card.onclick = () => openSnapshotLightbox(originalIdx);

            let tagHtml = `<span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">Hợp lệ</span>`;
            if (isViolation) {
                tagHtml = `<span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-600 text-white shadow-xs animate-pulse">Vi phạm AI</span>`;
            }
            const simBadge = snap.face_similarity ? `<span class="px-1.5 py-0.5 rounded text-[9px] font-bold ${snap.face_similarity >= 50 ? 'bg-indigo-600' : 'bg-rose-600'} text-white shadow-2xs">${snap.face_similarity}% Khớp</span>` : '';

            card.innerHTML = `
                <div>
                    <div class="relative aspect-[4/3] rounded-xl overflow-hidden bg-slate-900 mb-2">
                        <img src="${snap.image_url}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        <div class="absolute top-1.5 right-1.5 flex items-center gap-1">${simBadge}${tagHtml}</div>
                        <div class="absolute bottom-1.5 left-1.5 px-1.5 py-0.5 bg-black/70 backdrop-blur-xs rounded text-[9px] font-mono text-white">
                            ${snap.captured_time || snap.captured_at}
                        </div>
                    </div>
                </div>
                <p class="text-[11px] text-gray-600 line-clamp-2 leading-tight mt-1">${snap.details || 'Khung hình bình thường.'}</p>
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

    // ====================================================
    // QUICK PROCTORING SETTINGS CONTROLS
    // ====================================================
    function openQuickSettingsModal() {
        document.getElementById('quickToggleCamera').checked = currentExamEnableCamera;
        document.getElementById('quickToggleFace').checked = currentExamRequireFace;
        document.getElementById('quickModalIntervalInput').value = currentExamInterval;
        
        onQuickToggleCameraChange(currentExamEnableCamera);
        onQuickToggleFaceChange(currentExamRequireFace);
        
        document.getElementById('quickSettingsModal').classList.remove('hidden');
    }

    function closeQuickSettingsModal() {
        document.getElementById('quickSettingsModal').classList.add('hidden');
    }

    function onQuickToggleCameraChange(checked) {
        const card = document.getElementById('quickCameraCard');
        const intervalBox = document.getElementById('quickModalIntervalContainer');
        if (checked) {
            card.className = 'p-4 rounded-2xl border border-emerald-300 bg-emerald-50/30 transition-all';
            intervalBox.style.display = 'block';
        } else {
            card.className = 'p-4 rounded-2xl border border-gray-200 bg-gray-50/40 transition-all';
            intervalBox.style.display = 'none';
        }
    }

    function onQuickToggleFaceChange(checked) {
        const card = document.getElementById('quickFaceCard');
        if (checked) {
            card.className = 'p-4 rounded-2xl border border-indigo-300 bg-indigo-50/30 transition-all';
        } else {
            card.className = 'p-4 rounded-2xl border border-gray-200 bg-gray-50/40 transition-all';
        }
    }

    function submitQuickSettings() {
        const btn = document.getElementById('btnSaveQuickSettings');
        btn.disabled = true;
        btn.innerText = 'Đang lưu...';

        const enableCamera = document.getElementById('quickToggleCamera').checked;
        const requireFace = document.getElementById('quickToggleFace').checked;
        const intervalSec = parseInt(document.getElementById('quickModalIntervalInput').value) || 120;

        fetch('{{ route('teacher.exams.update-quick-settings', $exam) }}', {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                enable_proctor_camera: enableCamera,
                require_face_verification: requireFace,
                proctor_interval_seconds: intervalSec
            })
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerText = 'Lưu cài đặt ngay';

            if (data.success) {
                currentExamEnableCamera = data.exam.enable_proctor_camera;
                currentExamRequireFace = data.exam.require_face_verification;
                currentExamInterval = data.exam.proctor_interval_seconds;

                // Update Header Badges
                const camTag = document.getElementById('quickProctorCameraTag');
                const camDot = document.getElementById('quickProctorCameraDot');
                const camText = document.getElementById('quickProctorCameraText');
                if (currentExamEnableCamera) {
                    camTag.className = 'inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200';
                    camDot.className = 'w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse';
                    camText.innerText = 'Camera AI: Đang bật';
                } else {
                    camTag.className = 'inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-500 border border-gray-200';
                    camDot.className = 'w-1.5 h-1.5 rounded-full bg-gray-400';
                    camText.innerText = 'Camera AI: Đã tắt';
                }

                const faceTag = document.getElementById('quickFaceVerifyTag');
                const faceText = document.getElementById('quickFaceVerifyText');
                if (currentExamRequireFace) {
                    faceTag.className = 'inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200';
                    faceText.innerText = 'Face ID vào thi: Bắt buộc';
                } else {
                    faceTag.className = 'inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-500 border border-gray-200';
                    faceText.innerText = 'Face ID vào thi: Không yêu cầu';
                }

                const intervalTag = document.getElementById('quickIntervalTag');
                const intervalText = document.getElementById('quickIntervalText');
                if (currentExamEnableCamera) {
                    intervalTag.className = 'inline-flex items-center px-2 py-0.5 rounded-md text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200/80';
                    intervalText.innerText = `${currentExamInterval}s / lần`;
                } else {
                    intervalTag.className = 'hidden items-center px-2 py-0.5 rounded-md text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200/80';
                }

                closeQuickSettingsModal();
                fetchMonitorData();
            } else {
                alert(data.error || 'Có lỗi xảy ra khi lưu cài đặt.');
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerText = 'Lưu cài đặt ngay';
            console.error(err);
            alert('Lỗi kết nối khi lưu cài đặt.');
        });
    }
</script>
@endsection
