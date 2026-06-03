<?php $__env->startSection('title', 'Laporan'); ?>
<?php $__env->startSection('page-title', 'Laporan Presensi'); ?>
<?php $__env->startSection('page-subtitle', 'Export dan unduh laporan kehadiran'); ?>

<?php $__env->startSection('content'); ?>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100">
        <h3 class="font-semibold text-gray-800 text-sm">Semua Sesi — Pilih untuk Export</h3>
    </div>

    <?php if($sessions->isEmpty()): ?>
        <div class="py-16 text-center text-gray-400 text-sm">
            Belum ada sesi presensi.
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Sesi</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Kelas</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tanggal</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Kehadiran</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Export</th>
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
                            <td class="px-5 py-3.5 text-center">
                                <div class="font-semibold text-gray-800"><?php echo e($session->presentCount()); ?></div>
                                <div class="text-xs text-gray-400"><?php echo e($session->attendanceRate()); ?>%</div>
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold
                                    <?php echo e($session->status === 'open' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'); ?>">
                                    <?php echo e($session->status === 'open' ? 'Aktif' : 'Tutup'); ?>

                                </span>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="<?php echo e(route('admin.reports.pdf', $session)); ?>"
                                       class="px-3 py-1.5 text-xs rounded-lg text-white font-medium"
                                       style="background:#C0392B">
                                        📄 PDF
                                    </a>
                                    <a href="<?php echo e(route('admin.reports.csv', $session)); ?>"
                                       class="px-3 py-1.5 text-xs rounded-lg text-white font-medium"
                                       style="background:#1E7E34">
                                        📊 CSV
                                    </a>
                                    <a href="<?php echo e(route('admin.sessions.monitor', $session)); ?>"
                                       class="px-3 py-1.5 text-xs rounded-lg text-gray-600 font-medium bg-gray-100 hover:bg-gray-200">
                                        Monitor
                                    </a>
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

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECT OOAD\attendance-system\laravel\resources\views/admin/reports/index.blade.php ENDPATH**/ ?>