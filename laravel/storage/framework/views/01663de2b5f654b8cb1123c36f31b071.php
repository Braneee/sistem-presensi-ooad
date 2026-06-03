<?php $__env->startSection('title', 'Mahasiswa'); ?>
<?php $__env->startSection('page-title', 'Data Mahasiswa'); ?>
<?php $__env->startSection('page-subtitle', 'Kelola data mahasiswa terdaftar'); ?>

<?php $__env->startSection('content'); ?>

<div class="flex items-center justify-between mb-5">
    <div></div>
    <a href="<?php echo e(route('admin.students.create')); ?>"
       class="px-4 py-2.5 rounded-xl text-white text-sm font-semibold flex items-center gap-2"
       style="background:#1F4E79">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Tambah Mahasiswa
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
        <h3 class="font-semibold text-gray-800 text-sm">Daftar Mahasiswa</h3>
        <span class="text-xs text-gray-400">Total: <?php echo e($students->total()); ?> mahasiswa</span>
    </div>

    <?php if($students->isEmpty()): ?>
        <div class="py-16 text-center text-gray-400 text-sm">
            Belum ada mahasiswa. <a href="<?php echo e(route('admin.students.create')); ?>" class="text-blue-600 hover:underline">Tambah sekarang →</a>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Mahasiswa</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">NIM</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Kelas</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Wajah</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <img src="<?php echo e($student->photo_url); ?>"
                                         alt="<?php echo e($student->name); ?>"
                                         class="w-8 h-8 rounded-full object-cover flex-shrink-0">
                                    <div>
                                        <p class="font-medium text-gray-800"><?php echo e($student->name); ?></p>
                                        <p class="text-xs text-gray-400"><?php echo e($student->email ?? '-'); ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 text-gray-600 font-mono text-xs"><?php echo e($student->nim); ?></td>
                            <td class="px-5 py-3.5 text-gray-600"><?php echo e($student->classRoom->name); ?></td>
                            <td class="px-5 py-3.5 text-center">
                                <?php if($student->hasFaceRegistered()): ?>
                                    <span class="inline-flex items-center gap-1 text-xs text-green-700 bg-green-100 px-2 py-0.5 rounded-full font-medium">
                                        ✓ Terdaftar
                                    </span>
                                <?php else: ?>
                                    <a href="<?php echo e(route('admin.faces.register', $student)); ?>"
                                       class="inline-flex items-center gap-1 text-xs text-amber-700 bg-amber-100 px-2 py-0.5 rounded-full font-medium hover:bg-amber-200 transition-colors">
                                        + Daftar
                                    </a>
                                <?php endif; ?>
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                    <?php echo e($student->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'); ?>">
                                    <?php echo e($student->is_active ? 'Aktif' : 'Nonaktif'); ?>

                                </span>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="<?php echo e(route('admin.students.edit', $student)); ?>"
                                       class="text-xs text-blue-600 hover:text-blue-800 font-medium">Edit</a>
                                    <a href="<?php echo e(route('admin.faces.register', $student)); ?>"
                                       class="text-xs text-purple-600 hover:text-purple-800 font-medium">Wajah</a>
                                    <form action="<?php echo e(route('admin.students.destroy', $student)); ?>" method="POST" class="inline">
                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button type="submit"
                                                onclick="return confirm('Hapus mahasiswa <?php echo e($student->name); ?>?')"
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
            <?php echo e($students->links()); ?>

        </div>
    <?php endif; ?>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECT OOAD\attendance-system\laravel\resources\views/admin/students/index.blade.php ENDPATH**/ ?>