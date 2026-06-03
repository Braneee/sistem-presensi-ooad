<?php $__env->startSection('title', 'Monitor Sesi'); ?>
<?php $__env->startSection('page-title', 'Monitor Kehadiran Realtime'); ?>
<?php $__env->startSection('page-subtitle', $session->title . ' — ' . $session->classRoom->name); ?>

<?php $__env->startSection('content'); ?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    
    <div class="space-y-4">

        
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-800 text-sm">Info Sesi</h3>
                <span id="session-status-badge"
                      class="px-3 py-1 rounded-full text-xs font-semibold
                             <?php echo e($session->status === 'open' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'); ?>">
                    <?php echo e($session->status === 'open' ? '● Aktif' : '● Tutup'); ?>

                </span>
            </div>

            <dl class="space-y-2.5 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-500">Kelas</dt>
                    <dd class="font-medium text-gray-800"><?php echo e($session->classRoom->name); ?></dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Tanggal</dt>
                    <dd class="font-medium text-gray-800"><?php echo e($session->date->isoFormat('D MMMM Y')); ?></dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Waktu</dt>
                    <dd class="font-medium text-gray-800">
                        <?php echo e(\Carbon\Carbon::parse($session->start_time)->format('H:i')); ?> –
                        <?php echo e(\Carbon\Carbon::parse($session->end_time)->format('H:i')); ?>

                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Kode</dt>
                    <dd class="font-mono text-xs bg-gray-100 px-2 py-0.5 rounded"><?php echo e($session->code); ?></dd>
                </div>
                <?php if($session->notes): ?>
                    <div>
                        <dt class="text-gray-500 mb-1">Catatan</dt>
                        <dd class="text-gray-700 text-xs"><?php echo e($session->notes); ?></dd>
                    </div>
                <?php endif; ?>
            </dl>

            <?php if($session->status === 'open'): ?>
                <div class="mt-4 pt-4 border-t border-gray-100 flex gap-2">
                    <form action="<?php echo e(route('admin.sessions.close', $session)); ?>" method="POST" class="flex-1">
                        <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                        <button type="submit"
                                onclick="return confirm('Tutup sesi ini? Mahasiswa tidak bisa lagi presensi.')"
                                class="w-full py-2 rounded-lg text-xs font-semibold text-white transition-colors"
                                style="background:#C0392B">
                            Tutup Sesi
                        </button>
                    </form>
                    <a href="<?php echo e(route('admin.reports.pdf', $session)); ?>"
                       class="flex-1 py-2 rounded-lg text-xs font-semibold text-center text-white transition-colors"
                       style="background:#1F4E79">
                        PDF
                    </a>
                </div>
            <?php else: ?>
                <div class="mt-4 pt-4 border-t border-gray-100 flex gap-2">
                    <form action="<?php echo e(route('admin.sessions.reopen', $session)); ?>" method="POST" class="flex-1">
                        <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                        <button type="submit"
                                class="w-full py-2 rounded-lg text-xs font-semibold text-white"
                                style="background:#1E7E34">
                            Buka Kembali
                        </button>
                    </form>
                    <a href="<?php echo e(route('admin.reports.pdf', $session)); ?>"
                       class="flex-1 py-2 rounded-lg text-xs font-semibold text-center text-white"
                       style="background:#1F4E79">
                        PDF
                    </a>
                    <a href="<?php echo e(route('admin.reports.csv', $session)); ?>"
                       class="flex-1 py-2 rounded-lg text-xs font-semibold text-center text-white"
                       style="background:#1E7E34">
                        CSV
                    </a>
                </div>
            <?php endif; ?>
        </div>

        
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h3 class="font-semibold text-gray-800 text-sm mb-4 text-center">Progress Kehadiran</h3>

            <div class="flex justify-center mb-4">
                <div class="relative inline-flex items-center justify-center">
                    <svg class="w-32 h-32 -rotate-90" viewBox="0 0 120 120">
                        <circle cx="60" cy="60" r="50" fill="none" stroke="#e5e7eb" stroke-width="10"/>
                        <circle id="progress-ring" cx="60" cy="60" r="50" fill="none"
                                stroke="#1E7E34" stroke-width="10"
                                stroke-linecap="round"
                                stroke-dasharray="314.16"
                                stroke-dashoffset="314.16"
                                style="transition: stroke-dashoffset 0.6s ease"/>
                    </svg>
                    <div class="absolute text-center">
                        <p id="attendance-rate" class="text-2xl font-bold text-gray-800">0%</p>
                        <p class="text-xs text-gray-500">hadir</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-2 text-center">
                <div class="bg-green-50 rounded-lg py-2">
                    <p id="stat-present" class="text-xl font-bold text-green-600">0</p>
                    <p class="text-xs text-green-700">Hadir</p>
                </div>
                <div class="bg-amber-50 rounded-lg py-2">
                    <p id="stat-late" class="text-xl font-bold text-amber-500">0</p>
                    <p class="text-xs text-amber-700">Terlambat</p>
                </div>
                <div class="bg-red-50 rounded-lg py-2">
                    <p id="stat-absent" class="text-xl font-bold text-red-500">0</p>
                    <p class="text-xs text-red-700">Absen</p>
                </div>
            </div>

            <div class="mt-3 text-center">
                <p class="text-xs text-gray-400">
                    Total terdaftar: <span id="stat-total" class="font-semibold text-gray-600">0</span> mahasiswa
                </p>
            </div>
        </div>

    </div>

    
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="font-semibold text-gray-800 text-sm">Daftar Hadir Realtime</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Auto-refresh setiap 5 detik</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                    <span class="text-xs text-green-600 font-medium">Live</span>
                </div>
            </div>

            
            <div class="hidden md:grid grid-cols-12 px-5 py-2 bg-gray-50 border-b border-gray-100 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                <div class="col-span-1">#</div>
                <div class="col-span-4">Mahasiswa</div>
                <div class="col-span-3">NIM</div>
                <div class="col-span-2">Waktu</div>
                <div class="col-span-1 text-center">Status</div>
                <div class="col-span-1 text-right">Akurasi</div>
            </div>

            
            <div id="attendance-list" class="divide-y divide-gray-50 min-h-[200px] max-h-[520px] overflow-y-auto">
                <div id="empty-state" class="py-16 text-center">
                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-gray-400 text-sm">Menunggu kehadiran mahasiswa...</p>
                    <p class="text-gray-300 text-xs mt-1">Data akan muncul otomatis saat mahasiswa presensi</p>
                </div>
            </div>

            
            <div class="px-5 py-3 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                <p class="text-xs text-gray-400">
                    Terakhir update: <span id="last-update">—</span>
                </p>
                <div class="flex gap-3">
                    <a href="<?php echo e(route('admin.reports.pdf', $session)); ?>"
                       class="text-xs text-red-600 hover:text-red-800 font-medium flex items-center gap-1">
                        📄 Export PDF
                    </a>
                    <a href="<?php echo e(route('admin.reports.csv', $session)); ?>"
                       class="text-xs text-green-600 hover:text-green-800 font-medium flex items-center gap-1">
                        📊 Export CSV
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
const SESSION_ID   = <?php echo e($session->id); ?>;
let lastCount      = -1;
let pollingActive  = true;

async function fetchAttendance() {
    if (!pollingActive) return;

    try {
        const res = await fetch(`/api/sessions/${SESSION_ID}/attendance`, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        });
        if (!res.ok) return;

        const data = await res.json();
        if (!data.success) return;

        const s    = data.stats;
        const atts = data.attendances;

        // Update stats
        document.getElementById('stat-present').textContent  = s.present;
        document.getElementById('stat-late').textContent     = s.late;
        document.getElementById('stat-absent').textContent   = s.absent;
        document.getElementById('stat-total').textContent    = s.total;
        document.getElementById('attendance-rate').textContent = s.attendance_rate + '%';

        // Update ring
        const circumference = 314.16;
        const offset = circumference - (circumference * s.attendance_rate / 100);
        document.getElementById('progress-ring').style.strokeDashoffset = offset;

        // Update status badge
        if (data.session.status !== 'open') {
            document.getElementById('session-status-badge').textContent = '● Tutup';
            document.getElementById('session-status-badge').className =
                'px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-500';
        }

        // Update last update time
        document.getElementById('last-update').textContent =
            new Date().toLocaleTimeString('id-ID');

        // Re-render list only when count changes
        if (atts.length !== lastCount) {
            lastCount = atts.length;
            const list = document.getElementById('attendance-list');

            if (atts.length === 0) {
                list.innerHTML = `
                    <div id="empty-state" class="py-16 text-center">
                        <p class="text-gray-400 text-sm">Menunggu kehadiran mahasiswa...</p>
                    </div>`;
            } else {
                list.innerHTML = atts.map((a, i) => `
                    <div class="grid grid-cols-12 px-5 py-3 items-center hover:bg-gray-50 transition-colors">
                        <div class="col-span-1 text-xs text-gray-400">${i + 1}</div>
                        <div class="col-span-4 flex items-center gap-2.5">
                            <div class="w-7 h-7 rounded-full flex-shrink-0 flex items-center justify-center text-white text-xs font-bold"
                                 style="background:${a.status === 'present' ? '#1E7E34' : '#d97706'}">
                                ${a.name.charAt(0).toUpperCase()}
                            </div>
                            <span class="text-sm font-medium text-gray-800 truncate">${a.name}</span>
                        </div>
                        <div class="col-span-3 text-xs text-gray-500 font-mono">${a.nim}</div>
                        <div class="col-span-2 text-xs text-gray-600 font-medium">${a.checked_in}</div>
                        <div class="col-span-1 text-center">
                            <span class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold
                                ${a.status === 'present'
                                    ? 'bg-green-100 text-green-700'
                                    : 'bg-amber-100 text-amber-700'}">
                                ${a.status === 'present' ? 'Hadir' : 'Lambat'}
                            </span>
                        </div>
                        <div class="col-span-1 text-right text-xs text-gray-400">${a.similarity}</div>
                    </div>
                `).join('');
            }
        }

    } catch (err) {
        console.warn('[Monitor] Polling error:', err.message);
    }
}

// Start polling immediately then every 5s
fetchAttendance();
const pollInterval = setInterval(fetchAttendance, 5000);

// Stop polling when navigating away
window.addEventListener('beforeunload', () => {
    pollingActive = false;
    clearInterval(pollInterval);
});

// Pause when tab is hidden, resume when visible (save resources)
document.addEventListener('visibilitychange', () => {
    pollingActive = !document.hidden;
    if (pollingActive) fetchAttendance();
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECT OOAD\attendance-system\laravel\resources\views/admin/sessions/monitor.blade.php ENDPATH**/ ?>