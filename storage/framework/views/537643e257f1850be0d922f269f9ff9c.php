<?php $__env->startSection('title', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Header dengan role-based title -->
    <div>
        <h1 class="text-3xl font-bold text-gray-900">
            Dashboard <?php echo e(auth()->user()->isAdmin() ? 'Admin' : 'Warga'); ?>

        </h1>
        <p class="text-gray-600">
            <?php echo e(auth()->user()->isAdmin() ? 'Kelola laporan dan anggaran desa' : 'Pantau laporan dan perkembangan desa'); ?>

        </p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-lg">
                    <i class="fas fa-file-alt text-blue-600 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-gray-500">Total Laporan</p>
                    <p class="text-3xl font-bold"><?php echo e($stats['total_laporan']); ?></p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-100 rounded-lg">
                    <i class="fas fa-clock text-yellow-600 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-gray-500">Menunggu Verifikasi</p>
                    <p class="text-3xl font-bold"><?php echo e($stats['laporan_menunggu']); ?></p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-orange-100 rounded-lg">
                    <i class="fas fa-tools text-orange-600 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-gray-500">Sedang Dikerjakan</p>
                    <p class="text-3xl font-bold"><?php echo e($stats['laporan_dikerjakan']); ?></p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-lg">
                    <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-gray-500">Selesai</p>
                    <p class="text-3xl font-bold"><?php echo e($stats['laporan_selesai']); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions dengan role-based -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <?php if(auth()->user()->isAdmin()): ?>
        <!-- Admin Actions -->
        <a href="<?php echo e(route('laporan.index')); ?>" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
            <div class="text-center">
                <div class="p-3 bg-blue-600 rounded-full inline-block">
                    <i class="fas fa-tasks text-white text-2xl"></i>
                </div>
                <h3 class="mt-4 font-bold text-lg">Tanggapi Laporan</h3>
                <p class="text-gray-600 mt-2">Kelola dan verifikasi laporan dari warga</p>
            </div>
        </a>
        
        <a href="<?php echo e(route('anggaran.index')); ?>" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
            <div class="text-center">
                <div class="p-3 bg-green-600 rounded-full inline-block">
                    <i class="fas fa-coins text-white text-2xl"></i>
                </div>
                <h3 class="mt-4 font-bold text-lg">Masukkan Anggaran</h3>
                <p class="text-gray-600 mt-2">Kelola program dan realisasi anggaran desa</p>
            </div>
        </a>
        
        <a href="<?php echo e(route('peta')); ?>" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
            <div class="text-center">
                <div class="p-3 bg-purple-600 rounded-full inline-block">
                    <i class="fas fa-map-marked-alt text-white text-2xl"></i>
                </div>
                <h3 class="mt-4 font-bold text-lg">Monitor Peta</h3>
                <p class="text-gray-600 mt-2">Pantau lokasi laporan di peta interaktif</p>
            </div>
        </a>
        <?php else: ?>
        <!-- Warga Actions -->
        <a href="<?php echo e(route('laporan.create')); ?>" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
            <div class="text-center">
                <div class="p-3 bg-blue-600 rounded-full inline-block">
                    <i class="fas fa-plus text-white text-2xl"></i>
                </div>
                <h3 class="mt-4 font-bold text-lg">Buat Laporan Baru</h3>
                <p class="text-gray-600 mt-2">Laporkan masalah infrastruktur atau lingkungan</p>
            </div>
        </a>
        
        <a href="<?php echo e(route('peta')); ?>" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
            <div class="text-center">
                <div class="p-3 bg-green-600 rounded-full inline-block">
                    <i class="fas fa-map-marked-alt text-white text-2xl"></i>
                </div>
                <h3 class="mt-4 font-bold text-lg">Lihat Peta Interaktif</h3>
                <p class="text-gray-600 mt-2">Pantau laporan dan progres perbaikan</p>
            </div>
        </a>
        
        <a href="<?php echo e(route('transparansi.index')); ?>" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
            <div class="text-center">
                <div class="p-3 bg-purple-600 rounded-full inline-block">
                    <i class="fas fa-chart-pie text-white text-2xl"></i>
                </div>
                <h3 class="mt-4 font-bold text-lg">Transparansi Anggaran</h3>
                <p class="text-gray-600 mt-2">Lihat penggunaan dana desa secara transparan</p>
            </div>
        </a>
        <?php endif; ?>
    </div>

    <!-- Recent Reports -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b flex justify-between items-center">
            <h2 class="text-xl font-bold">Laporan Terbaru</h2>
            <?php if(auth()->user()->isAdmin()): ?>
            <a href="<?php echo e(route('laporan.index')); ?>" class="text-blue-600 hover:underline text-sm">
                Lihat Semua →
            </a>
            <?php endif; ?>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. Tiket</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Judul</th>
                        <?php if(auth()->user()->isAdmin()): ?>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pelapor</th>
                        <?php endif; ?>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php $__empty_1 = true; $__currentLoopData = $laporan_terbaru; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="px-6 py-4">
                            <span class="font-mono text-sm"><?php echo e($item->nomor_tiket); ?></span>
                        </td>
                        <td class="px-6 py-4">
                            <a href="<?php echo e(route('laporan.show', $item)); ?>" class="text-blue-600 hover:underline">
                                <?php echo e(Str::limit($item->judul, 40)); ?>

                            </a>
                        </td>
                        <?php if(auth()->user()->isAdmin()): ?>
                        <td class="px-6 py-4"><?php echo e($item->user->name); ?></td>
                        <?php endif; ?>
                        <td class="px-6 py-4">
                            <span class="status-badge status-<?php echo e($item->status); ?>">
                                <?php echo e($item->status_label); ?>

                            </span>
                        </td>
                        <td class="px-6 py-4"><?php echo e($item->created_at->format('d/m/Y')); ?></td>
                        <td class="px-6 py-4">
                            <a href="<?php echo e(route('laporan.show', $item)); ?>" 
                               class="px-3 py-1 bg-blue-100 text-blue-700 rounded text-sm hover:bg-blue-200">
                                Lihat
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="<?php echo e(auth()->user()->isAdmin() ? '6' : '5'); ?>" class="px-6 py-4 text-center text-gray-500">
                            Belum ada laporan
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Additional Info untuk Admin -->
    <?php if(auth()->user()->isAdmin()): ?>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Anggaran Summary -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4">Ringkasan Anggaran</h2>
            <?php
                $totalAnggaran = \App\Models\Anggaran::sum('anggaran_disahkan');
                $totalTerpakai = \App\Models\Anggaran::sum('anggaran_terpakai');
                $totalSisa = $totalAnggaran - $totalTerpakai;
                $persentase = $totalAnggaran > 0 ? ($totalTerpakai / $totalAnggaran) * 100 : 0;
            ?>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Total Anggaran</span>
                    <span class="font-bold">Rp <?php echo e(number_format($totalAnggaran, 0, ',', '.')); ?></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Total Terpakai</span>
                    <span class="font-bold text-green-600">Rp <?php echo e(number_format($totalTerpakai, 0, ',', '.')); ?></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Sisa Anggaran</span>
                    <span class="font-bold text-blue-600">Rp <?php echo e(number_format($totalSisa, 0, ',', '.')); ?></span>
                </div>
                <div>
                    <div class="flex justify-between mb-1">
                        <span class="text-sm text-gray-600">Penggunaan</span>
                        <span class="text-sm font-medium"><?php echo e(number_format($persentase, 1)); ?>%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-600 h-2 rounded-full" style="width: <?php echo e($persentase); ?>%"></div>
                    </div>
                </div>
                <div class="pt-4 border-t">
                    <a href="<?php echo e(route('anggaran.index')); ?>" class="text-blue-600 hover:underline text-sm">
                        <i class="fas fa-arrow-right mr-1"></i> Kelola Anggaran Lengkap
                    </a>
                </div>
            </div>
        </div>

        <!-- Quick Admin Actions -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4">Aksi Cepat</h2>
            <div class="space-y-3">
                <a href="<?php echo e(route('faq.index')); ?>" 
                   class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-question-circle text-blue-600"></i>
                    </div>
                    <div>
                        <p class="font-medium">Kelola FAQ Chatbot</p>
                        <p class="text-sm text-gray-500">Update pertanyaan umum</p>
                    </div>
                </a>
                
                <a href="<?php echo e(route('laporan.index')); ?>?status=menunggu" 
                   class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                    <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-clock text-yellow-600"></i>
                    </div>
                    <div>
                        <p class="font-medium">Verifikasi Laporan Menunggu</p>
                        <p class="text-sm text-gray-500"><?php echo e($stats['laporan_menunggu']); ?> laporan</p>
                    </div>
                </a>
                
                <a href="<?php echo e(route('anggaran.create')); ?>" 
                   class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-plus-circle text-green-600"></i>
                    </div>
                    <div>
                        <p class="font-medium">Buat Program Anggaran Baru</p>
                        <p class="text-sm text-gray-500">Rencanakan anggaran baru</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/naufal/sigap-desa/resources/views/dashboard.blade.php ENDPATH**/ ?>