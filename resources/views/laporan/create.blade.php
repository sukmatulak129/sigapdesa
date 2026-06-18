@extends('layouts.app')

@section('title', 'Buat Laporan Baru')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Buat Laporan Baru</h1>
        <p class="text-gray-600">Laporkan masalah infrastruktur, lingkungan, atau kebutuhan desa lainnya</p>
    </div>

    <!-- Progress Steps -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center">
                    1
                </div>
                <span class="ml-2 font-medium">Pilih Lokasi</span>
            </div>
            <div class="flex-1 h-1 bg-gray-300 mx-4"></div>
            <div class="flex items-center">
                <div class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center">
                    2
                </div>
                <span class="ml-2 font-medium">Isi Form</span>
            </div>
            <div class="flex-1 h-1 bg-gray-300 mx-4"></div>
            <div class="flex items-center">
                <div class="w-8 h-8 bg-gray-300 text-gray-600 rounded-full flex items-center justify-center">
                    3
                </div>
                <span class="ml-2 text-gray-600">Konfirmasi</span>
            </div>
        </div>
    </div>

    <form action="{{ route('laporan.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        
        <!-- Location Selection -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4">1. Lokasi Kejadian</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Latitude
                    </label>
                    <input type="number" step="any" name="latitude" 
                           value="{{ request('lat', '-6.2088') }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Longitude
                    </label>
                    <input type="number" step="any" name="longitude" 
                           value="{{ request('lng', '106.8456') }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                </div>
            </div>
            
            <div class="mt-4 text-sm text-gray-600">
                <p><i class="fas fa-map-marker-alt mr-2"></i>Koordinat di atas adalah lokasi default. 
                Untuk presisi lebih baik, buka <a href="{{ route('peta') }}" class="text-blue-600 hover:underline">halaman peta</a> dan klik lokasi yang diinginkan.</p>
            </div>
        </div>
        
        <!-- Report Details -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4">2. Detail Laporan</h2>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Judul Laporan *
                    </label>
                    <input type="text" name="judul" 
                           placeholder="Contoh: Jalan Rusak di RT 05"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Kategori *
                    </label>
                    <select name="kategori" 
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="">Pilih Kategori</option>
                        <option value="infrastruktur">Infrastruktur (Jalan, Jembatan, Drainase)</option>
                        <option value="lingkungan">Lingkungan (Sampah, Banjir, Kebersihan)</option>
                        <option value="sosial">Sosial (Fasilitas Umum, Kegiatan)</option>
                        <option value="administrasi">Administrasi (Pelayanan, Administrasi)</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Sub Kategori *
                    </label>
                    <select name="subkategori" 
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="">Pilih Sub Kategori</option>
                        <option value="jalan_rusak">Jalan Rusak</option>
                        <option value="jembatan">Jembatan</option>
                        <option value="drainase">Drainase/Parit</option>
                        <option value="sampah">Sampah Menumpuk</option>
                        <option value="banjir">Banjir/Genangan</option>
                        <option value="penerangan">Penerangan Jalan</option>
                        <option value="lainnya">Lainnya</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Deskripsi Detail *
                    </label>
                    <textarea name="deskripsi" rows="4" 
                              placeholder="Deskripsikan masalah dengan detail. Contoh: 'Jalan berlubang besar selebar 1 meter di depan rumah Pak RT...'"
                              class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required></textarea>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Foto Bukti (Maksimal 3 foto)
                    </label>
                    <input type="file" name="foto[]" multiple accept="image/*"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2"
                           onchange="previewImages(this)">
                    
                    <div id="image-preview" class="mt-4 grid grid-cols-3 gap-4 hidden">
                        <!-- Image previews will be inserted here -->
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Submit -->
        <div class="flex justify-end space-x-4">
            <a href="{{ route('dashboard') }}" 
               class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                Batal
            </a>
            <button type="submit" 
                    class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center">
                <i class="fas fa-paper-plane mr-2"></i> Kirim Laporan
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    function previewImages(input) {
        const preview = document.getElementById('image-preview');
        preview.innerHTML = '';
        preview.classList.add('hidden');
        
        if (input.files.length > 0) {
            preview.classList.remove('hidden');
            
            for (let i = 0; i < Math.min(input.files.length, 3); i++) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'relative';
                    div.innerHTML = `
                        <img src="${e.target.result}" class="w-full h-32 object-cover rounded-lg">
                        <button type="button" onclick="removeImage(${i})" 
                                class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center">
                            ×
                        </button>
                    `;
                    preview.appendChild(div);
                }
                reader.readAsDataURL(input.files[i]);
            }
        }
    }
    
    function removeImage(index) {
        const input = document.querySelector('input[name="foto[]"]');
        const dt = new DataTransfer();
        
        // Copy all files except the removed one
        for (let i = 0; i < input.files.length; i++) {
            if (i !== index) {
                dt.items.add(input.files[i]);
            }
        }
        
        input.files = dt.files;
        previewImages(input);
    }
</script>
@endpush
@endsection