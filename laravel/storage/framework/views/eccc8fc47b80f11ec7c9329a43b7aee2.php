<?php $__env->startSection('title', 'Sesi Presensi'); ?>
<?php $__env->startSection('page-title', 'Sesi Presensi'); ?>
<?php $__env->startSection('page-subtitle', 'Kelola sesi kehadiran kelas'); ?>

<?php $__env->startSection('content'); ?>

<div class="flex items-center justify-between mb-5">
    <div></div>
    <a href="<?php echo e(route('admin.sessions.create')); ?>"
       class="px-4 py-2.5 rounded-xl text-white text-sm font-semibold flex items-center gap-2"
       style="background:#1F4E79">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Buat Sesi Baru
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100">
        <h3 class="font-semibold text-gray-800 text-sm">Semua Sesi</h3>
    </div>

    <?php if($sessions->isEmpty()): ?>
        <div class="py-16 text-center text-gray-400 text-sm">
            Belum ada sesi presensi. <a href="<?php echo e(route('admin.sessions.create')); ?>" class="text-blue-600 hover:underline">Buat sekarang →</a>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Sesi</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Kelas</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tanggal</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Waktu</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Hadir</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php $__currentLoopData = $sessions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $session): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-5 py-3.5">
                                <p class="font-medium text-gray-800"><?php echo e($session->title); ?></p>
                                <p class="text-xs text-gray-400 font-mono"><?php echo e($session->code); ?></p>
                            </td>
                            <td class="px-5 py-3.5 text-gray-600"><?php echo e($session->classRoom->name); ?></td>
                            <td class="px-5 py-3.5 text-gray-600"><?php echo e($session->date->format('d/m/Y')); ?></td>
                            <td class="px-5 py-3.5 text-gray-600 text-xs">
                                <?php echo e(\Carbon\Carbon::parse($session->start_time)->format('H:i')); ?>–<?php echo e(\Carbon\Carbon::parse($session->end_time)->format('H:i')); ?>

                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <span class="font-semibold text-gray-800"><?php echo e($session->presentCount()); ?></span>
                                <span class="text-gray-400 text-xs"> mhs</span>
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold
                                    <?php if($session->status === 'open'): ?> bg-green-100 text-green-700
                                    <?php elseif($session->status === 'closed'): ?> bg-gray-100 text-gray-500
                                    <?php else: ?> bg-red-100 text-red-600 <?php endif; ?>">
                                    <?php echo e(['open'=>'Aktif','closed'=>'Tutup','cancelled'=>'Batal'][$session->status] ?? $session->status); ?>

                                </span>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="<?php echo e(route('admin.sessions.monitor', $session)); ?>"
                                       class="text-xs text-blue-600 hover:text-blue-800 font-medium">Monitor</a>

                                    <?php if($session->status === 'open'): ?>
                                        <form action="<?php echo e(route('admin.sessions.close', $session)); ?>" method="POST" class="inline">
                                            <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                            <button type="submit" class="text-xs text-amber-600 hover:text-amber-800 font-medium">Tutup</button>
                                        </form>
                                    <?php else: ?>
                                        <form action="<?php echo e(route('admin.sessions.reopen', $session)); ?>" method="POST" class="inline">
                                            <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                            <button type="submit" class="text-xs text-green-600 hover:text-green-800 font-medium">Buka</button>
                                        </form>
                                    <?php endif; ?>

                                    <form action="<?php echo e(route('admin.sessions.destroy', $session)); ?>" method="POST" class="inline">
                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button type="submit"
                                                onclick="return confirm('Hapus sesi ini? Data kehadiran akan ikut terhapus.')"
                                                class="text-xs text-red-500 hover:text-red-700 font-medium">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>

        <div class="px-5 py-4 border-t border-gray-100">
            <?php echo e($sessions->links()); ?>

        </div>
    <?php endif; ?>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECT OOAD\attendance-system\laravel\resources\views/admin/sessions/index.blade.php ENDPATH**/ ?>