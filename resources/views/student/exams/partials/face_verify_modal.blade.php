<!-- ====================================================
     FACE VERIFICATION MODAL (AUTO-SCAN PRE-EXAM)
     ==================================================== -->
<style>
    @keyframes scanLineVerify {
        0% { top: 10%; opacity: 0.85; }
        50% { top: 86%; opacity: 1; }
        100% { top: 10%; opacity: 0.85; }
    }
    .laser-verify-line {
        position: absolute;
        left: 12%;
        right: 12%;
        height: 3px;
        background: linear-gradient(90deg, transparent, #10b981, #38bdf8, #10b981, transparent);
        box-shadow: 0 0 12px #10b981, 0 0 24px #38bdf8;
        border-radius: 9999px;
        animation: scanLineVerify 2s ease-in-out infinite;
        pointer-events: none;
        z-index: 20;
    }
</style>

<div id="faceVerifyModal" class="fixed inset-0 z-[99999] hidden">
    <!-- Backdrop -->
    <div class="absolute inset-0 bg-slate-950/85 backdrop-blur-md transition-opacity" onclick="closeFaceVerifyModal()"></div>

    <div class="fixed inset-0 overflow-y-auto flex items-center justify-center p-4 sm:p-6">
        <div class="relative bg-white rounded-3xl shadow-2xl border border-slate-100 max-w-md w-full p-6 sm:p-8 space-y-5 transform transition-all overflow-hidden z-10 text-center flex flex-col items-center">
            
            <!-- Close button -->
            <button type="button" onclick="closeFaceVerifyModal()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 p-2 rounded-xl hover:bg-slate-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>

            <!-- Header info -->
            <div class="space-y-1">
                <h3 class="font-black text-lg text-slate-900">Xác thực Khuôn mặt</h3>
                <p id="modalExamTitle" class="text-xs text-slate-500 font-medium">Đang chuẩn bị phòng thi...</p>
            </div>

            <!-- Circular Camera Scanner with Laser Line -->
            <div id="verifyCameraContainer" class="relative w-52 h-52 sm:w-56 sm:h-56 rounded-full overflow-hidden border-4 border-indigo-600 shadow-2xl bg-slate-950 flex items-center justify-center my-2 transition-all duration-300">
                <video id="verifyVideo" autoplay playsinline muted class="w-full h-full object-cover transform -scale-x-100"></video>
                
                <!-- Laser Scanning Line -->
                <div class="laser-verify-line"></div>

                <!-- Circular Progress SVG Ring -->
                <svg class="absolute inset-0 w-full h-full -rotate-90 pointer-events-none" viewBox="0 0 160 160">
                    <circle cx="80" cy="80" r="74" fill="none" stroke="rgba(255,255,255,0.15)" stroke-width="6" />
                    <circle id="verifyProgressCircle" cx="80" cy="80" r="74" fill="none" stroke="#10b981" stroke-width="6" stroke-dasharray="465" stroke-dashoffset="465" stroke-linecap="round" class="transition-all duration-100" />
                </svg>

                <!-- Centered Scanning Overlay -->
                <div id="scanOverlayStatus" class="hidden absolute inset-0 bg-slate-950/75 backdrop-blur-xs flex flex-col items-center justify-center text-white text-center p-3 z-30">
                    <div id="scanSpinner" class="w-9 h-9 border-3 border-emerald-400 border-t-transparent rounded-full animate-spin mb-2"></div>
                    <span id="scanPercentText" class="text-xs font-bold text-slate-200">Đang đối chiếu...</span>
                </div>
            </div>

            <!-- Status Indicator -->
            <div id="verifyMessageContainer" class="w-full text-center">
                <div id="verifyStatusBox" class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-indigo-600"></span>
                    </span>
                    <span id="verifyStatusText">Nhìn thẳng và căn giữa khuôn mặt</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden Canvas for capturing verify frame -->
<canvas id="verifyCanvas" class="hidden"></canvas>

<!-- Ensure face-api.min.js is loaded if not already present -->
<script src="{{ asset('vendor/face-api/face-api.min.js') }}"></script>

<script>
    let activeVerifyExamId = null;
    let verifyStream = null;
    let isVerifying = false;
    let verifyLoopTimer = null;
    let autoRetryTimer = null;
    let verifySteadyCounter = 0;
    const VERIFY_STEADY_GOAL = 11; // ~1.3s of steady close-up centered positioning
    const VERIFY_CIRCLE_DASH = 465;

    async function openFaceVerifyModal(examId, examTitle) {
        activeVerifyExamId = examId;
        document.getElementById('modalExamTitle').innerText = examTitle || '';
        document.getElementById('faceVerifyModal').classList.remove('hidden');
        setCircleProgress(0);
        resetVerifyStatus();
        verifySteadyCounter = 0;
        await startVerifyCamera();
        startVerifyAutoTracking();
    }

    function closeFaceVerifyModal() {
        if (verifyLoopTimer) clearInterval(verifyLoopTimer);
        if (autoRetryTimer) clearTimeout(autoRetryTimer);
        document.getElementById('faceVerifyModal').classList.add('hidden');
        stopVerifyCamera();
    }

    async function startVerifyCamera() {
        const videoEl = document.getElementById('verifyVideo');
        try {
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                updateStatusBox('danger', 'Trình duyệt không hỗ trợ webcam.');
                return;
            }
            verifyStream = await navigator.mediaDevices.getUserMedia({
                video: { width: { ideal: 640 }, height: { ideal: 480 }, facingMode: 'user' },
                audio: false
            });
            videoEl.srcObject = verifyStream;
            try { await videoEl.play(); } catch(e){}
        } catch (err) {
            console.error('Camera open error:', err);
            updateStatusBox('danger', 'Không thể mở camera. Vui lòng cấp quyền webcam trên trình duyệt.');
        }
    }

    function stopVerifyCamera() {
        if (verifyStream) {
            verifyStream.getTracks().forEach(t => t.stop());
            verifyStream = null;
        }
    }

    function setCircleProgress(percent) {
        const circle = document.getElementById('verifyProgressCircle');
        if (!circle) return;
        const offset = VERIFY_CIRCLE_DASH - (percent * VERIFY_CIRCLE_DASH);
        circle.style.strokeDashoffset = offset;
    }

    function resetVerifyStatus() {
        updateStatusBox('normal', 'Căn giữa mặt và đưa lại gần khung hình');
        document.getElementById('scanOverlayStatus').classList.add('hidden');
    }

    function updateStatusBox(type, msg) {
        const box = document.getElementById('verifyStatusBox');
        const textEl = document.getElementById('verifyStatusText');
        if (!box || !textEl) return;

        if (type === 'success') {
            box.className = 'inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 animate-pulse';
        } else if (type === 'danger') {
            box.className = 'inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs font-semibold bg-rose-100 text-rose-800';
        } else if (type === 'warning') {
            box.className = 'inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs font-semibold bg-amber-100 text-amber-800';
        } else {
            box.className = 'inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700';
        }
        textEl.innerText = msg;
    }

    function resetAndRetryVerify() {
        resetVerifyStatus();
        setCircleProgress(0);
        verifySteadyCounter = 0;
        isVerifying = false;
        startVerifyAutoTracking();
    }

    // Real-time tracking loop: checks centered + close-up before auto-capturing
    function startVerifyAutoTracking() {
        if (verifyLoopTimer) clearInterval(verifyLoopTimer);
        const videoEl = document.getElementById('verifyVideo');
        const container = document.getElementById('verifyCameraContainer');

        const options = (typeof faceapi !== 'undefined' && faceapi.TinyFaceDetectorOptions)
            ? new faceapi.TinyFaceDetectorOptions({ inputSize: 320, scoreThreshold: 0.35 })
            : null;

        verifyLoopTimer = setInterval(async () => {
            if (isVerifying || !videoEl || videoEl.paused || videoEl.ended) return;

            let isPositionReady = false;

            if (typeof faceapi !== 'undefined' && options && faceapi.nets.tinyFaceDetector.params) {
                try {
                    const det = await faceapi.detectSingleFace(videoEl, options);
                    if (det) {
                        const box = det.box;
                        const vW = videoEl.videoWidth || 640;
                        const vH = videoEl.videoHeight || 480;

                        const fCenterX = box.x + box.width / 2;
                        const fCenterY = box.y + box.height / 2;
                        const isCenteredX = Math.abs(fCenterX - vW / 2) < (vW * 0.20);
                        const isCenteredY = Math.abs(fCenterY - vH / 2) < (vH * 0.22);
                        const isCentered = isCenteredX && isCenteredY;

                        const isCloseUp = (box.height / vH) >= 0.32; // Close-up check

                        if (!isCloseUp) {
                            updateStatusBox('warning', 'Đưa mặt lại gần khung hình (cận cảnh)');
                            container.className = 'relative w-52 h-52 sm:w-56 sm:h-56 rounded-full overflow-hidden border-4 border-amber-400 shadow-2xl bg-slate-950 flex items-center justify-center my-2 transition-all duration-200';
                            verifySteadyCounter = 0;
                            setCircleProgress(0);
                            return;
                        }

                        if (!isCentered) {
                            updateStatusBox('warning', 'Căn giữa khuôn mặt');
                            container.className = 'relative w-52 h-52 sm:w-56 sm:h-56 rounded-full overflow-hidden border-4 border-amber-400 shadow-2xl bg-slate-950 flex items-center justify-center my-2 transition-all duration-200';
                            verifySteadyCounter = 0;
                            setCircleProgress(0);
                            return;
                        }

                        isPositionReady = true;
                    } else {
                        updateStatusBox('normal', 'Nhìn thẳng vào camera');
                        container.className = 'relative w-52 h-52 sm:w-56 sm:h-56 rounded-full overflow-hidden border-4 border-indigo-600 shadow-2xl bg-slate-950 flex items-center justify-center my-2 transition-all duration-200';
                        verifySteadyCounter = 0;
                        setCircleProgress(0);
                        return;
                    }
                } catch(e) {
                    isPositionReady = true;
                }
            } else {
                isPositionReady = true;
            }

            if (isPositionReady) {
                container.className = 'relative w-52 h-52 sm:w-56 sm:h-56 rounded-full overflow-hidden border-4 border-emerald-400 shadow-[0_0_25px_rgba(16,185,129,0.6)] bg-slate-950 flex items-center justify-center my-2 transition-all duration-200';
                verifySteadyCounter++;
                const progress = Math.min(1.0, verifySteadyCounter / VERIFY_STEADY_GOAL);
                setCircleProgress(progress);
                updateStatusBox('warning', 'Vị trí chuẩn, đang quét...');

                if (verifySteadyCounter >= VERIFY_STEADY_GOAL) {
                    clearInterval(verifyLoopTimer);
                    performFaceScan();
                }
            }
        }, 120);
    }

    async function performFaceScan() {
        if (isVerifying || !activeVerifyExamId) return;
        isVerifying = true;

        const videoEl = document.getElementById('verifyVideo');
        const canvasEl = document.getElementById('verifyCanvas');
        const scanOverlay = document.getElementById('scanOverlayStatus');

        scanOverlay.classList.remove('hidden');
        setCircleProgress(1.0);
        updateStatusBox('warning', 'Đang so khớp danh tính...');

        // Capture high-res frame
        canvasEl.width = videoEl.videoWidth || 640;
        canvasEl.height = videoEl.videoHeight || 480;
        const ctx = canvasEl.getContext('2d');
        ctx.drawImage(videoEl, 0, 0, canvasEl.width, canvasEl.height);
        const probeDataUrl = canvasEl.toDataURL('image/jpeg', 0.92);

        try {
            const verifyUrl = `/sinh-vien/de-thi/${activeVerifyExamId}/xac-thuc-khuon-mat`;
            const response = await fetch(verifyUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ image: probeDataUrl })
            });

            const data = await response.json();

            if (data.success && data.matched) {
                // SUCCESS: Seamless instant entry without showing % match
                updateStatusBox('success', 'Xác thực thành công, đang vào bài thi...');
                document.getElementById('scanPercentText').innerText = 'Hoàn tất';
                
                // Immediately enter exam
                setTimeout(() => {
                    window.location.href = data.redirect_url;
                }, 250);
            } else {
                // FAILED: Auto-retry without clicking any button!
                scanOverlay.classList.add('hidden');
                setCircleProgress(0);
                updateStatusBox('danger', data.message || 'Chưa nhận diện đúng. Đang tự động quét lại...');
                
                if (autoRetryTimer) clearTimeout(autoRetryTimer);
                autoRetryTimer = setTimeout(() => {
                    if (!document.getElementById('faceVerifyModal').classList.contains('hidden')) {
                        resetAndRetryVerify();
                    }
                }, 1200);
            }
        } catch (err) {
            scanOverlay.classList.add('hidden');
            setCircleProgress(0);
            console.error('Face verification error:', err);
            updateStatusBox('danger', 'Đang tự động kết nối và quét lại...');
            
            if (autoRetryTimer) clearTimeout(autoRetryTimer);
            autoRetryTimer = setTimeout(() => {
                if (!document.getElementById('faceVerifyModal').classList.contains('hidden')) {
                    resetAndRetryVerify();
                }
            }, 1500);
        } finally {
            isVerifying = false;
        }
    }
</script>
