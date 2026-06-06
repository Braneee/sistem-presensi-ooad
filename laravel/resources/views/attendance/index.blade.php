<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Presensi — Sistem Presensi Face Recognition</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(135deg, #1F4E79 0%, #2E75B6 60%, #c8dff2 100%);
            min-height: 100vh;
        }

        @keyframes pulse-ring {
            0% {
                transform: scale(1);
                opacity: 0.8;
            }

            100% {
                transform: scale(1.4);
                opacity: 0;
            }
        }

        .pulse-ring::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 50%;
            background: #22c55e;
            animation: pulse-ring 1.5s ease-out infinite;
        }

        #camera-feed {
            transform: scaleX(-1);
            /* Mirror effect */
        }

        .corner-tl {
            position: absolute;
            top: -3px;
            left: -3px;
            width: 24px;
            height: 24px;
            border-top: 3px solid white;
            border-left: 3px solid white;
            border-radius: 4px 0 0 0;
        }

        .corner-tr {
            position: absolute;
            top: -3px;
            right: -3px;
            width: 24px;
            height: 24px;
            border-top: 3px solid white;
            border-right: 3px solid white;
            border-radius: 0 4px 0 0;
        }

        .corner-bl {
            position: absolute;
            bottom: -3px;
            left: -3px;
            width: 24px;
            height: 24px;
            border-bottom: 3px solid white;
            border-left: 3px solid white;
            border-radius: 0 0 0 4px;
        }

        .corner-br {
            position: absolute;
            bottom: -3px;
            right: -3px;
            width: 24px;
            height: 24px;
            border-bottom: 3px solid white;
            border-right: 3px solid white;
            border-radius: 0 0 4px 0;
        }
    </style>
</head>

