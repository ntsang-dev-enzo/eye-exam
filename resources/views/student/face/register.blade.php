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
    <div class="bg-gradient-to-r from-indigo-600 via-blue-600 to-indigo-800 rounded-3xl p-6 sm:p-8 text-white shadow-xl shadow-indigo-100 relative overflow-hidden">
        <div class="absolute -right-8 -bottom-8 w-48 h-48 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-white/15 backdrop-blur-md rounded-full text-xs font-semibold mb-3 border border-white/20">
                    <svg class="w-4 h-4 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    Bảo mật danh tính & Chống gian lận
                </div>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight">Đăng ký Nhận diện Khuôn mặt (Face ID)</h1>
                <p class="text-indigo-100 text-sm mt-1 max-w-xl">
                    Hệ thống cần thu thập 3 góc nhìn khuôn mặt của bạn để tạo hồ sơ xác thực danh tính bằng AI (ArcFace). Dữ liệu này dùng để xác minh bạn trước mỗi kỳ thi.
                </p>
            </div>
            @if($user->face_registered)
                <div class="shrink-0 bg-white/20 backdrop-blur-md border border-white/30 px-4 py-2 rounded-2xl text-center">
                    <span class="inline-flex items-center gap-1.5 text-emerald-300 text-xs font-bold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Đã đăng ký Face ID
                    </span>
                    <p class="text-[11px] text-indigo-100 mt-0.5">Bạn có thể chụp lại để cập nhật</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Steps Progress Bar -->
    <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-sm border border-gray-100">
        <div class="grid grid-cols-3 gap-3">
            <div id="stepIndicator1" class="flex items-center gap-3 p-2.5 rounded-xl bg-indigo-50 border border-indigo-200 transition-all">
                <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white flex items-center justify-center font-bold text-sm shrink-0">
                    1
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-bold text-indigo-900 truncate">Nhìn thẳng</p>
                    <p class="text-[11px] text-indigo-600 truncate">Góc chính diện</p>
                </div>
            </div>

            <div id="stepIndicator2" class="flex items-center gap-3 p-2.5 rounded-xl bg-gray-50 border border-gray-100 transition-all">
                <div class="w-8 h-8 rounded-lg bg-gray-200 text-gray-600 flex items-center justify-center font-bold text-sm shrink-0">
                    2
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-bold text-gray-700 truncate">Nghiêng trái</p>
                    <p class="text-[11px] text-gray-400 truncate">Khoảng 15° - 20°</p>
                </div>
            </div>

            <div id="stepIndicator3" class="flex items-center gap-3 p-2.5 rounded-xl bg-gray-50 border border-gray-100 transition-all">
                <div class="w-8 h-8 rounded-lg bg-gray-200 text-gray-600 flex items-center justify-center font-bold text-sm shrink-0">
                    3
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-bold text-gray-700 truncate">Nghiêng phải</p>
                    <p class="text-[11px] text-gray-400 truncate">Khoảng 15° - 20°</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Registration Workspace -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Left: Webcam Viewport (7 cols) -->
        <div class="lg:col-span-7 bg-white rounded-3xl p-5 sm:p-6 shadow-sm border border-gray-100 flex flex-col items-center">
            
            <!-- Instructions banner -->
            <div id="statusInstruction" class="w-full bg-blue-50 border border-blue-200 text-blue-800 text-xs sm:text-sm font-semibold px-4 py-2.5 rounded-2xl mb-4 text-center flex items-center justify-center gap-2">
                <span class="relative flex h-2.5 w-2.5">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-blue-600"></span>
                </span>
                <span id="instructionText">Đang khởi tạo camera và tải mô hình AI...</span>
            </div>

            <!-- Video Viewport Container -->
            <div class="relative w-full aspect-[4/3] max-w-md bg-slate-900 rounded-3xl overflow-hidden shadow-inner flex items-center justify-center border-4 border-slate-800">
                <video id="webcamVideo" autoplay playsinline muted class="w-full h-full object-cover transform -scale-x-100"></video>
                <canvas id="faceOverlay" class="absolute inset-0 w-full h-full pointer-events-none transform -scale-x-100"></canvas>

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
                    <div id="guideIconContainer" class="text-white/70 text-center space-y-1">
                        <div id="guideArrow" class="hidden">
                        </div>
                    </div>
                </div>

                <!-- Camera offline overlay -->
                <div id="cameraOfflineMsg" class="hidden absolute inset-0 bg-slate-900/90 text-white flex flex-col items-center justify-center p-6 text-center">
                    <svg class="w-12 h-12 text-rose-500 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18"></path></svg>
                    <p class="font-bold text-sm">Không thể kết nối camera</p>
                    <p class="text-xs text-slate-400 mt-1">Vui lòng cấp quyền truy cập webcam trong trình duyệt để tiếp tục.</p>
                    <button type="button" onclick="startCamera()" class="mt-4 px-4 py-2 bg-indigo-600 text-white rounded-xl text-xs font-semibold hover:bg-indigo-700">Thử lại</button>
                </div>
            </div>

            <!-- Viewport Controls & Live Status -->
            <div class="mt-5 flex items-center justify-between w-full max-w-md gap-3">
                <div class="flex items-center gap-2 text-xs">
                    <span id="aiBadge" class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                        Đang tải AI...
                    </span>
                    <span id="poseBadge" class="text-slate-600 font-semibold">Chưa phát hiện mặt</span>
                </div>

                <div class="text-[11px] text-slate-400 font-medium flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>Hỗ trợ đeo / tháo kính</span>
                </div>
            </div>
        </div>

        <!-- Right: Captured Angles Gallery & Final Submission (5 cols) -->
        <div class="lg:col-span-5 flex flex-col justify-between space-y-4">
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 space-y-4">
                <h3 class="font-bold text-gray-900 text-base flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    Ảnh các góc chụp đã ghi nhận
                </h3>

                <!-- 3 Photo Slots -->
                <div class="space-y-3">
                    <!-- Angle 1: Frontal -->
                    <div id="slotFrontal" class="flex items-center justify-between p-3 rounded-2xl border-2 border-dashed border-gray-200 hover:border-indigo-300 transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="w-14 h-14 rounded-xl bg-gray-100 overflow-hidden shrink-0 flex items-center justify-center border border-gray-200">
                                <img id="previewFrontal" class="w-full h-full object-cover hidden" alt="Chính diện">
                                <svg id="emptyFrontal" class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-900">1. Góc chính diện (Thẳng)</p>
                                <span id="statusBadgeFrontal" class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-gray-100 text-gray-600 mt-1">Chưa chụp</span>
                            </div>
                        </div>
                        <button type="button" onclick="selectStep(1)" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 px-2 py-1">Chụp lại</button>
                    </div>

                    <!-- Angle 2: Left -->
                    <div id="slotLeft" class="flex items-center justify-between p-3 rounded-2xl border-2 border-dashed border-gray-200 hover:border-indigo-300 transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="w-14 h-14 rounded-xl bg-gray-100 overflow-hidden shrink-0 flex items-center justify-center border border-gray-200">
                                <img id="previewLeft" class="w-full h-full object-cover hidden" alt="Nghiêng trái">
                                <svg id="emptyLeft" class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-900">2. Góc nghiêng trái (~20°)</p>
                                <span id="statusBadgeLeft" class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-gray-100 text-gray-600 mt-1">Chưa chụp</span>
                            </div>
                        </div>
                        <button type="button" onclick="selectStep(2)" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 px-2 py-1">Chụp lại</button>
                    </div>

                    <!-- Angle 3: Right -->
                    <div id="slotRight" class="flex items-center justify-between p-3 rounded-2xl border-2 border-dashed border-gray-200 hover:border-indigo-300 transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="w-14 h-14 rounded-xl bg-gray-100 overflow-hidden shrink-0 flex items-center justify-center border border-gray-200">
                                <img id="previewRight" class="w-full h-full object-cover hidden" alt="Nghiêng phải">
                                <svg id="emptyRight" class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-900">3. Góc nghiêng phải (~20°)</p>
                                <span id="statusBadgeRight" class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-gray-100 text-gray-600 mt-1">Chưa chụp</span>
                            </div>
                        </div>
                        <button type="button" onclick="selectStep(3)" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 px-2 py-1">Chụp lại</button>
                    </div>
                </div>

                <!-- Guidance Tips -->
                <div class="bg-gray-50 rounded-2xl p-3.5 text-xs text-gray-500 space-y-1.5 border border-gray-100">
                    <p class="font-bold text-gray-700 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Mẹo chụp ảnh đạt chuẩn:
                    </p>
                    <ul class="list-disc list-inside space-y-1 text-[11px] text-gray-600 pl-1">
                        <li>Ngồi trong phòng đủ ánh sáng, tránh ngược sáng.</li>
                        <li>Tháo khẩu trang hoặc kính râm trước khi chụp.</li>
                        <li>Giữ yên đầu trong 1 giây khi vòng tròn chuyển xanh.</li>
                    </ul>
                </div>
            </div>

            <!-- Final Submit Action Button -->
            <button type="button" id="submitRegisterBtn" disabled onclick="submitRegistration()" class="w-full py-4 bg-indigo-600 text-white rounded-2xl font-bold text-sm shadow-lg shadow-indigo-200 transition-all hover:bg-indigo-700 disabled:opacity-40 disabled:pointer-events-none disabled:shadow-none flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
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

