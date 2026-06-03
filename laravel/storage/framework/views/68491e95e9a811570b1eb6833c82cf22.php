<?php $__env->startSection('title', 'Registrasi Wajah'); ?>
<?php $__env->startSection('page-title', 'Registrasi Wajah'); ?>
<?php $__env->startSection('page-subtitle', 'Status pendaftaran wajah seluruh mahasiswa'); ?>

<?php $__env->startSection('content'); ?>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
        <h3 class="font-semibold text-gray-800 text-sm">Status Wajah Mahasiswa</h3>
        <div class="flex items-center gap-3 text-xs text-gray-500">
            <span class="flex items-center gap-1.5">
                <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                Terdaftar: <?php echo e($students->getCollection()->filter(fn($s) => $s->faces_count > 0)->count()); ?>

            </span>
            <span class="flex items-center gap-1.5">
                <span class="w-2 h-2 bg-amber-400 rounded-full"></span>
                Belum: <?php echo e($students->getCollection()->filter(fn($s) => $s->faces_count === 0)->count()); ?>

            </span>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Mahasiswa</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">NIM</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Kelas</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Status Wajah</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-3">
                                <img src="<?php echo e($student->photo_url); ?>" alt="<?php echo e($student->name); ?>"
                                     class="w-8 h-8 rounded-full object-cover">
                                <p class="font-medium text-gray-800"><?php echo e($student->name); ?></p>
                            </div>
                        </td>
                        <td class="px-5 py-3.5 text-gray-500 font-mono text-xs"><?php echo e($student->nim); ?></td>
                        <td class="px-5 py-3.5 text-gray-600"><?php echo e($student->classRoom->name); ?></td>
                        <td class="px-5 py-3.5 text-center">
                            <?php if($student->faces_count > 0): ?>
                                <span class="inline-flex items-center gap-1 text-xs bg-green-100 text-green-700 px-3 py-1 rounded-full font-semibold">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    Terdaftar
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center gap-1 text-xs bg-amber-100 text-amber-700 px-3 py-1 rounded-full font-semibold">
                                    ⚠ Belum Terdaftar
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            <a href="<?php echo e(route('admin.faces.register', $student)); ?>"
                               class="inline-flex items-center gap-1 text-xs px-3 py-1.5 rounded-lg font-medium text-white"
                               style="background:<?php echo e($student->faces_count > 0 ? '#2E75B6' : '#1F4E79'); ?>">
                                <?php echo e($student->faces_count > 0 ? '🔄 Perbarui Wajah' : '📸 Daftarkan Wajah'); ?>

                            </a>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>

    <div class="px-5 py-4 border-t border-gray-100">
        <?php echo e($students->links()); ?>

    </div>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECT OOAD\attendance-system\laravel\resources\views/admin/faces/index.blade.php ENDPATH**/ ?>