<body class="flex items-center justify-center p-4 py-8">

    <div class="w-full max-w-md">

        <!-- Header -->
        <div class="text-center mb-6">
            <div class="inline-flex w-14 h-14 bg-white/20 rounded-2xl items-center justify-center mb-3">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-white">Presensi Online</h1>
            <p class="text-blue-100 text-sm mt-1">Sistem Presensi Face Recognition</p>
        </div>

        <!-- Session Selector Card -->
        <div class="bg-white rounded-2xl shadow-xl p-5 mb-4">
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                Pilih Sesi Presensi
            </label>
            <select id="session-select"
                class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 outline-none transition-all bg-gray-50">
                <option value="">— Pilih sesi —</option>
                @foreach($openSessions as $session)
                    <option value="{{ $session->id }}">
                        {{ $session->title }} | {{ $session->classRoom->name }}
                        ({{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }}–{{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }})
                    </option>
                @endforeach
            </select>

            @if($openSessions->isEmpty())
                <div class="mt-3 flex items-center gap-2 px-3 py-2.5 bg-amber-50 rounded-lg border border-amber-200">
                    <svg class="w-4 h-4 text-amber-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd" />
                    </svg>
                    <p class="text-xs text-amber-700">
                        Tidak ada sesi presensi yang sedang buka.
                        <a href="{{ route('login') }}" class="underline font-medium">Hubungi admin.</a>
                    </p>
                </div>
            @endif
        </div>

        <!-- Camera Card -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">

            <!-- Camera Preview Area -->
            <div class="relative bg-gray-900" style="aspect-ratio:4/3">

                <!-- Video feed -->
                <video id="camera-feed" class="w-full h-full object-cover" autoplay playsinline muted></video>

                <!-- Face guide overlay -->
                <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                    <div class="relative w-44 h-52">
                        <div class="corner-tl"></div>
                        <div class="corner-tr"></div>
                        <div class="corner-bl"></div>
                        <div class="corner-br"></div>
                        <div class="absolute inset-0 border border-white/30 rounded-2xl"></div>
                    </div>
                </div>

                <!-- Processing overlay -->
                <div id="processing-overlay"
                    class="absolute inset-0 bg-black/70 flex flex-col items-center justify-center hidden z-10">
                    <div class="w-14 h-14 border-4 border-white/30 border-t-white rounded-full animate-spin mb-4"></div>
                    <p class="text-white text-sm font-semibold">Memproses wajah...</p>
                    <p class="text-white/60 text-xs mt-1">Mohon tunggu sebentar</p>
                </div>

                <!-- Camera off overlay -->
                <div id="camera-off-overlay"
                    class="absolute inset-0 bg-gray-900 flex flex-col items-center justify-center z-10">
                    <svg class="w-14 h-14 text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M15 10l4.553-2.069A1 1 0 0121 8.869v6.262a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                    <p class="text-gray-400 text-sm mb-4">Kamera belum aktif</p>
                    <button onclick="startCamera()"
                        class="px-5 py-2.5 rounded-xl text-white text-sm font-medium transition-all hover:opacity-90"
                        style="background:#1F4E79">
                        Aktifkan Kamera
                    </button>
                    <p class="text-gray-500 text-xs mt-2 px-6 text-center">
                        Izinkan akses kamera saat browser meminta
                    </p>
                </div>

                <!-- Success flash overlay -->
                <div id="success-overlay"
                    class="absolute inset-0 bg-green-500/20 hidden z-10 flex items-center justify-center">
                    <div class="w-20 h-20 bg-green-500 rounded-full flex items-center justify-center">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Controls -->
            <div class="p-5">

                <!-- Feedback Message -->
                <div id="feedback-area" class="mb-4 min-h-[3rem] flex items-center justify-center">
                    <div id="feedback-msg"
                        class="hidden w-full px-4 py-3 rounded-xl text-sm text-center font-medium leading-relaxed">
                    </div>
                    <p id="default-hint" class="text-xs text-gray-400 text-center">
                        Posisikan wajah di dalam bingkai. Sistem akan mendeteksi otomatis.
                    </p>
                </div>

                <!-- Capture Button disembunyikan karena presensi berjalan otomatis -->
                <button id="capture-btn" onclick="captureAndRecognize()" disabled class="hidden w-full py-4 rounded-xl text-white font-bold text-base transition-all
               disabled:opacity-40 disabled:cursor-not-allowed
               hover:opacity-90 active:scale-[0.98]
               items-center justify-center gap-2.5"
                    style="background: linear-gradient(135deg, #1F4E79 0%, #2E75B6 100%)">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span id="btn-text">Presensi Sekarang</span>
                </button>

                <!-- Admin link -->
                <p class="text-center mt-4 text-xs text-gray-400">
                    Admin?
                    <a href="{{ route('login') }}" class="text-blue-600 hover:underline">Masuk ke dashboard →</a>
                </p>
            </div>
        </div>

        <!-- Time display -->
        <div class="text-center mt-4">
            <p id="clock" class="text-white/80 text-lg font-mono font-semibold"></p>
            <p class="text-blue-200 text-xs mt-0.5">{{ now()->isoFormat('dddd, D MMMM Y') }}</p>
        </div>
    </div>

    <!-- Hidden canvas for capture -->
    <canvas id="capture-canvas" class="hidden"></canvas>

    <script>
        // ── Constants ─────────────────────────────────────────────────────────────
        const CSRF = document.querySelector('meta[name="csrf-token"]').content;
        let mediaStream = null;
        let isProcessing = false;
        let successTimer = null;

        let autoDetectTimer = null;
        let attendanceCompleted = false;

        // ── Clock ─────────────────────────────────────────────────────────────────
        function updateClock() {
            const now = new Date();
            document.getElementById('clock').textContent =
                now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        }
        updateClock();
        setInterval(updateClock, 1000);

        // ── Camera ────────────────────────────────────────────────────────────────
        async function startCamera() {
            try {
                mediaStream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: 'user',
                        width: { ideal: 640 },
                        height: { ideal: 480 },
                    }
                });

                const video = document.getElementById('camera-feed');
                video.srcObject = mediaStream;
                await video.play();

                document.getElementById('camera-off-overlay').classList.add('hidden');
                updateBtnState();
                startAutoDetect();

            } catch (err) {
                const msg = err.name === 'NotAllowedError'
                    ? 'Akses kamera ditolak. Izinkan akses kamera di pengaturan browser.'
                    : `Gagal mengakses kamera: ${err.message}`;
                showFeedback('error', '📷 ' + msg);
            }
        }

        // ── State ─────────────────────────────────────────────────────────────────
        function updateBtnState() {
            const sessionId = document.getElementById('session-select').value;
            const btn = document.getElementById('capture-btn');
            btn.disabled = !(sessionId && mediaStream && !isProcessing);
        }

        function startAutoDetect() {
            if (autoDetectTimer) {
                clearInterval(autoDetectTimer);
            }

            autoDetectTimer = setInterval(() => {
                const sessionId = document.getElementById('session-select').value;

                if (!sessionId) return;
                if (!mediaStream) return;
                if (isProcessing) return;
                if (attendanceCompleted) return;

                captureAndRecognize(true);
            }, 3000);
        }

        function stopAutoDetect() {
            if (autoDetectTimer) {
                clearInterval(autoDetectTimer);
                autoDetectTimer = null;
            }
        }

        document.getElementById('session-select').addEventListener('change', () => {
            attendanceCompleted = false;
            updateBtnState();

            if (mediaStream) {
                startAutoDetect();
            }
        });
        // ── Capture & Recognize ───────────────────────────────────────────────────
        async function captureAndRecognize(isAuto = false) {
            if (isProcessing || attendanceCompleted) return;

            const sessionId = document.getElementById('session-select').value;
            if (!sessionId) {
                if (!isAuto) {
                    showFeedback('error', '⚠️ Pilih sesi presensi terlebih dahulu.');
                }
                return;
            }

            const video = document.getElementById('camera-feed');
            const canvas = document.getElementById('capture-canvas');

            // Capture frame
            canvas.width = video.videoWidth || 640;
            canvas.height = video.videoHeight || 480;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(video, 0, 0);
            const imageBase64 = canvas.toDataURL('image/jpeg', 0.85);

            // Set processing state
            isProcessing = true;
            updateBtnState();
            document.getElementById('btn-text').textContent = 'Memproses...';
            document.getElementById('processing-overlay').classList.remove('hidden');
            document.getElementById('default-hint').classList.add('hidden');
            hideFeedback();

            try {
                const res = await fetch('/api/attendance/detect', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                    },
                    body: JSON.stringify({
                        session_id: parseInt(sessionId),
                        image: imageBase64,
                    }),
                });

                const result = await res.json();

                if (result.success) {
                    attendanceCompleted = true;
                    stopAutoDetect();

                    // Show success flash on camera
                    document.getElementById('success-overlay').classList.remove('hidden');
                    setTimeout(() => document.getElementById('success-overlay').classList.add('hidden'), 1500);

                    const isLate = result.data.status === 'late';
                    const statusTx = isLate ? '⏰ Terlambat' : '✅ Tepat Waktu';
                    showFeedback('success',
                        `<strong>${result.message}</strong><br>
                     <span class="font-normal opacity-80 text-xs">
                         ${statusTx} &nbsp;|&nbsp; Pukul ${result.data.checked_in}
                         &nbsp;|&nbsp; Akurasi ${result.data.similarity}
                     </span>`
                    );

                    // Re-enable after 10s to prevent quick double submission
                    successTimer = setTimeout(() => {
                        isProcessing = false;
                        document.getElementById('btn-text').textContent = 'Presensi Selesai';
                        updateBtnState();
                    }, 10000);

                } else {
                    const codeIcons = {
                        SESSION_CLOSED: '🔒 Sesi Ditutup',
                        FACE_NOT_DETECTED: '👤 Wajah Tidak Terdeteksi',
                        FACE_NOT_RECOGNIZED: '❓ Wajah Tidak Dikenal',
                        DUPLICATE_ATTENDANCE: '✅ Sudah Presensi',
                        IMAGE_QUALITY_LOW: '📷 Kualitas Gambar Rendah',
                        AI_ENGINE_DOWN: '🤖 AI Engine Offline',
                    };
                    const prefix = codeIcons[result.code] || '⚠️ Gagal';
                    showFeedback('error', `<strong>${prefix}</strong><br><span class="font-normal text-xs opacity-80">${result.message}</span>`);

                    if (result.code === 'DUPLICATE_ATTENDANCE') {
                        attendanceCompleted = true;
                        stopAutoDetect();
                    }

                    isProcessing = false;
                    document.getElementById('btn-text').textContent = 'Presensi Sekarang';
                    updateBtnState();
                }

            } catch (err) {
                const msg = !navigator.onLine
                    ? '📡 Koneksi internet terputus. Periksa jaringan Anda.'
                    : '⚠️ Terjadi kesalahan jaringan. Silakan coba lagi.';
                showFeedback('error', msg);
                isProcessing = false;
                document.getElementById('btn-text').textContent = 'Presensi Sekarang';
                updateBtnState();

            } finally {
                document.getElementById('processing-overlay').classList.add('hidden');
            }
        }

        // ── Feedback ──────────────────────────────────────────────────────────────
        function showFeedback(type, html) {
            const msg = document.getElementById('feedback-msg');
            msg.innerHTML = html;
            msg.className = `w-full px-4 py-3 rounded-xl text-sm text-center font-medium leading-relaxed ${type === 'success'
                ? 'bg-green-50 border border-green-200 text-green-800'
                : 'bg-red-50 border border-red-200 text-red-800'
                }`;
            msg.classList.remove('hidden');
            document.getElementById('default-hint').classList.add('hidden');

            if (type === 'error') {
                setTimeout(resetFeedback, 10000);
            }
        }

        function hideFeedback() {
            document.getElementById('feedback-msg').classList.add('hidden');
        }

        function resetFeedback() {
            document.getElementById('feedback-msg').classList.add('hidden');
            document.getElementById('default-hint').classList.remove('hidden');
        }

        // ── Init ──────────────────────────────────────────────────────────────────
        window.addEventListener('DOMContentLoaded', () => {
            startCamera();
        });

        window.addEventListener('beforeunload', () => {
            if (mediaStream) mediaStream.getTracks().forEach(t => t.stop());
        });
    </script>

</body>

</html>