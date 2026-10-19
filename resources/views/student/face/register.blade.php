@extends('layouts.student')

@section('title', 'Đăng ký nhận diện khuôn mặt')

@section('content')
<style>
    @keyframes scanLineAnim {
        0% { top: 6%; opacity: 0.8; }
        50% { top: 90%; opacity: 1; }
        100% { top: 6%; opacity: 0.8; }
    }
    .laser-scan-line {
        position: absolute;
        left: 8%;
        right: 8%;
        height: 3px;
        background: linear-gradient(90deg, transparent, #10b981, #38bdf8, #10b981, transparent);
        box-shadow: 0 0 12px #10b981, 0 0 24px #38bdf8;
        border-radius: 9999px;
        animation: scanLineAnim 2.2s ease-in-out infinite;
        pointer-events: none;
        z-index: 20;
    }
</style>

<div class="max-w-4xl mx-auto space-y-6 pb-12">
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-700 via-blue-600 to-slate-900 rounded-2xl p-6 sm:p-8 text-white shadow-2xs relative overflow-hidden border border-blue-700/50">
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-white/10 border border-white/15 rounded-full text-xs font-semibold mb-3">
                    <svg class="w-4 h-4 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    Bảo mật danh tính & Chống gian lận
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Đăng ký Nhận diện Khuôn mặt (Face ID)</h1>
                <p class="text-blue-100 text-xs sm:text-sm mt-1 max-w-xl">
                    Hệ thống cần thu thập 3 góc nhìn khuôn mặt của bạn để tạo hồ sơ xác thực danh tính bằng AI (ArcFace). Dữ liệu này dùng để xác minh bạn trước mỗi kỳ thi.
                </p>
            </div>
            @if($user->face_registered)
                <div class="shrink-0 bg-white/15 border border-white/20 px-4 py-2 rounded-xl text-center">
                    <span class="inline-flex items-center gap-1.5 text-emerald-300 text-xs font-bold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Đã đăng ký Face ID
                    </span>
                    <p class="text-[11px] text-blue-100 mt-0.5">Bạn có thể chụp lại để cập nhật</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Steps Wizard Horizontal Cards -->
    <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-2xs border border-slate-200/80">
        <div class="grid grid-cols-3 gap-3">
            <div id="stepIndicator1" class="flex items-center gap-3 p-3 rounded-xl bg-blue-50 border-2 border-blue-600 transition-all">
                <div class="w-8 h-8 rounded-lg bg-blue-600 text-white flex items-center justify-center font-bold text-xs shrink-0">
                    1
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-bold text-blue-900 truncate">Nhìn thẳng</p>
                    <p class="text-[11px] text-blue-600 truncate">Góc chính diện</p>
                </div>
            </div>

            <div id="stepIndicator2" class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 border border-slate-200/80 transition-all opacity-60">
                <div class="w-8 h-8 rounded-lg bg-slate-200 text-slate-600 flex items-center justify-center font-bold text-xs shrink-0">
                    2
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-bold text-slate-700 truncate">Nghiêng trái</p>
                    <p class="text-[11px] text-slate-400 truncate">Khoảng 15° - 20°</p>
                </div>
            </div>

            <div id="stepIndicator3" class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 border border-slate-200/80 transition-all opacity-60">
                <div class="w-8 h-8 rounded-lg bg-slate-200 text-slate-600 flex items-center justify-center font-bold text-xs shrink-0">
                    3
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-bold text-slate-700 truncate">Nghiêng phải</p>
                    <p class="text-[11px] text-slate-400 truncate">Khoảng 15° - 20°</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Registration Workspace -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Left: Webcam Viewport & Controls (7 cols) -->
        <div class="lg:col-span-7 bg-white rounded-2xl p-5 sm:p-6 shadow-2xs border border-slate-200/80 flex flex-col items-center">
            
            <!-- Instructions banner -->
            <div id="statusInstruction" class="w-full bg-blue-50 border border-blue-200/80 text-blue-800 text-xs sm:text-sm font-semibold px-4 py-2.5 rounded-xl mb-4 text-center flex items-center justify-center gap-2">
                <span class="relative flex h-2.5 w-2.5">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-blue-600"></span>
                </span>
                <span id="instructionText">Đang khởi tạo camera và tải mô hình AI...</span>
            </div>

            <!-- Video Viewport Container -->
            <div class="relative w-full aspect-[4/3] max-w-md bg-slate-900 rounded-2xl overflow-hidden shadow-inner flex items-center justify-center border-4 border-slate-800">
                <video id="webcamVideo" autoplay playsinline muted class="w-full h-full object-cover transform -scale-x-100"></video>
                <canvas id="faceOverlay" class="absolute inset-0 w-full h-full pointer-events-none transform -scale-x-100"></canvas>

                <!-- Camera Flash Effect -->
                <div id="cameraFlash" class="absolute inset-0 bg-white opacity-0 pointer-events-none transition-opacity duration-200 z-30"></div>

                <!-- Oval Face Guide -->
                <div id="faceGuideBox" class="absolute inset-0 m-auto w-56 h-72 border-2 border-dashed border-white/50 rounded-[50%] pointer-events-none transition-all duration-300 flex items-center justify-center overflow-hidden">
                    <!-- Laser Scanning Line -->
                    <div class="laser-scan-line"></div>

                    <!-- Dynamic Progress Ring for auto-capture -->
                    <svg class="absolute inset-0 w-full h-full -rotate-90 pointer-events-none" viewBox="0 0 100 100">
                        <circle cx="50" cy="50" r="46" fill="none" stroke="rgba(255,255,255,0.15)" stroke-width="3" />
                        <circle id="progressCircle" cx="50" cy="50" r="46" fill="none" stroke="#10b981" stroke-width="4" stroke-dasharray="289" stroke-dashoffset="289" class="transition-all duration-100" />
                    </svg>

                    <!-- Center prompt icon / arrow -->
                    <div id="guideIconContainer" class="text-white/90 text-center space-y-1.5 z-20 pointer-events-none flex flex-col items-center justify-center">
                        <div id="guideArrow"></div>
                        <span id="guideHintText" class="text-[11px] font-bold px-2.5 py-1 bg-black/60 rounded-full backdrop-blur-xs shadow-sm">1. Nhìn thẳng camera</span>
                    </div>
                </div>

                <!-- Camera offline overlay -->
                <div id="cameraOfflineMsg" class="hidden absolute inset-0 bg-slate-900/90 text-white flex flex-col items-center justify-center p-6 text-center">
                    <svg class="w-12 h-12 text-rose-500 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18"></path></svg>
                    <p class="font-bold text-sm">Không thể kết nối camera</p>
                    <p class="text-xs text-slate-400 mt-1">Vui lòng cấp quyền truy cập webcam trong trình duyệt để tiếp tục.</p>
                    <button type="button" onclick="startCamera()" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-xl text-xs font-semibold hover:bg-blue-700">Thử lại</button>
                </div>
            </div>

            <!-- Viewport Controls & Live Status Control Bar -->
            <div class="mt-4 w-full max-w-md space-y-3">
                <div class="flex items-center justify-between gap-3 bg-slate-50 p-3 rounded-xl border border-slate-200/80">
                    <div class="flex items-center gap-2 text-xs">
                        <span id="aiBadge" class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                            Đang tải AI...
                        </span>
                        <span id="poseBadge" class="text-slate-600 font-semibold">Chưa phát hiện mặt</span>
                    </div>

                    <!-- Manual Capture Button -->
                    <button type="button" onclick="manualCaptureAngle()" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-lg transition-all shadow-2xs flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        <span>Chụp thủ công</span>
                    </button>
                </div>

                <div class="text-[11px] text-slate-400 font-medium flex items-center justify-center gap-1 text-center">
                    <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>Thiết bị: Webcam mặc định • Ánh sáng tốt • Hỗ trợ đeo kính</span>
                </div>
            </div>
        </div>

        <!-- Right: Captured Angles Gallery & Final Submission (5 cols) -->
        <div class="lg:col-span-5 flex flex-col justify-between space-y-4">
            <div class="bg-white rounded-2xl p-5 shadow-2xs border border-slate-200/80 space-y-4">
                <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2 border-b border-slate-100 pb-3">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    Ảnh các góc chụp đã ghi nhận
                </h3>

                <!-- 3 Photo Slots -->
                <div class="space-y-3">
                    <!-- Angle 1: Frontal -->
                    <div id="slotFrontal" class="flex items-center justify-between p-3 rounded-xl border-2 border-dashed border-slate-200 hover:border-blue-300 transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-lg bg-slate-100 overflow-hidden shrink-0 flex items-center justify-center border border-slate-200">
                                <img id="previewFrontal" class="w-full h-full object-cover hidden" alt="Chính diện">
                                <svg id="emptyFrontal" class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-900">1. Góc chính diện (Thẳng)</p>
                                <span id="statusBadgeFrontal" class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-600 mt-0.5">Chưa chụp</span>
                            </div>
                        </div>
                        <button type="button" onclick="selectStep(1)" class="text-xs font-semibold text-blue-600 hover:text-blue-800 px-2 py-1">Chụp lại</button>
                    </div>

                    <!-- Angle 2: Left -->
                    <div id="slotLeft" class="flex items-center justify-between p-3 rounded-xl border-2 border-dashed border-slate-200 hover:border-blue-300 transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-lg bg-slate-100 overflow-hidden shrink-0 flex items-center justify-center border border-slate-200">
                                <img id="previewLeft" class="w-full h-full object-cover hidden" alt="Nghiêng trái">
                                <svg id="emptyLeft" class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-900">2. Góc nghiêng trái (~20°)</p>
                                <span id="statusBadgeLeft" class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-600 mt-0.5">Chưa chụp</span>
                            </div>
                        </div>
                        <button type="button" onclick="selectStep(2)" class="text-xs font-semibold text-blue-600 hover:text-blue-800 px-2 py-1">Chụp lại</button>
                    </div>

                    <!-- Angle 3: Right -->
                    <div id="slotRight" class="flex items-center justify-between p-3 rounded-xl border-2 border-dashed border-slate-200 hover:border-blue-300 transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-lg bg-slate-100 overflow-hidden shrink-0 flex items-center justify-center border border-slate-200">
                                <img id="previewRight" class="w-full h-full object-cover hidden" alt="Nghiêng phải">
                                <svg id="emptyRight" class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-900">3. Góc nghiêng phải (~20°)</p>
                                <span id="statusBadgeRight" class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-600 mt-0.5">Chưa chụp</span>
                            </div>
                        </div>
                        <button type="button" onclick="selectStep(3)" class="text-xs font-semibold text-blue-600 hover:text-blue-800 px-2 py-1">Chụp lại</button>
                    </div>
                </div>

                <!-- Guidance Tips -->
                <div class="bg-slate-50 rounded-xl p-3 text-xs text-slate-500 space-y-1 border border-slate-100">
                    <p class="font-bold text-slate-700 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Mẹo chụp ảnh đạt chuẩn:
                    </p>
                    <ul class="list-disc list-inside space-y-0.5 text-[11px] text-slate-600 pl-1">
                        <li>Ngồi trong phòng đủ ánh sáng, tránh ngược sáng.</li>
                        <li>Tháo khẩu trang hoặc kính râm trước khi chụp.</li>
                        <li>Giữ yên đầu trong 1 giây khi vòng tròn chuyển xanh.</li>
                    </ul>
                </div>
            </div>

            <!-- Final Submit Action Button -->
            <button type="button" id="submitRegisterBtn" disabled onclick="submitRegistration()" class="w-full py-3.5 bg-blue-600 text-white rounded-xl font-bold text-xs shadow-2xs transition-all hover:bg-blue-700 disabled:opacity-40 disabled:pointer-events-none disabled:shadow-none flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                <span>Hoàn tất Lưu Hồ Sơ Khuôn Mặt</span>
            </button>
        </div>
    </div>
</div>

<!-- Hidden Canvas for high-res snapshot capture -->
<canvas id="snapshotCanvas" class="hidden"></canvas>

<!-- ====================================================
     REGISTRATION SUCCESS IN-PAGE POPUP MODAL
     ==================================================== -->
<div id="registerSuccessModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4">
    <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-md transition-opacity"></div>
    <div class="relative bg-white rounded-3xl shadow-2xl border border-slate-100 max-w-md w-full p-6 sm:p-8 text-center space-y-5 z-10">
        <div class="w-16 h-16 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto shadow-lg shadow-emerald-100">
            <svg class="w-9 h-9" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
        </div>
        
        <div class="space-y-1.5">
            <h3 class="text-xl font-black text-slate-900">Đăng ký Face ID thành công!</h3>
            <p class="text-xs text-slate-500 leading-relaxed">
                Hồ sơ nhận diện khuôn mặt của bạn đã được cập nhật an toàn vào hệ thống. Bạn đã sẵn sàng tham gia các kỳ thi trực tuyến.
            </p>
        </div>

        <div class="bg-emerald-50 rounded-2xl p-3 text-xs font-semibold text-emerald-800 border border-emerald-200/60">
            Tự động chuyển hướng về trang chủ trong <span id="countdownRedirect" class="font-bold text-emerald-700">3</span>s...
        </div>

        <button type="button" onclick="goToDashboard()" class="w-full py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-2xl shadow-lg shadow-indigo-200 transition-all active:scale-95 flex items-center justify-center gap-2">
            <span>Vào Bảng điều khiển ngay</span>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
        </button>
    </div>
</div>

<!-- ====================================================
     REGISTRATION ERROR IN-PAGE POPUP MODAL
     ==================================================== -->
<div id="registerErrorModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4">
    <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-md transition-opacity" onclick="closeErrorModal()"></div>
    <div class="relative bg-white rounded-3xl shadow-2xl border border-slate-100 max-w-md w-full p-6 text-center space-y-4 z-10">
        <div class="w-14 h-14 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center mx-auto shadow-md shadow-rose-100">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
        </div>
        <div class="space-y-1">
            <h3 class="text-base font-bold text-slate-900">Không thể hoàn tất đăng ký</h3>
            <p id="registerErrorMsg" class="text-xs text-slate-500 leading-relaxed"></p>
        </div>
        <button type="button" onclick="closeErrorModal()" class="w-full py-3 bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs rounded-xl transition-all">
            Đã hiểu và thử lại
        </button>
    </div>
</div>

<!-- MediaPipe Face Mesh -->
<script src="{{ asset('vendor/mediapipe/face_mesh.js') }}"></script>

<script>
    let currentStep = 1; // 1: Frontal, 2: Left, 3: Right
    let capturedData = {
        frontal: null,
        left: null,
        right: null
    };

    const videoEl = document.getElementById('webcamVideo');
    const overlayEl = document.getElementById('faceOverlay');
    const canvasEl = document.getElementById('snapshotCanvas');
    const guideBox = document.getElementById('faceGuideBox');
    const guideArrow = document.getElementById('guideArrow');
    const guideHintText = document.getElementById('guideHintText');
    const progressCircle = document.getElementById('progressCircle');
    const instructionText = document.getElementById('instructionText');
    const aiBadge = document.getElementById('aiBadge');
    const poseBadge = document.getElementById('poseBadge');

    let faceMeshInstance = null;
    let isFaceMeshRunning = false;
    let isProcessingFrame = false;
    let isCapturingCooldown = false;
    let steadyStartTime = null;
    const HOLD_DURATION_MS = 750; // Giữ đúng hướng trong 750ms để tự động chụp
    const TOTAL_CIRCLE_DASH = 289;

    // Web Audio chime
    function playBeep(freq = 600, duration = 0.12) {
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.type = 'sine';
            osc.frequency.setValueAtTime(freq, ctx.currentTime);
            gain.gain.setValueAtTime(0.12, ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + duration);
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.start();
            osc.stop(ctx.currentTime + duration);
        } catch(e) {}
    }

    function updateProgressRing(percent) {
        if (!progressCircle) return;
        const offset = TOTAL_CIRCLE_DASH - (percent * TOTAL_CIRCLE_DASH);
        progressCircle.style.strokeDashoffset = offset;
    }

    async function init() {
        console.log('[Face Register] Initializing Camera & MediaPipe Face Mesh...');
        updateStepUI();
        await startCamera();
        isFaceMeshRunning = true;
        scheduleNextFrame();
        initFaceMesh();
    }

    async function startCamera() {
        document.getElementById('cameraOfflineMsg').classList.add('hidden');
        try {
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                throw new Error('Webcam không được hỗ trợ hoặc kết nối không an toàn (cần HTTPS/localhost).');
            }

            const stream = await navigator.mediaDevices.getUserMedia({
                video: {
                    width: { ideal: 640 },
                    height: { ideal: 480 },
                    facingMode: 'user'
                },
                audio: false
            });

            videoEl.srcObject = stream;

            videoEl.onloadedmetadata = () => {
                videoEl.play().catch(e => console.log('Autoplay handled:', e));
                overlayEl.width = videoEl.videoWidth || 640;
                overlayEl.height = videoEl.videoHeight || 480;
            };

            try {
                await videoEl.play();
            } catch (playErr) {
                console.log('Video autoplay handled:', playErr);
            }

            overlayEl.width = videoEl.videoWidth || 640;
            overlayEl.height = videoEl.videoHeight || 480;
            updateStepUI();
        } catch (err) {
            console.error('Camera error:', err);
            document.getElementById('cameraOfflineMsg').classList.remove('hidden');
            instructionText.innerText = 'Không thể mở camera. Vui lòng cấp quyền truy cập webcam trên trình duyệt!';
        }
    }

    async function initFaceMesh() {
        if (typeof FaceMesh === 'undefined') {
            console.warn('[Face Register] MediaPipe FaceMesh đang tải, thử lại sau 300ms...');
            setTimeout(initFaceMesh, 300);
            return;
        }

        try {
            faceMeshInstance = new FaceMesh({
                locateFile: (file) => `{{ asset('vendor/mediapipe') }}/${file}`
            });

            faceMeshInstance.setOptions({
                maxNumFaces: 2,
                refineLandmarks: true,
                minDetectionConfidence: 0.5,
                minTrackingConfidence: 0.5
            });

            faceMeshInstance.onResults(onFaceMeshResults);
            await faceMeshInstance.initialize();
            console.log('[Face Register] MediaPipe Face Mesh đã sẵn sàng!');

            aiBadge.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800';
            aiBadge.innerText = 'AI Tự động bắt hướng';
            updateStepUI();
        } catch (err) {
            console.error('[Face Register] Lỗi khởi tạo Face Mesh:', err);
            aiBadge.className = 'inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800';
            aiBadge.innerText = 'Chụp thủ công';
            poseBadge.innerText = 'Bấm nút chụp bên dưới';
        }
    }

    function scheduleNextFrame() {
        if (isFaceMeshRunning) {
            requestAnimationFrame(processFrame);
        }
    }

    async function processFrame() {
        if (!isFaceMeshRunning) return;

        try {
            if (videoEl && !videoEl.paused && !videoEl.ended && videoEl.readyState >= 2 && videoEl.videoWidth > 0 && videoEl.videoHeight > 0) {
                if (faceMeshInstance && !isProcessingFrame) {
                    isProcessingFrame = true;
                    faceMeshInstance.send({ image: videoEl })
                        .catch(() => {})
                        .finally(() => {
                            isProcessingFrame = false;
                        });
                }
            }
        } catch (e) {
            console.warn('[Face Register] Frame process warning:', e);
        } finally {
            scheduleNextFrame();
        }
    }

    function drawFacialKeypoints(ctx, lm, width, height, isCorrect) {
        ctx.save();
        ctx.fillStyle = isCorrect ? 'rgba(16, 185, 129, 0.85)' : 'rgba(56, 189, 248, 0.6)';
        ctx.shadowColor = isCorrect ? '#10b981' : '#38bdf8';
        ctx.shadowBlur = 4;

        // Key landmarks: nose tip, eyes, mouth corners, chin
        const keyIndices = [1, 33, 263, 61, 291, 199, 10, 152];
        for (const idx of keyIndices) {
            if (lm[idx]) {
                const x = lm[idx].x * width;
                const y = lm[idx].y * height;
                ctx.beginPath();
                ctx.arc(x, y, 2.5, 0, 2 * Math.PI);
                ctx.fill();
            }
        }
        ctx.restore();
    }

    function onFaceMeshResults(results) {
        if (overlayEl.width !== videoEl.videoWidth || overlayEl.height !== videoEl.videoHeight) {
            overlayEl.width = videoEl.videoWidth || 640;
            overlayEl.height = videoEl.videoHeight || 480;
        }
        const ctx = overlayEl.getContext('2d');
        ctx.clearRect(0, 0, overlayEl.width, overlayEl.height);

        // 1. Đã hoàn tất cả 3 góc
        if (capturedData.frontal && capturedData.left && capturedData.right) {
            guideBox.className = 'absolute inset-0 m-auto w-56 h-72 border-2 border-solid border-emerald-500 rounded-[50%] shadow-[0_0_25px_rgba(16,185,129,0.7)] pointer-events-none transition-all duration-300';
            poseBadge.className = 'text-emerald-600 font-bold';
            poseBadge.innerText = 'Đã hoàn tất đủ 3 góc!';
            updateProgressRing(1.0);
            return;
        }

        // 2. Cooldown nghỉ giữa các góc chụp
        if (isCapturingCooldown) {
            steadyStartTime = null;
            updateProgressRing(0);
            return;
        }

        // 3. Góc hiện tại đã có ảnh
        const currentSlotKey = currentStep === 1 ? 'frontal' : (currentStep === 2 ? 'left' : 'right');
        if (capturedData[currentSlotKey]) {
            steadyStartTime = null;
            updateProgressRing(0);
            poseBadge.className = 'text-emerald-600 font-bold';
            poseBadge.innerText = 'Đã chụp góc này';
            return;
        }

        const faceCount = results.multiFaceLandmarks ? results.multiFaceLandmarks.length : 0;
        if (faceCount === 0) {
            guideBox.className = 'absolute inset-0 m-auto w-56 h-72 border-2 border-dashed border-white/40 rounded-[50%] pointer-events-none transition-all duration-300';
            poseBadge.className = 'text-slate-400 font-medium';
            poseBadge.innerText = 'Chưa phát hiện khuôn mặt';
            steadyStartTime = null;
            updateProgressRing(0);
            return;
        }

        if (faceCount > 1) {
            guideBox.className = 'absolute inset-0 m-auto w-56 h-72 border-2 border-dashed border-rose-500 rounded-[50%] pointer-events-none transition-all duration-300';
            poseBadge.className = 'text-rose-600 font-bold';
            poseBadge.innerText = 'Phát hiện > 1 người trước camera';
            steadyStartTime = null;
            updateProgressRing(0);
            return;
        }

        const lm = results.multiFaceLandmarks[0];

        // Bounding box & Center check
        let minX = 1.0, maxX = 0.0, minY = 1.0, maxY = 0.0;
        for (let i = 0; i < lm.length; i++) {
            const p = lm[i];
            if (p.x < minX) minX = p.x;
            if (p.x > maxX) maxX = p.x;
            if (p.y < minY) minY = p.y;
            if (p.y > maxY) maxY = p.y;
        }
        const faceHeightRatio = maxY - minY;
        const centerX = (minX + maxX) / 2;
        const centerY = (minY + maxY) / 2;

        if (faceHeightRatio < 0.20) {
            guideBox.className = 'absolute inset-0 m-auto w-56 h-72 border-2 border-dashed border-amber-400 rounded-[50%] pointer-events-none transition-all duration-200';
            poseBadge.className = 'text-amber-600 font-bold';
            poseBadge.innerText = 'Đưa mặt lại gần camera hơn';
            steadyStartTime = null;
            updateProgressRing(0);
            return;
        }
        if (faceHeightRatio > 0.85) {
            guideBox.className = 'absolute inset-0 m-auto w-56 h-72 border-2 border-dashed border-amber-400 rounded-[50%] pointer-events-none transition-all duration-200';
            poseBadge.className = 'text-amber-600 font-bold';
            poseBadge.innerText = 'Lùi mặt xa camera một chút';
            steadyStartTime = null;
            updateProgressRing(0);
            return;
        }

        if (centerX < 0.18 || centerX > 0.82 || centerY < 0.15 || centerY > 0.85) {
            guideBox.className = 'absolute inset-0 m-auto w-56 h-72 border-2 border-dashed border-amber-400 rounded-[50%] pointer-events-none transition-all duration-200';
            poseBadge.className = 'text-amber-600 font-bold';
            poseBadge.innerText = 'Căn mặt vào giữa khung oval';
            steadyStartTime = null;
            updateProgressRing(0);
            return;
        }

        // 3D Head Pose calculation:
        // Landmark 1: Đầu mũi (Nose tip)
        // Landmark 33: Góc ngoài mắt trái (Left eye outer corner)
        // Landmark 263: Góc ngoài mắt phải (Right eye outer corner)
        const eyeMidX = (lm[33].x + lm[263].x) / 2;
        const eyeDist = Math.hypot(lm[263].x - lm[33].x, lm[263].y - lm[33].y) || 0.001;
        const rawYaw = (lm[1].x - eyeMidX) / eyeDist;

        const noseY = lm[1].y;
        const foreheadY = lm[10].y;
        const chinY = lm[152].y;
        const upperDist = Math.max(0.001, Math.abs(noseY - foreheadY));
        const lowerDist = Math.max(0.001, Math.abs(chinY - noseY));
        const pitchRatio = upperDist / lowerDist;

        if (pitchRatio > 1.85) {
            guideBox.className = 'absolute inset-0 m-auto w-56 h-72 border-2 border-dashed border-amber-400 rounded-[50%] pointer-events-none transition-all duration-200';
            poseBadge.className = 'text-amber-600 font-bold';
            poseBadge.innerText = 'Ngẩng đầu lên ngang tầm camera';
            steadyStartTime = null;
            updateProgressRing(0);
            return;
        }
        if (pitchRatio < 0.50) {
            guideBox.className = 'absolute inset-0 m-auto w-56 h-72 border-2 border-dashed border-amber-400 rounded-[50%] pointer-events-none transition-all duration-200';
            poseBadge.className = 'text-amber-600 font-bold';
            poseBadge.innerText = 'Hạ cằm xuống ngang tầm camera';
            steadyStartTime = null;
            updateProgressRing(0);
            return;
        }

        // Head pose determination:
        // Video có transform: -scale-x-100 (gương selfie)
        // Khi người dùng nghiêng sang TRÁI (hướng vai trái của bạn):
        // Mũi di chuyển sang phía X lớn hơn trong raw frame -> rawYaw > 0.
        // Khi người dùng nghiêng sang PHẢI (hướng vai phải của bạn):
        // Mũi di chuyển sang phía X nhỏ hơn trong raw frame -> rawYaw < 0.
        let detectedPose = 'transition';
        let poseText = 'Đang chỉnh góc';

        if (rawYaw >= 0.11) {
            detectedPose = 'left';
            poseText = 'Đang nghiêng TRÁI';
        } else if (rawYaw <= -0.11) {
            detectedPose = 'right';
            poseText = 'Đang nghiêng PHẢI';
        } else if (Math.abs(rawYaw) <= 0.08) {
            detectedPose = 'frontal';
            poseText = 'Đang nhìn THẲNG';
        }

        let isCorrectAngle = false;
        let targetName = '';

        if (currentStep === 1) {
            targetName = 'Nhìn thẳng';
            if (detectedPose === 'frontal') isCorrectAngle = true;
        } else if (currentStep === 2) {
            targetName = 'Nghiêng trái';
            if (detectedPose === 'left') isCorrectAngle = true;
        } else if (currentStep === 3) {
            targetName = 'Nghiêng phải';
            if (detectedPose === 'right') isCorrectAngle = true;
        }

        drawFacialKeypoints(ctx, lm, overlayEl.width, overlayEl.height, isCorrectAngle);

        if (isCorrectAngle) {
            guideBox.className = 'absolute inset-0 m-auto w-56 h-72 border-2 border-solid border-emerald-400 rounded-[50%] shadow-[0_0_24px_rgba(16,185,129,0.7)] pointer-events-none transition-all duration-200';
            poseBadge.className = 'text-emerald-600 font-bold';
            poseBadge.innerText = `✓ Đúng góc: ${targetName} (Giữ yên...)`;

            const now = Date.now();
            if (steadyStartTime === null) {
                steadyStartTime = now;
            }
            const elapsed = now - steadyStartTime;
            const progress = Math.min(1.0, elapsed / HOLD_DURATION_MS);
            updateProgressRing(progress);

            if (progress >= 1.0) {
                captureCurrentAngle();
                steadyStartTime = null;
                updateProgressRing(0);
            }
        } else {
            guideBox.className = 'absolute inset-0 m-auto w-56 h-72 border-2 border-dashed border-amber-400 rounded-[50%] pointer-events-none transition-all duration-200';
            poseBadge.className = 'text-amber-700 font-semibold';
            poseBadge.innerText = `${poseText} • Yêu cầu: ${targetName}`;
            steadyStartTime = null;
            updateProgressRing(0);
        }
    }

    function updateStepUI() {
        const steps = [
            { id: 1, name: 'Nhìn thẳng', desc: 'Góc 1/3: Giữ khuôn mặt ở giữa khung oval và nhìn thẳng vào camera', hint: '1. Nhìn thẳng camera' },
            { id: 2, name: 'Nghiêng trái', desc: 'Góc 2/3: Nghiêng nhẹ mặt sang bên TRÁI (khoảng 15° - 20°)', hint: '2. Nghiêng sang TRÁI (←)' },
            { id: 3, name: 'Nghiêng phải', desc: 'Góc 3/3: Nghiêng nhẹ mặt sang bên PHẢI (khoảng 15° - 20°)', hint: '3. Nghiêng sang PHẢI (→)' },
        ];

        steps.forEach(s => {
            const el = document.getElementById(`stepIndicator${s.id}`);
            const badge = el.querySelector('div:first-child');
            if (s.id === currentStep) {
                el.className = 'flex items-center gap-3 p-3 rounded-xl bg-blue-50 border-2 border-blue-600 shadow-sm transition-all';
                badge.className = 'w-8 h-8 rounded-lg bg-blue-600 text-white flex items-center justify-center font-bold text-xs shrink-0';
            } else if (capturedData[s.id === 1 ? 'frontal' : (s.id === 2 ? 'left' : 'right')]) {
                el.className = 'flex items-center gap-3 p-3 rounded-xl bg-emerald-50 border border-emerald-300 transition-all';
                badge.className = 'w-8 h-8 rounded-lg bg-emerald-600 text-white flex items-center justify-center font-bold text-xs shrink-0';
            } else {
                el.className = 'flex items-center gap-3 p-3 rounded-xl bg-slate-50 border border-slate-200/80 transition-all opacity-60';
                badge.className = 'w-8 h-8 rounded-lg bg-slate-200 text-slate-600 flex items-center justify-center font-bold text-xs shrink-0';
            }
        });

        if (capturedData.frontal && capturedData.left && capturedData.right) {
            instructionText.innerText = '✓ Đã chụp đủ 3 góc khuôn mặt. Bấm nút bên dưới để hoàn tất lưu hồ sơ.';
            guideArrow.innerHTML = `<svg class="w-8 h-8 text-emerald-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>`;
            guideHintText.innerText = 'Đã hoàn thành 3 góc';
        } else {
            const cur = steps[currentStep - 1];
            instructionText.innerText = cur.desc;
            guideHintText.innerText = cur.hint;

            if (currentStep === 1) {
                guideArrow.innerHTML = `<svg class="w-7 h-7 text-white/90 mx-auto animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>`;
            } else if (currentStep === 2) {
                guideArrow.innerHTML = `<svg class="w-8 h-8 text-emerald-300 mx-auto animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>`;
            } else if (currentStep === 3) {
                guideArrow.innerHTML = `<svg class="w-8 h-8 text-emerald-300 mx-auto animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>`;
            }
        }

        steadyStartTime = null;
        updateProgressRing(0);
    }

    function captureCurrentAngle() {
        canvasEl.width = videoEl.videoWidth || 640;
        canvasEl.height = videoEl.videoHeight || 480;
        const ctx = canvasEl.getContext('2d');
        ctx.drawImage(videoEl, 0, 0, canvasEl.width, canvasEl.height);
        const dataUrl = canvasEl.toDataURL('image/jpeg', 0.92);

        playBeep(880, 0.12);

        const flashEl = document.getElementById('cameraFlash');
        if (flashEl) {
            flashEl.classList.remove('opacity-0');
            flashEl.classList.add('opacity-80');
            setTimeout(() => {
                flashEl.classList.remove('opacity-80');
                flashEl.classList.add('opacity-0');
            }, 180);
        }

        isCapturingCooldown = true;
        steadyStartTime = null;
        updateProgressRing(0);

        if (currentStep === 1) {
            capturedData.frontal = dataUrl;
            document.getElementById('previewFrontal').src = dataUrl;
            document.getElementById('previewFrontal').classList.remove('hidden');
            document.getElementById('emptyFrontal').classList.add('hidden');
            document.getElementById('statusBadgeFrontal').className = 'inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 mt-1';
            document.getElementById('statusBadgeFrontal').innerText = '✓ Đã chụp';

            currentStep = 2;
            updateStepUI();
            instructionText.innerText = '✓ Đã chụp góc nhìn thẳng! Chuẩn bị góc 2: Hãy nghiêng nhẹ mặt sang bên TRÁI...';
        } else if (currentStep === 2) {
            capturedData.left = dataUrl;
            document.getElementById('previewLeft').src = dataUrl;
            document.getElementById('previewLeft').classList.remove('hidden');
            document.getElementById('emptyLeft').classList.add('hidden');
            document.getElementById('statusBadgeLeft').className = 'inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 mt-1';
            document.getElementById('statusBadgeLeft').innerText = '✓ Đã chụp';

            currentStep = 3;
            updateStepUI();
            instructionText.innerText = '✓ Đã chụp góc nghiêng trái! Chuẩn bị góc 3: Hãy nghiêng nhẹ mặt sang bên PHẢI...';
        } else if (currentStep === 3) {
            capturedData.right = dataUrl;
            document.getElementById('previewRight').src = dataUrl;
            document.getElementById('previewRight').classList.remove('hidden');
            document.getElementById('emptyRight').classList.add('hidden');
            document.getElementById('statusBadgeRight').className = 'inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 mt-1';
            document.getElementById('statusBadgeRight').innerText = '✓ Đã chụp';

            updateStepUI();
            instructionText.innerText = '✓ Tuyệt vời! Đã chụp đủ 3 góc khuôn mặt. Bấm nút bên dưới để hoàn tất lưu hồ sơ.';
            setTimeout(() => playBeep(1046, 0.2), 150);
        }

        checkCompletion();

        setTimeout(() => {
            isCapturingCooldown = false;
            updateStepUI();
        }, 1200);
    }

    function manualCaptureAngle() {
        captureCurrentAngle();
    }

    function selectStep(stepNum) {
        currentStep = stepNum;
        
        if (stepNum === 1) capturedData.frontal = null;
        if (stepNum === 2) capturedData.left = null;
        if (stepNum === 3) capturedData.right = null;

        const key = stepNum === 1 ? 'Frontal' : (stepNum === 2 ? 'Left' : 'Right');
        document.getElementById('preview' + key).classList.add('hidden');
        document.getElementById('empty' + key).classList.remove('hidden');
        document.getElementById('statusBadge' + key).className = 'inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-600 mt-1';
        document.getElementById('statusBadge' + key).innerText = 'Chưa chụp';

        isCapturingCooldown = false;
        steadyStartTime = null;
        updateProgressRing(0);
        updateStepUI();
        checkCompletion();
    }

    function checkCompletion() {
        const isComplete = Boolean(capturedData.frontal && capturedData.left && capturedData.right);
        const submitBtn = document.getElementById('submitRegisterBtn');
        submitBtn.disabled = !isComplete;
        if (isComplete) {
            submitBtn.classList.remove('opacity-40', 'pointer-events-none');
            submitBtn.classList.add('animate-pulse', 'ring-4', 'ring-blue-400/40');
            setTimeout(() => submitBtn.classList.remove('animate-pulse'), 2000);
        } else {
            submitBtn.classList.add('opacity-40', 'pointer-events-none');
            submitBtn.classList.remove('ring-4', 'ring-blue-400/40');
        }
    }

    let nextDashboardUrl = '{{ route("student.dashboard") }}';
    let redirectCountdownTimer = null;

    function showRegisterSuccessModal(targetUrl) {
        nextDashboardUrl = targetUrl || '{{ route("student.dashboard") }}';
        document.getElementById('registerSuccessModal').classList.remove('hidden');
        
        let timeLeft = 3;
        const countEl = document.getElementById('countdownRedirect');
        redirectCountdownTimer = setInterval(() => {
            timeLeft--;
            if (countEl) countEl.innerText = timeLeft;
            if (timeLeft <= 0) {
                clearInterval(redirectCountdownTimer);
                goToDashboard();
            }
        }, 1000);
    }

    function goToDashboard() {
        if (redirectCountdownTimer) clearInterval(redirectCountdownTimer);
        window.location.href = nextDashboardUrl;
    }

    function showRegisterErrorModal(message) {
        document.getElementById('registerErrorMsg').innerText = message || 'Không thể đăng ký khuôn mặt. Vui lòng thử lại.';
        document.getElementById('registerErrorModal').classList.remove('hidden');
    }

    function closeErrorModal() {
        document.getElementById('registerErrorModal').classList.add('hidden');
    }

    async function submitRegistration() {
        const submitBtn = document.getElementById('submitRegisterBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = `
            <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            <span>Đang trích xuất ArcFace Embedding và lưu hồ sơ...</span>
        `;

        try {
            const response = await fetch('{{ route("student.face.register.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(capturedData)
            });

            const data = await response.json();

            if (data.success) {
                showRegisterSuccessModal(data.redirect_url);
            } else {
                showRegisterErrorModal(data.message);
                submitBtn.disabled = false;
                submitBtn.innerHTML = `<span>Thử lưu lại</span>`;
            }
        } catch (error) {
            console.error('Submit error:', error);
            showRegisterErrorModal('Lỗi kết nối máy chủ xác thực. Vui lòng thử lại!');
            submitBtn.disabled = false;
            submitBtn.innerHTML = `<span>Thử lưu lại</span>`;
        }
    }

    window.addEventListener('DOMContentLoaded', init);
    if (document.readyState === 'complete' || document.readyState === 'interactive') {
        setTimeout(init, 50);
    }
</script>
@endsection