<!-- Load local face-api.min.js directly -->
<script src="{{ asset('vendor/face-api/face-api.min.js') }}"></script>

<script>
    let currentStep = 1; // 1: Frontal, 2: Left, 3: Right
    let capturedData = {
        frontal: null,
        left: null,
        right: null
    };

    let videoEl = document.getElementById('webcamVideo');
    let overlayEl = document.getElementById('faceOverlay');
    let canvasEl = document.getElementById('snapshotCanvas');
    let guideBox = document.getElementById('faceGuideBox');
    let progressCircle = document.getElementById('progressCircle');
    let instructionText = document.getElementById('instructionText');
    let aiBadge = document.getElementById('aiBadge');
    let poseBadge = document.getElementById('poseBadge');

    let isModelLoaded = false;
    let isDetecting = false;
    let steadyCounter = 0;
    let isCapturingCooldown = false;
    const STEADY_THRESHOLD = 10; // ~1.2s of steady hold before snap
    const TOTAL_CIRCLE_DASH = 289;

    // Audio beep using Web Audio API
    function playBeep(freq = 600, duration = 0.15) {
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.type = 'sine';
            osc.frequency.setValueAtTime(freq, ctx.currentTime);
            gain.gain.setValueAtTime(0.1, ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + duration);
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.start();
            osc.stop(ctx.currentTime + duration);
        } catch(e) {}
    }

    async function init() {
        console.log('[Face Register] Initializing UI and Camera...');
        updateStepUI();
        await startCamera();
        loadFaceApiModels().then(() => {
            if (isModelLoaded) {
                startDetectionLoop();
            }
        });
    }

    async function startCamera() {
        document.getElementById('cameraOfflineMsg').classList.add('hidden');
        try {
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                throw new Error('MediaDevices not supported on this browser or connection is not HTTPS/localhost');
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

            if (videoEl.readyState >= 1) {
                overlayEl.width = videoEl.videoWidth || 640;
                overlayEl.height = videoEl.videoHeight || 480;
            } else {
                await new Promise((resolve) => {
                    const timer = setTimeout(resolve, 1500);
                    videoEl.onloadedmetadata = () => {
                        clearTimeout(timer);
                        resolve();
                    };
                });
                overlayEl.width = videoEl.videoWidth || 640;
                overlayEl.height = videoEl.videoHeight || 480;
            }

            try {
                await videoEl.play();
            } catch (playErr) {
                console.log('Video autoplay handled:', playErr);
            }

            updateStepUI();
        } catch (err) {
            console.error('Camera error:', err);
            document.getElementById('cameraOfflineMsg').classList.remove('hidden');
            instructionText.innerText = 'Không thể mở camera. Vui lòng cấp quyền truy cập webcam trên trình duyệt!';
        }
    }

    async function loadFaceApiModels() {
        const modelPath = '{{ asset("vendor/face-api/models") }}';
        try {
            if (typeof faceapi === 'undefined') {
                throw new Error('faceapi library not defined');
            }

            const loadPromise = Promise.all([
                faceapi.nets.tinyFaceDetector.loadFromUri(modelPath),
                faceapi.nets.faceLandmark68Net.loadFromUri(modelPath)
            ]);
            const timeoutPromise = new Promise((_, reject) => setTimeout(() => reject(new Error('Model loading timed out')), 5000));
            
            await Promise.race([loadPromise, timeoutPromise]);
            isModelLoaded = true;
            aiBadge.className = 'inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800';
            aiBadge.innerText = 'AI Tự động';
            updateStepUI();
        } catch (e) {
            console.warn('face-api warning, manual capture fallback active:', e);
            aiBadge.className = 'inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-100 text-indigo-800';
            aiBadge.innerText = 'Chụp ảnh sẵn sàng';
            poseBadge.innerText = 'Bấm nút chụp bên dưới';
            updateStepUI();
        }
    }

    function updateStepUI() {
        const steps = [
            { id: 1, name: 'Nhìn thẳng', desc: 'Góc 1/3: Giữ khuôn mặt ở giữa khung oval và nhìn thẳng camera' },
            { id: 2, name: 'Nghiêng trái', desc: 'Góc 2/3: Nghiêng nhẹ mặt sang bên TRÁI khoảng 15° - 20°' },
            { id: 3, name: 'Nghiêng phải', desc: 'Góc 3/3: Nghiêng nhẹ mặt sang bên PHẢI khoảng 15° - 20°' },
        ];

        steps.forEach(s => {
            const el = document.getElementById(`stepIndicator${s.id}`);
            const badge = el.querySelector('div:first-child');
            if (s.id === currentStep) {
                el.className = 'flex items-center gap-3 p-2.5 rounded-xl bg-indigo-50 border-2 border-indigo-500 shadow-sm';
                badge.className = 'w-8 h-8 rounded-lg bg-indigo-600 text-white flex items-center justify-center font-bold text-sm shrink-0';
            } else if (capturedData[s.id === 1 ? 'frontal' : (s.id === 2 ? 'left' : 'right')]) {
                el.className = 'flex items-center gap-3 p-2.5 rounded-xl bg-emerald-50 border border-emerald-200';
                badge.className = 'w-8 h-8 rounded-lg bg-emerald-600 text-white flex items-center justify-center font-bold text-sm shrink-0';
            } else {
                el.className = 'flex items-center gap-3 p-2.5 rounded-xl bg-gray-50 border border-gray-100 opacity-60';
                badge.className = 'w-8 h-8 rounded-lg bg-gray-200 text-gray-600 flex items-center justify-center font-bold text-sm shrink-0';
            }
        });

        if (capturedData.frontal && capturedData.left && capturedData.right) {
            instructionText.innerText = 'Đã chụp đủ 3 góc khuôn mặt. Bấm nút bên dưới để lưu hồ sơ.';
        } else {
            instructionText.innerText = steps[currentStep - 1].desc;
        }

        steadyCounter = 0;
        updateProgressRing(0);
    }

    function selectStep(stepNum) {
        currentStep = stepNum;
        
        // Reset this slot to allow single re-capture
        if (stepNum === 1) capturedData.frontal = null;
        if (stepNum === 2) capturedData.left = null;
        if (stepNum === 3) capturedData.right = null;

        const key = stepNum === 1 ? 'Frontal' : (stepNum === 2 ? 'Left' : 'Right');
        document.getElementById('statusBadge' + key).className = 'inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-gray-100 text-gray-600 mt-1';
        document.getElementById('statusBadge' + key).innerText = 'Chưa chụp';

        isCapturingCooldown = false;
        steadyCounter = 0;
        updateProgressRing(0);
        updateStepUI();
        checkCompletion();
    }

    function updateProgressRing(percent) {
        if (!progressCircle) return;
        const offset = TOTAL_CIRCLE_DASH - (percent * TOTAL_CIRCLE_DASH);
        progressCircle.style.strokeDashoffset = offset;
    }

    async function startDetectionLoop() {
        if (isDetecting) return;
        isDetecting = true;

        const options = new faceapi.TinyFaceDetectorOptions({ inputSize: 320, scoreThreshold: 0.35 });

        setInterval(async () => {
            if (!isModelLoaded || videoEl.paused || videoEl.ended) return;

            // 1. If all 3 angles are already captured, stop auto-capturing completely!
            if (capturedData.frontal && capturedData.left && capturedData.right) {
                guideBox.className = 'absolute inset-0 m-auto w-56 h-72 border-2 border-solid border-emerald-500 rounded-[50%] shadow-[0_0_20px_rgba(16,185,129,0.7)] pointer-events-none';
                poseBadge.className = 'text-emerald-600 font-bold';
                poseBadge.innerText = 'Đã hoàn tất 3 góc';
                steadyCounter = 0;
                updateProgressRing(1.0);
                return;
            }

            // 2. If in cooldown between angles, wait
            if (isCapturingCooldown) {
                steadyCounter = 0;
                updateProgressRing(0);
                return;
            }

            // 3. If the current step already has a photo, do NOT auto-capture again!
            const currentSlotKey = currentStep === 1 ? 'frontal' : (currentStep === 2 ? 'left' : 'right');
            if (capturedData[currentSlotKey]) {
                steadyCounter = 0;
                updateProgressRing(0);
                poseBadge.className = 'text-emerald-600 font-bold';
                poseBadge.innerText = 'Đã chụp góc này';
                return;
            }

            const detection = await faceapi.detectSingleFace(videoEl, options).withFaceLandmarks();
            const ctx = overlayEl.getContext('2d');
            ctx.clearRect(0, 0, overlayEl.width, overlayEl.height);

            if (!detection) {
                guideBox.className = 'absolute inset-0 m-auto w-56 h-72 border-2 border-dashed border-white/40 rounded-[50%] pointer-events-none transition-all duration-300';
                poseBadge.className = 'text-gray-500 font-medium';
                poseBadge.innerText = 'Không phát hiện khuôn mặt';
                steadyCounter = 0;
                updateProgressRing(0);
                return;
            }

            // Check Face Centering and Close-up (Cận cảnh & Canh ngay giữa)
            const box = detection.detection.box;
            const videoWidth = videoEl.videoWidth || 640;
            const videoHeight = videoEl.videoHeight || 480;

            const faceCenterX = box.x + box.width / 2;
            const faceCenterY = box.y + box.height / 2;
            const isCenteredX = Math.abs(faceCenterX - videoWidth / 2) < (videoWidth * 0.18);
            const isCenteredY = Math.abs(faceCenterY - videoHeight / 2) < (videoHeight * 0.20);
            const isCentered = isCenteredX && isCenteredY;

            const faceHeightRatio = box.height / videoHeight;
            const isCloseUp = faceHeightRatio >= 0.33; // Face must take at least 33% of frame height

            if (!isCloseUp) {
                guideBox.className = 'absolute inset-0 m-auto w-56 h-72 border-2 border-dashed border-amber-400 rounded-[50%] pointer-events-none transition-all duration-200';
                poseBadge.className = 'text-amber-600 font-bold';
                poseBadge.innerText = 'Đưa mặt lại gần hơn (cận cảnh)';
                steadyCounter = 0;
                updateProgressRing(0);
                return;
            }

            if (!isCentered) {
                guideBox.className = 'absolute inset-0 m-auto w-56 h-72 border-2 border-dashed border-amber-400 rounded-[50%] pointer-events-none transition-all duration-200';
                poseBadge.className = 'text-amber-600 font-bold';
                poseBadge.innerText = 'Căn giữa khuôn mặt';
                steadyCounter = 0;
                updateProgressRing(0);
                return;
            }

            // Estimate horizontal yaw from facial landmarks (supports with or without glasses)
            const landmarks = detection.landmarks;
            const nose = landmarks.getNose()[3]; // nose tip (point 30)
            const leftEye = landmarks.getLeftEye()[0];  // image-left eye corner (smaller X)
            const rightEye = landmarks.getRightEye()[3]; // image-right eye corner (larger X)

            const eyeMidX = (leftEye.x + rightEye.x) / 2;
            const eyeDist = Math.hypot(rightEye.x - leftEye.x, rightEye.y - leftEye.y) || 1;
            
            // Nose shift relative to eye midpoint:
            // Positive: Nose moved towards higher X (User's LEFT)
            // Negative: Nose moved towards lower X (User's RIGHT)
            const noseShift = (nose.x - eyeMidX) / eyeDist;

            // Determine detected head pose
            let detectedPose = 'transition';
            let poseDesc = 'Đang chỉnh góc';

            if (noseShift >= 0.11) {
                detectedPose = 'left';
                poseDesc = 'Nghiêng trái';
            } else if (noseShift <= -0.11) {
                detectedPose = 'right';
                poseDesc = 'Nghiêng phải';
            } else if (Math.abs(noseShift) <= 0.08) {
                detectedPose = 'straight';
                poseDesc = 'Nhìn thẳng';
            }

            // Check if current detected pose matches requested step
            let isCorrectAngle = false;
            let requiredAngleName = '';

            if (currentStep === 1) { // Frontal
                requiredAngleName = 'Nhìn thẳng';
                if (detectedPose === 'straight') isCorrectAngle = true;
            } else if (currentStep === 2) { // Left only
                requiredAngleName = 'Nghiêng trái';
                if (detectedPose === 'left') isCorrectAngle = true;
            } else if (currentStep === 3) { // Right only
                requiredAngleName = 'Nghiêng phải';
                if (detectedPose === 'right') isCorrectAngle = true;
            }

            if (isCorrectAngle) {
                poseBadge.className = 'text-emerald-600 font-bold';
                poseBadge.innerText = `${poseDesc} (Giữ yên...)`;
            } else {
                poseBadge.className = 'text-amber-600 font-semibold';
                poseBadge.innerText = `Đang ${poseDesc} (Cần ${requiredAngleName})`;
            }

            if (isCorrectAngle) {
                guideBox.className = 'absolute inset-0 m-auto w-56 h-72 border-2 border-solid border-emerald-400 rounded-[50%] shadow-[0_0_20px_rgba(16,185,129,0.6)] pointer-events-none transition-all duration-200';
                steadyCounter++;
                const progress = Math.min(1.0, steadyCounter / STEADY_THRESHOLD);
                updateProgressRing(progress);

                if (steadyCounter >= STEADY_THRESHOLD) {
                    // Auto-capture steady angle once!
                    captureCurrentAngle();
                    steadyCounter = 0;
                    updateProgressRing(0);
                }
            } else {
                guideBox.className = 'absolute inset-0 m-auto w-56 h-72 border-2 border-dashed border-amber-400 rounded-[50%] pointer-events-none transition-all duration-200';
                steadyCounter = Math.max(0, steadyCounter - 1);
                updateProgressRing(steadyCounter / STEADY_THRESHOLD);
            }

        }, 120);
    }

    function captureCurrentAngle() {
        canvasEl.width = videoEl.videoWidth || 640;
        canvasEl.height = videoEl.videoHeight || 480;
        const ctx = canvasEl.getContext('2d');
        
        ctx.drawImage(videoEl, 0, 0, canvasEl.width, canvasEl.height);
        const dataUrl = canvasEl.toDataURL('image/jpeg', 0.92);

        playBeep(880, 0.15);

        // Enter transition cooldown so it never immediately snaps the next angle
        isCapturingCooldown = true;
        steadyCounter = 0;
        updateProgressRing(0);

        if (currentStep === 1) {
            capturedData.frontal = dataUrl;
            document.getElementById('previewFrontal').src = dataUrl;
            document.getElementById('previewFrontal').classList.remove('hidden');
            document.getElementById('emptyFrontal').classList.add('hidden');
            document.getElementById('statusBadgeFrontal').className = 'inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 mt-1';
            document.getElementById('statusBadgeFrontal').innerText = 'Đã chụp';
            
            // Move to Step 2
            currentStep = 2;
            updateStepUI();
        } else if (currentStep === 2) {
            capturedData.left = dataUrl;
            document.getElementById('previewLeft').src = dataUrl;
            document.getElementById('previewLeft').classList.remove('hidden');
            document.getElementById('emptyLeft').classList.add('hidden');
            document.getElementById('statusBadgeLeft').className = 'inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 mt-1';
            document.getElementById('statusBadgeLeft').innerText = 'Đã chụp';

            // Move to Step 3
            currentStep = 3;
            updateStepUI();
        } else if (currentStep === 3) {
            capturedData.right = dataUrl;
            document.getElementById('previewRight').src = dataUrl;
            document.getElementById('previewRight').classList.remove('hidden');
            document.getElementById('emptyRight').classList.add('hidden');
            document.getElementById('statusBadgeRight').className = 'inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 mt-1';
            document.getElementById('statusBadgeRight').innerText = 'Đã chụp';

            updateStepUI();
            instructionText.innerText = 'Đã chụp đủ 3 góc. Bấm nút bên dưới để lưu hồ sơ.';
        }

        checkCompletion();

        // 1.8 second cooldown before allowing the next angle to auto-capture
        setTimeout(() => {
            isCapturingCooldown = false;
        }, 1800);
    }

    function manualCaptureAngle() {
        captureCurrentAngle();
    }

    function checkCompletion() {
        const isComplete = Boolean(capturedData.frontal && capturedData.left && capturedData.right);
        const submitBtn = document.getElementById('submitRegisterBtn');
        submitBtn.disabled = !isComplete;
        if (isComplete) {
            submitBtn.classList.remove('opacity-40', 'pointer-events-none');
            submitBtn.classList.add('animate-pulse');
            setTimeout(() => submitBtn.classList.remove('animate-pulse'), 1500);
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
