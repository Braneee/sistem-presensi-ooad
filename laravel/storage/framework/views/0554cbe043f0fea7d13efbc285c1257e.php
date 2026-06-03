<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'Admin'); ?> — Sistem Presensi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary:   '#1F4E79',
                        secondary: '#2E75B6',
                        success:   '#1E7E34',
                        danger:    '#C0392B',
                    }
                }
            }
        }
    </script>
    <style type="text/tailwindcss">
        [x-cloak] { display: none !important; }
        .sidebar-link { @apply flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-all; }
        .sidebar-link.active { background: rgba(255,255,255,0.2); @apply text-white font-semibold; }
        .sidebar-link:not(.active) { @apply text-blue-100 hover:text-white; background: transparent; }
        .sidebar-link:not(.active):hover { background: rgba(255,255,255,0.1); }
    </style>
</head>
<body class="bg-gray-50 font-sans antialiased">

    <!-- Sidebar -->
    <aside class="fixed inset-y-0 left-0 w-64 z-30 shadow-xl flex flex-col"
           style="background: linear-gradient(180deg, #1F4E79 0%, #2E75B6 100%)">

        <!-- Logo -->
        <div class="flex items-center gap-3 px-6 py-5 border-b border-white/20 flex-shrink-0">
            <div class="w-9 h-9 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-white font-bold text-sm leading-tight">Sistem Presensi</h1>
                <p class="text-blue-200 text-xs">Face Recognition</p>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="px-3 py-4 space-y-0.5 flex-1 overflow-y-auto">

            <p class="px-3 pt-2 pb-1 text-blue-300 text-xs font-semibold uppercase tracking-wider">Menu Utama</p>

            <a href="<?php echo e(route('admin.dashboard')); ?>"
               class="sidebar-link <?php echo e(request()->routeIs('admin.dashboard') ? 'active' : ''); ?>">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Dashboard
            </a>

            <a href="<?php echo e(route('admin.sessions.index')); ?>"
               class="sidebar-link <?php echo e(request()->routeIs('admin.sessions*') ? 'active' : ''); ?>">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Sesi Presensi
            </a>

            <a href="<?php echo e(route('admin.attendance.index')); ?>"
               class="sidebar-link <?php echo e(request()->routeIs('admin.attendance*') ? 'active' : ''); ?>">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
                Data Kehadiran
            </a>

            <p class="px-3 pt-4 pb-1 text-blue-300 text-xs font-semibold uppercase tracking-wider">Data Master</p>

            <a href="<?php echo e(route('admin.students.index')); ?>"
               class="sidebar-link <?php echo e(request()->routeIs('admin.students*') ? 'active' : ''); ?>">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Mahasiswa
            </a>

            <a href="<?php echo e(route('admin.faces.index')); ?>"
               class="sidebar-link <?php echo e(request()->routeIs('admin.faces*') ? 'active' : ''); ?>">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Registrasi Wajah
            </a>

            <a href="<?php echo e(route('admin.reports.index')); ?>"
               class="sidebar-link <?php echo e(request()->routeIs('admin.reports*') ? 'active' : ''); ?>">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Laporan
            </a>

            <!-- Quick link ke halaman presensi publik -->
            <div class="pt-4">
                <a href="<?php echo e(route('attendance.index')); ?>" target="_blank"
                   class="sidebar-link border border-white/20">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                    Halaman Presensi
                </a>
            </div>
        </nav>

        <!-- User Info & Logout -->
        <div class="p-4 border-t border-white/20 flex-shrink-0">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center flex-shrink-0">
                    <span class="text-white text-sm font-bold">
                        <?php echo e(strtoupper(substr(auth()->user()->name, 0, 1))); ?>

                    </span>
                </div>
                <div class="min-w-0">
                    <p class="text-white text-xs font-medium truncate"><?php echo e(auth()->user()->name); ?></p>
                    <p class="text-blue-200 text-xs"><?php echo e(ucfirst(auth()->user()->role)); ?></p>
                </div>
            </div>
            <form action="<?php echo e(route('logout')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <button type="submit"
                        class="w-full text-center text-xs text-blue-200 hover:text-white transition-colors py-1.5 rounded hover:bg-white/10">
                    Keluar dari Sistem
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="ml-64 min-h-screen flex flex-col">

        <!-- Top Bar -->
        <header class="bg-white border-b border-gray-200 sticky top-0 z-20">
            <div class="flex items-center justify-between px-6 py-3">
                <div>
                    <h2 class="text-base font-semibold text-gray-800"><?php echo $__env->yieldContent('page-title', 'Dashboard'); ?></h2>
                    <p class="text-xs text-gray-500"><?php echo $__env->yieldContent('page-subtitle', ''); ?></p>
                </div>
                <div class="flex items-center gap-4">
                    <!-- Flask/AI Status -->
                    <div id="flask-status-badge"
                         class="flex items-center gap-1.5 text-xs px-3 py-1.5 rounded-full bg-gray-100 border border-gray-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400 animate-pulse" id="status-dot"></span>
                        <span id="status-text" class="text-gray-500">Memeriksa AI...</span>
                    </div>
                    <div class="text-xs text-gray-500">
                        <?php echo e(now()->isoFormat('dddd, D MMMM Y')); ?>

                    </div>
                </div>
            </div>
        </header>

        <!-- Flash Messages -->
        <div class="px-6 pt-5">
            <?php if(session('success')): ?>
                <div class="mb-0 flex items-center gap-2 px-4 py-3 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm">
                    <svg class="w-4 h-4 flex-shrink-0 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <?php echo e(session('success')); ?>

                </div>
            <?php endif; ?>
            <?php if(session('error')): ?>
                <div class="mb-0 flex items-center gap-2 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm">
                    <svg class="w-4 h-4 flex-shrink-0 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <?php echo e(session('error')); ?>

                </div>
            <?php endif; ?>
        </div>

        <!-- Page Content -->
        <main class="flex-1 p-6">
            <?php echo $__env->yieldContent('content'); ?>
        </main>

        <footer class="px-6 py-3 border-t border-gray-100 text-xs text-gray-400 text-center">
            Sistem Presensi Face Recognition &copy; <?php echo e(date('Y')); ?>

        </footer>
    </div>

    <script>
        async function checkFlaskStatus() {
            try {
                const res  = await fetch('/api/dashboard/stats', {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                });
                const data = await res.json();
                const ok   = data?.data?.flask_status === 'online';
                document.getElementById('status-dot').className =
                    `w-1.5 h-1.5 rounded-full ${ok ? 'bg-green-500' : 'bg-red-500 animate-pulse'}`;
                document.getElementById('status-text').textContent  = ok ? 'AI Online' : 'AI Offline';
                document.getElementById('flask-status-badge').className =
                    `flex items-center gap-1.5 text-xs px-3 py-1.5 rounded-full border ${
                        ok ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200'
                    }`;
            } catch {
                document.getElementById('status-dot').className = 'w-1.5 h-1.5 rounded-full bg-red-500';
                document.getElementById('status-text').textContent = 'AI Offline';
            }
        }
        checkFlaskStatus();
        setInterval(checkFlaskStatus, 30000);
    </script>

    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH D:\PROJECT OOAD\attendance-system\laravel\resources\views/layouts/admin.blade.php ENDPATH**/ ?>