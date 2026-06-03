@extends('layouts.admin')

@section('title', 'Registrasi Wajah')
@section('page-title', 'Registrasi Wajah')
@section('page-subtitle', 'Daftarkan data wajah mahasiswa untuk sistem presensi')

@section('content')

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- Student Info --}}
    <div class="space-y-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h3 class="font-semibold text-gray-800 text-sm mb-4">Identitas Mahasiswa</h3>

            <div class="flex items-center gap-4 mb-4">
                <img src="{{ $student->photo_url }}" alt="{{ $student->name }}"
                     class="w-16 h-16 rounded-xl object-cover border-2 border-gray-100">
                <div>
                    <p class="font-semibold text-gray-800">{{ $student->name }}</p>
                    <p class="text-xs text-gray-500 font-mono">{{ $student->nim }}</p>
                    <p class="text-xs text-gray-500">{{ $student->classRoom->name }}</p>
                </div>
            </div>

            <dl class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-500">Email</dt>
                    <dd class="text-gray-700 text-xs">{{ $student->email ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Jenis Kelamin</dt>
                    <dd class="text-gray-700">{{ $student->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Status Wajah</dt>
                    <dd>
                        @if($student->hasFaceRegistered())
                            <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-medium">✓ Terdaftar</span>
                        @else
                            <span class="text-xs bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full font-medium">Belum Terdaftar</span>
                        @endif
                    </dd>
                </div>
            </dl>
        </div>

        {{-- Existing Faces --}}
        @if($student->faces->count() > 0)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-semibold text-gray-800 text-sm mb-3">Data Wajah Tersimpan</h3>
                <div class="space-y-3">
                    @foreach($student->faces as $face)
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                            @if($face->photo_url)
                                <img src="{{ $face->photo_url }}" alt="Face" class="w-12 h-12 rounded-lg object-cover">
                            @endif
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-medium text-gray-700">{{ $face->model_version }}</p>
                                <p class="text-xs text-gray-400">{{ $face->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                            <form action="{{ route('admin.faces.destroy', $face) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        onclick="return confirm('Hapus data wajah ini?')"
                                        class="text-xs text-red-500 hover:text-red-700">Hapus</button>
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <a href="{{ route('admin.faces.index') }}"
           class="flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700">
            ← Kembali ke daftar registrasi
        </a>
    </div>

    {{-- Camera Registration --}}
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800 text-sm">Ambil Foto Wajah</h3>
                <p class="text-xs text-gray-400 mt-0.5">Gunakan webcam atau upload foto</p>
            </div>

            {{-- Tabs --}}
            <div class="border-b border-gray-100 flex">
                <button onclick="switchTab('webcam')" id="tab-webcam"
                        class="px-5 py-3 text-sm font-medium border-b-2 border-blue-600 text-blue-600">
                    📹 Webcam
                </button>
                <button onclick="switchTab('upload')" id="tab-upload"
                        class="px-5 py-3 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700">
                    📁 Upload Foto
                </button>
            </div>

            {{-- Webcam Tab --}}
            <div id="tab-content-webcam" class="p-5">
                <div class="relative bg-gray-900 rounded-xl overflow-hidden mb-4" style="aspect-ratio:4/3">
                    <video id="reg-camera" class="w-full h-full object-cover" style="transform:scaleX(-1)"
                           autoplay playsinline muted></video>

                    <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                        <div class="w-40 h-48 border-2 border-white/50 rounded-2xl relative">
                            <div class="absolute -top-0.5 -left-0.5 w-5 h-5 border-t-2 border-l-2 border-white rounded-tl"></div>
                            <div class="absolute -top-0.5 -right-0.5 w-5 h-5 border-t-2 border-r-2 border-white rounded-tr"></div>
                            <div class="absolute -bottom-0.5 -left-0.5 w-5 h-5 border-b-2 border-l-2 border-white rounded-bl"></div>
                            <div class="absolute -bottom-0.5 -right-0.5 w-5 h-5 border-b-2 border-r-2 border-white rounded-br"></div>
                        </div>
                    </div>

                    <div id="reg-processing"
                         class="absolute inset-0 bg-black/70 hidden flex-col items-center justify-center">
                        <div class="w-12 h-12 border-4 border-white/30 border-t-white rounded-full animate-spin mb-3"></div>
                        <p class="text-white text-sm">Menyimpan data wajah...</p>
                    </div>

                    <div id="reg-camera-off"
                         class="absolute inset-0 bg-gray-900 flex flex-col items-center justify-center">
                        <p class="text-gray-400 text-sm mb-3">Kamera belum aktif</p>
                        <button onclick="startRegCamera()"
                                class="px-4 py-2 rounded-lg text-white text-sm"
                                style="background:#1F4E79">Aktifkan Kamera</button>
                    </div>
                </div>

                <button onclick="captureAndRegister()" id="reg-capture-btn"
                        disabled
                        class="w-full py-3.5 rounded-xl text-white font-semibold text-sm disabled:opacity-40 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                        style="background: linear-gradient(135deg, #1F4E79, #2E75B6)">
                    📸 Ambil Foto & Daftar Wajah
                </button>
            </div>

            {{-- Upload Tab --}}
            <div id="tab-content-upload" class="p-5 hidden">
                <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center mb-4"
                     id="drop-zone">
                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-gray-500 text-sm mb-2">Pilih atau seret foto wajah</p>
                    <p class="text-xs text-gray-400 mb-4">JPEG/PNG, maksimal 5MB</p>
                    <input type="file" id="upload-input" accept="image/jpeg,image/png" class="hidden">
                    <button onclick="document.getElementById('upload-input').click()"
                            class="px-4 py-2 rounded-lg text-sm text-white"
                            style="background:#1F4E79">
                        Pilih File
                    </button>
                </div>

                <div id="upload-preview" class="hidden mb-4">
                    <img id="upload-preview-img" class="w-full rounded-xl max-h-64 object-contain bg-gray-100">
                </div>

                <button onclick="registerFromUpload()" id="upload-register-btn"
                        disabled
                        class="w-full py-3.5 rounded-xl text-white font-semibold text-sm disabled:opacity-40 disabled:cursor-not-allowed"
                        style="background: linear-gradient(135deg, #1F4E79, #2E75B6)">
                    📁 Daftarkan dari Foto
                </button>
            </div>

            {{-- Feedback --}}
            <div id="reg-feedback" class="mx-5 mb-5 hidden px-4 py-3 rounded-xl text-sm text-center font-medium"></div>

            <div class="px-5 pb-4">
                <p class="text-xs text-gray-400">
                    Tips: Pastikan wajah terlihat jelas, pencahayaan cukup, dan tidak ada benda yang menghalangi wajah.
                </p>
            </div>
        </div>
    </div>
</div>

<canvas id="reg-canvas" class="hidden"></canvas>

@endsection

@push('scripts')
<script>
const CSRF       = document.querySelector('meta[name="csrf-token"]').content;
const STUDENT_ID = {{ $student->id }};
let regStream    = null;
let uploadBase64 = null;

// ── Tabs ──────────────────────────────────────────────────────────────────────
function switchTab(tab) {
    ['webcam', 'upload'].forEach(t => {
        document.getElementById(`tab-content-${t}`).classList.toggle('hidden', t !== tab);
        const tabBtn = document.getElementById(`tab-${t}`);
        tabBtn.className = t === tab
            ? 'px-5 py-3 text-sm font-medium border-b-2 border-blue-600 text-blue-600'
            : 'px-5 py-3 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700';
    });
}

// ── Camera ────────────────────────────────────────────────────────────────────
async function startRegCamera() {
    try {
        regStream = await navigator.mediaDevices.getUserMedia({
            video: { facingMode: 'user', width: { ideal: 640 } }
        });
        const video = document.getElementById('reg-camera');
        video.srcObject = regStream;
        await video.play();
        document.getElementById('reg-camera-off').classList.add('hidden');
        document.getElementById('reg-capture-btn').disabled = false;
    } catch (err) {
        showRegFeedback('error', 'Gagal mengakses kamera: ' + err.message);
    }
}

async function captureAndRegister() {
    const video  = document.getElementById('reg-camera');
    const canvas = document.getElementById('reg-canvas');
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    canvas.getContext('2d').drawImage(video, 0, 0);
    const base64 = canvas.toDataURL('image/jpeg', 0.9);
    await doRegister(base64);
}

// ── Upload ─────────────────────────────────────────────────────────────────────
document.getElementById('upload-input').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = (ev) => {
        uploadBase64 = ev.target.result;
        document.getElementById('upload-preview-img').src = uploadBase64;
        document.getElementById('upload-preview').classList.remove('hidden');
        document.getElementById('upload-register-btn').disabled = false;
    };
    reader.readAsDataURL(file);
});

async function registerFromUpload() {
    if (!uploadBase64) return;
    await doRegister(uploadBase64);
}

// ── Core Register ─────────────────────────────────────────────────────────────
async function doRegister(base64Image) {
    document.getElementById('reg-processing').classList.remove('hidden');
    document.getElementById('reg-processing').classList.add('flex');
    hideRegFeedback();

    try {
        const res = await fetch('/api/faces/register', {
            method:  'POST',
            headers: {
                'Content-Type':  'application/json',
                'Accept':        'application/json',
                'X-CSRF-TOKEN':  CSRF,
            },
            body: JSON.stringify({ student_id: STUDENT_ID, image: base64Image }),
        });

        const result = await res.json();

        if (result.success) {
            showRegFeedback('success', `✅ ${result.message}`);
            setTimeout(() => window.location.reload(), 2000);
        } else {
            showRegFeedback('error', `❌ ${result.message}`);
        }
    } catch (err) {
        showRegFeedback('error', '⚠️ Gagal terhubung ke server.');
    } finally {
        document.getElementById('reg-processing').classList.add('hidden');
        document.getElementById('reg-processing').classList.remove('flex');
    }
}

// ── Feedback ──────────────────────────────────────────────────────────────────
function showRegFeedback(type, msg) {
    const el = document.getElementById('reg-feedback');
    el.textContent = msg;
    el.className = `mx-5 mb-5 px-4 py-3 rounded-xl text-sm text-center font-medium ${
        type === 'success' ? 'bg-green-50 border border-green-200 text-green-800'
                          : 'bg-red-50 border border-red-200 text-red-800'
    }`;
    el.classList.remove('hidden');
}

function hideRegFeedback() {
    document.getElementById('reg-feedback').classList.add('hidden');
}

// ── Init ──────────────────────────────────────────────────────────────────────
window.addEventListener('DOMContentLoaded', startRegCamera);
window.addEventListener('beforeunload', () => {
    if (regStream) regStream.getTracks().forEach(t => t.stop());
});
</script>
@endpush
