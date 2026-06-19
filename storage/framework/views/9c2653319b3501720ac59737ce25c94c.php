<?php $__env->startSection('title', 'Buat Program Anggaran'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-6xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Buat Program Anggaran Baru</h1>
        <p class="text-gray-600">Buat program anggaran dengan rincian detail yang lengkap</p>
    </div>

    <form action="<?php echo e(route('anggaran.store')); ?>" method="POST" id="anggaranForm">
        <?php echo csrf_field(); ?>
        
        <div class="space-y-8">
            <!-- Program Information -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4">Informasi Program</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Program *
                        </label>
                        <input type="text" name="nama_program" 
                               value="<?php echo e(old('nama_program')); ?>"
                               placeholder="Contoh: Perbaikan Jalan Desa"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Kategori *
                        </label>
                        <select name="kategori" 
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                required>
                            <option value="">Pilih Kategori</option>
                            <option value="infrastruktur" <?php echo e(old('kategori') == 'infrastruktur' ? 'selected' : ''); ?>>Infrastruktur</option>
                            <option value="sosial" <?php echo e(old('kategori') == 'sosial' ? 'selected' : ''); ?>>Sosial</option>
                            <option value="ekonomi" <?php echo e(old('kategori') == 'ekonomi' ? 'selected' : ''); ?>>Ekonomi</option>
                            <option value="pendidikan" <?php echo e(old('kategori') == 'pendidikan' ? 'selected' : ''); ?>>Pendidikan</option>
                            <option value="kesehatan" <?php echo e(old('kategori') == 'kesehatan' ? 'selected' : ''); ?>>Kesehatan</option>
                            <option value="lainnya" <?php echo e(old('kategori') == 'lainnya' ? 'selected' : ''); ?>>Lainnya</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Sumber Dana *
                        </label>
                        <select name="sumber_dana" 
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                required>
                            <option value="">Pilih Sumber Dana</option>
                            <option value="dana_desa" <?php echo e(old('sumber_dana') == 'dana_desa' ? 'selected' : ''); ?>>Dana Desa</option>
                            <option value="apbd_provinsi" <?php echo e(old('sumber_dana') == 'apbd_provinsi' ? 'selected' : ''); ?>>APBD Provinsi</option>
                            <option value="apbd_kabupaten" <?php echo e(old('sumber_dana') == 'apbd_kabupaten' ? 'selected' : ''); ?>>APBD Kabupaten</option>
                            <option value="bansos" <?php echo e(old('sumber_dana') == 'bansos' ? 'selected' : ''); ?>>Bantuan Sosial</option>
                            <option value="swadaya" <?php echo e(old('sumber_dana') == 'swadaya' ? 'selected' : ''); ?>>Swadaya Masyarakat</option>
                            <option value="lainnya" <?php echo e(old('sumber_dana') == 'lainnya' ? 'selected' : ''); ?>>Lainnya</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Anggaran Diajukan (Rp)
                        </label>
                        <input type="text" id="anggaran_diajukan_display" 
                               placeholder="0"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50"
                               readonly>
                        <input type="hidden" name="anggaran_diajukan" id="anggaran_diajukan" value="0">
                        <p class="text-sm text-gray-500 mt-1" id="total_anggaran_text">Total: Rp 0</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Tanggal Mulai
                        </label>
                        <input type="date" name="tanggal_mulai" 
                               value="<?php echo e(old('tanggal_mulai')); ?>"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Tanggal Selesai
                        </label>
                        <input type="date" name="tanggal_selesai" 
                               value="<?php echo e(old('tanggal_selesai')); ?>"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Deskripsi Program *
                    </label>
                    <textarea name="deskripsi" rows="3" 
                              placeholder="Deskripsikan program anggaran secara lengkap..."
                              class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              required><?php echo e(old('deskripsi')); ?></textarea>
                </div>
                
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Catatan (Opsional)
                    </label>
                    <textarea name="catatan" rows="2" 
                              placeholder="Tambahkan catatan jika diperlukan..."
                              class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"><?php echo e(old('catatan')); ?></textarea>
                </div>
            </div>
            
            <!-- Detail Anggaran -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold">Detail Rencana Anggaran</h2>
                    <button type="button" onclick="addDetailRow()" 
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        <i class="fas fa-plus mr-2"></i> Tambah Item
                    </button>
                </div>
                
                <div id="detail-container" class="space-y-4">
                    <!-- Detail rows will be added here -->
                    <div class="detail-row border border-gray-200 rounded-lg p-4">
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Item *
                                </label>
                                <input type="text" name="details[0][item]" 
                                       placeholder="Contoh: Material Pasir"
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2 detail-item"
                                       required>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Deskripsi *
                                </label>
                                <input type="text" name="details[0][deskripsi]" 
                                       placeholder="Deskripsi item"
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2"
                                       required>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Jumlah *
                                </label>
                                <input type="number" name="details[0][jumlah]" 
                                       placeholder="0"
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2 detail-jumlah"
                                       min="0" step="0.01" required>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Harga Satuan (Rp) *
                                </label>
                                <input type="number" name="details[0][harga_satuan]" 
                                       placeholder="0"
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2 detail-harga"
                                       min="0" step="1" required>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Subtotal (Rp)
                                </label>
                                <input type="text" 
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 detail-subtotal"
                                       value="0" readonly>
                                <button type="button" onclick="removeDetailRow(this)" 
                                        class="mt-2 text-red-600 hover:text-red-800 text-sm">
                                    <i class="fas fa-trash mr-1"></i> Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Summary -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="font-bold text-lg">Ringkasan Anggaran</h3>
                            <p class="text-sm text-gray-600" id="total-items">0 item</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-600">Total Rencana Anggaran</p>
                            <p class="text-2xl font-bold text-green-600" id="total-anggaran">Rp 0</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Actions -->
            <div class="flex justify-between">
                <a href="<?php echo e(route('anggaran.index')); ?>" 
                   class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </a>
                
                <div class="space-x-3">
                    <button type="button" onclick="saveAsDraft()" 
                            class="px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                        <i class="fas fa-save mr-2"></i> Simpan Draft
                    </button>
                    <button type="submit" 
                            class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <i class="fas fa-paper-plane mr-2"></i> Simpan & Ajukan
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    let detailCount = 1;
    
    function formatRupiah(angka) {
        return 'Rp ' + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }
    
    function calculateSubtotal(row) {
        const jumlah = parseFloat(row.querySelector('.detail-jumlah').value) || 0;
        const harga = parseFloat(row.querySelector('.detail-harga').value) || 0;
        const subtotal = jumlah * harga;
        
        row.querySelector('.detail-subtotal').value = formatRupiah(subtotal.toFixed(0));
        
        updateTotalAnggaran();
        return subtotal;
    }
    
    function updateTotalAnggaran() {
        let total = 0;
        let itemCount = 0;
        
        document.querySelectorAll('.detail-row').forEach(row => {
            const jumlah = parseFloat(row.querySelector('.detail-jumlah').value) || 0;
            const harga = parseFloat(row.querySelector('.detail-harga').value) || 0;
            total += jumlah * harga;
            itemCount++;
        });
        
        document.getElementById('total-anggaran').textContent = formatRupiah(total.toFixed(0));
        document.getElementById('total-items').textContent = itemCount + ' item';
        document.getElementById('anggaran_diajukan_display').value = formatRupiah(total.toFixed(0));
        document.getElementById('anggaran_diajukan').value = total;
        
        // Update total anggaran text
        document.getElementById('total_anggaran_text').textContent = 'Total: ' + formatRupiah(total.toFixed(0));
    }
    
    function addDetailRow() {
        const container = document.getElementById('detail-container');
        const newRow = document.createElement('div');
        newRow.className = 'detail-row border border-gray-200 rounded-lg p-4';
        newRow.innerHTML = `
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Item *
                    </label>
                    <input type="text" name="details[${detailCount}][item]" 
                           placeholder="Contoh: Material Pasir"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 detail-item"
                           required>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Deskripsi *
                    </label>
                    <input type="text" name="details[${detailCount}][deskripsi]" 
                           placeholder="Deskripsi item"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2"
                           required>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Jumlah *
                    </label>
                    <input type="number" name="details[${detailCount}][jumlah]" 
                           placeholder="0"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 detail-jumlah"
                           min="0" step="0.01" required>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Harga Satuan (Rp) *
                    </label>
                    <input type="number" name="details[${detailCount}][harga_satuan]" 
                           placeholder="0"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 detail-harga"
                           min="0" step="1" required>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Subtotal (Rp)
                    </label>
                    <input type="text" 
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 detail-subtotal"
                           value="Rp 0" readonly>
                    <button type="button" onclick="removeDetailRow(this)" 
                            class="mt-2 text-red-600 hover:text-red-800 text-sm">
                        <i class="fas fa-trash mr-1"></i> Hapus
                    </button>
                </div>
            </div>
        `;
        
        container.appendChild(newRow);
        
        // Add event listeners for calculation
        const jumlahInput = newRow.querySelector('.detail-jumlah');
        const hargaInput = newRow.querySelector('.detail-harga');
        
        jumlahInput.addEventListener('input', () => calculateSubtotal(newRow));
        hargaInput.addEventListener('input', () => calculateSubtotal(newRow));
        
        detailCount++;
        
        // Initial calculation
        calculateSubtotal(newRow);
    }
    
    function removeDetailRow(button) {
        const row = button.closest('.detail-row');
        if (document.querySelectorAll('.detail-row').length > 1) {
            row.remove();
            updateTotalAnggaran();
        } else {
            alert('Minimal harus ada 1 item anggaran');
        }
    }
    
    function saveAsDraft() {
        // Add hidden input for draft status
        const form = document.getElementById('anggaranForm');
        const draftInput = document.createElement('input');
        draftInput.type = 'hidden';
        draftInput.name = 'draft';
        draftInput.value = 'true';
        form.appendChild(draftInput);
        
        // Submit form
        form.submit();
    }
    
    // Add event listeners to existing inputs
    document.addEventListener('DOMContentLoaded', function() {
        const firstRow = document.querySelector('.detail-row');
        const jumlahInput = firstRow.querySelector('.detail-jumlah');
        const hargaInput = firstRow.querySelector('.detail-harga');
        
        jumlahInput.addEventListener('input', () => calculateSubtotal(firstRow));
        hargaInput.addEventListener('input', () => calculateSubtotal(firstRow));
        
        // Initial calculation
        calculateSubtotal(firstRow);
        
        // Auto-format date inputs to today and 3 months from now
        const today = new Date().toISOString().split('T')[0];
        const threeMonthsLater = new Date();
        threeMonthsLater.setMonth(threeMonthsLater.getMonth() + 3);
        const threeMonthsLaterStr = threeMonthsLater.toISOString().split('T')[0];
        
        if (!document.querySelector('input[name="tanggal_mulai"]').value) {
            document.querySelector('input[name="tanggal_mulai"]').value = today;
        }
        
        if (!document.querySelector('input[name="tanggal_selesai"]').value) {
            document.querySelector('input[name="tanggal_selesai"]').value = threeMonthsLaterStr;
        }
    });
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/naufal/sigap-desa/resources/views/anggaran/create.blade.php ENDPATH**/ ?>