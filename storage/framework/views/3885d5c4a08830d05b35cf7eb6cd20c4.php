<?php $__env->startSection('title', 'Manajemen Anggaran'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Manajemen Anggaran</h1>
            <p class="text-gray-600">Kelola program dan realisasi anggaran desa</p>
        </div>
        <div class="flex space-x-2">
            <a href="<?php echo e(route('anggaran.dashboard')); ?>" 
               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <i class="fas fa-chart-bar mr-2"></i> Dashboard
            </a>
            <a href="<?php echo e(route('anggaran.create')); ?>" 
               class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                <i class="fas fa-plus mr-2"></i> Buat Program
            </a>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-lg">
                    <i class="fas fa-wallet text-blue-600 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-gray-500">Total Anggaran Disahkan</p>
                    <p class="text-2xl font-bold">Rp <?php echo e(number_format($totalAnggaran, 0, ',', '.')); ?></p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-lg">
                    <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-gray-500">Total Terpakai</p>
                    <p class="text-2xl font-bold">Rp <?php echo e(number_format($totalTerpakai, 0, ',', '.')); ?></p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-100 rounded-lg">
                    <i class="fas fa-coins text-yellow-600 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-gray-500">Sisa Anggaran</p>
                    <p class="text-2xl font-bold">Rp <?php echo e(number_format($totalAnggaran - $totalTerpakai, 0, ',', '.')); ?></p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-lg">
                    <i class="fas fa-list-alt text-purple-600 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-gray-500">Total Program</p>
                    <p class="text-2xl font-bold"><?php echo e($totalProgram); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-lg shadow p-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                    <option value="">Semua Status</option>
                    <option value="draft" <?php echo e(request('status') == 'draft' ? 'selected' : ''); ?>>Draft</option>
                    <option value="diajukan" <?php echo e(request('status') == 'diajukan' ? 'selected' : ''); ?>>Diajukan</option>
                    <option value="disahkan" <?php echo e(request('status') == 'disahkan' ? 'selected' : ''); ?>>Disahkan</option>
                    <option value="berjalan" <?php echo e(request('status') == 'berjalan' ? 'selected' : ''); ?>>Berjalan</option>
                    <option value="selesai" <?php echo e(request('status') == 'selesai' ? 'selected' : ''); ?>>Selesai</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                <select name="kategori" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                    <option value="">Semua Kategori</option>
                    <option value="infrastruktur" <?php echo e(request('kategori') == 'infrastruktur' ? 'selected' : ''); ?>>Infrastruktur</option>
                    <option value="sosial" <?php echo e(request('kategori') == 'sosial' ? 'selected' : ''); ?>>Sosial</option>
                    <option value="ekonomi" <?php echo e(request('kategori') == 'ekonomi' ? 'selected' : ''); ?>>Ekonomi</option>
                    <option value="pendidikan" <?php echo e(request('kategori') == 'pendidikan' ? 'selected' : ''); ?>>Pendidikan</option>
                    <option value="kesehatan" <?php echo e(request('kategori') == 'kesehatan' ? 'selected' : ''); ?>>Kesehatan</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tahun</label>
                <select name="tahun" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                    <option value="">Semua Tahun</option>
                    <?php for($i = date('Y'); $i >= 2020; $i--): ?>
                    <option value="<?php echo e($i); ?>" <?php echo e(request('tahun') == $i ? 'selected' : ''); ?>><?php echo e($i); ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Sumber Dana</label>
                <input type="text" name="sumber_dana" 
                       value="<?php echo e(request('sumber_dana')); ?>"
                       placeholder="Cari sumber dana..."
                       class="w-full border border-gray-300 rounded-lg px-4 py-2">
            </div>
            
            <div class="md:col-span-4 flex justify-end space-x-2">
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-filter mr-2"></i> Filter
                </button>
                <a href="<?php echo e(route('anggaran.index')); ?>" 
                   class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Anggaran Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Program</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Anggaran Disahkan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Terpakai</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dibuat Oleh</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php $__empty_1 = true; $__currentLoopData = $anggaran; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="px-6 py-4">
                            <div>
                                <a href="<?php echo e(route('anggaran.show', $item)); ?>" class="font-medium text-blue-600 hover:underline">
                                    <?php echo e($item->nama_program); ?>

                                </a>
                                <p class="text-sm text-gray-500 mt-1"><?php echo e(Str::limit($item->deskripsi, 50)); ?></p>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                <?php echo e(ucfirst($item->kategori)); ?>

                            </span>
                        </td>
                        <td class="px-6 py-4 font-medium">
                            Rp <?php echo e(number_format($item->anggaran_disahkan, 0, ',', '.')); ?>

                        </td>
                        <td class="px-6 py-4">
                            <div>
                                <p class="font-medium">Rp <?php echo e(number_format($item->anggaran_terpakai, 0, ',', '.')); ?></p>
                                <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                                    <div class="bg-green-600 h-2 rounded-full" style="width: <?php echo e($item->persentase_terpakai); ?>%"></div>
                                </div>
                                <p class="text-xs text-gray-500 mt-1"><?php echo e(number_format($item->persentase_terpakai, 1)); ?>%</p>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 text-xs rounded-full bg-<?php echo e($item->status_color); ?>-100 text-<?php echo e($item->status_color); ?>-800">
                                <?php echo e($item->status_label); ?>

                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <img src="<?php echo e($item->user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($item->user->name)); ?>" 
                                     class="w-8 h-8 rounded-full mr-2">
                                <span><?php echo e($item->user->name); ?></span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex space-x-2">
                                <a href="<?php echo e(route('anggaran.show', $item)); ?>" 
                                   class="px-3 py-1 bg-blue-100 text-blue-700 rounded text-sm hover:bg-blue-200">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <?php if($item->status == 'draft'): ?>
                                <a href="<?php echo e(route('anggaran.edit', $item)); ?>" 
                                   class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded text-sm hover:bg-yellow-200">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="<?php echo e(route('anggaran.destroy', $item)); ?>" method="POST" class="inline">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" 
                                            onclick="return confirm('Hapus program anggaran ini?')"
                                            class="px-3 py-1 bg-red-100 text-red-700 rounded text-sm hover:bg-red-200">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                            Belum ada program anggaran
                            <a href="<?php echo e(route('anggaran.create')); ?>" class="text-blue-600 hover:underline block mt-2">
                                Buat program pertama
                            </a>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-6 py-4 border-t">
            <?php echo e($anggaran->links()); ?>

        </div>
    </div>

    <!-- Export Section -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="font-bold text-blue-800">Ekspor Data Anggaran</h3>
                <p class="text-blue-700 mt-1">Download data anggaran lengkap dengan detail</p>
            </div>
            <div class="flex space-x-3">
                <form action="<?php echo e(route('anggaran.export')); ?>" method="GET" class="inline">
                    <input type="hidden" name="export_type" value="excel">
                    <input type="hidden" name="kategori" value="<?php echo e(request('kategori')); ?>">
                    <input type="hidden" name="status" value="<?php echo e(request('status')); ?>">
                    <input type="hidden" name="tahun" value="<?php echo e(request('tahun')); ?>">
                    <button type="submit" 
                            class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 flex items-center">
                        <i class="fas fa-file-excel mr-2"></i> Excel
                    </button>
                </form>
                <form action="<?php echo e(route('anggaran.export')); ?>" method="GET" class="inline">
                    <input type="hidden" name="export_type" value="pdf">
                    <input type="hidden" name="kategori" value="<?php echo e(request('kategori')); ?>">
                    <input type="hidden" name="status" value="<?php echo e(request('status')); ?>">
                    <input type="hidden" name="tahun" value="<?php echo e(request('tahun')); ?>">
                    <button type="submit" 
                            class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 flex items-center">
                        <i class="fas fa-file-pdf mr-2"></i> PDF
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/naufal/sigap-desa/resources/views/anggaran/index.blade.php ENDPATH**/ ?>