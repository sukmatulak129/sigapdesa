@extends('layouts.app')

@section('title', 'Detail Anggaran - ' . $anggaran->nama_program)

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $anggaran->nama_program }}</h1>
                <p class="text-gray-600">Program Anggaran Desa</p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('anggaran.index') }}" 
                   class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </a>
                @if($anggaran->status == 'draft')
                <a href="{{ route('anggaran.edit', $anggaran) }}" 
                   class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                    <i class="fas fa-edit mr-2"></i> Edit
                </a>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Program Info -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4">Informasi Program</h2>
                
                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Kategori</p>
                            <p class="font-medium">{{ ucfirst($anggaran->kategori) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Sumber Dana</p>
                            <p class="font-medium">{{ $anggaran->sumber_dana }}</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Status</p>
                            <span class="px-3 py-1 text-xs rounded-full bg-{{ $anggaran->status_color }}-100 text-{{ $anggaran->status_color }}-800">
                                {{ $anggaran->status_label }}
                            </span>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Dibuat Oleh</p>
                            <div class="flex items-center mt-1">
                                <img src="{{ $anggaran->user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($anggaran->user->name) }}" 
                                     class="w-6 h-6 rounded-full mr-2">
                                <span>{{ $anggaran->user->name }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-500">Deskripsi Program</p>
                        <p class="mt-2 whitespace-pre-line">{{ $anggaran->deskripsi }}</p>
                    </div>
                    
                    @if($anggaran->catatan)
                    <div class="border-l-4 border-yellow-500 bg-yellow-50 p-4">
                        <p class="text-sm text-gray-500">Catatan</p>
                        <p class="mt-1">{{ $anggaran->catatan }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Anggaran Stats -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4">Statistik Anggaran</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="text-center p-4 border rounded-lg">
                        <p class="text-sm text-gray-500">Anggaran Disahkan</p>
                        <p class="text-2xl font-bold text-blue-600">Rp {{ number_format($anggaran->anggaran_disahkan, 0, ',', '.') }}</p>
                    </div>
                    
                    <div class="text-center p-4 border rounded-lg">
                        <p class="text-sm text-gray-500">Telah Terpakai</p>
                        <p class="text-2xl font-bold text-green-600">Rp {{ number_format($anggaran->anggaran_terpakai, 0, ',', '.') }}</p>
                    </div>
                    
                    <div class="text-center p-4 border rounded-lg">
                        <p class="text-sm text-gray-500">Sisa Anggaran</p>
                        <p class="text-2xl font-bold text-purple-600">Rp {{ number_format($anggaran->sisa_anggaran, 0, ',', '.') }}</p>
                    </div>
                </div>
                
                <!-- Progress Bar -->
                <div class="mt-6">
                    <div class="flex justify-between mb-1">
                        <span class="text-sm font-medium">Progress Penggunaan Anggaran</span>
                        <span class="text-sm font-medium">{{ number_format($anggaran->persentase_terpakai, 1) }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-green-600 h-3 rounded-full" style="width: {{ $anggaran->persentase_terpakai }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Detail Anggaran -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold">Rincian Anggaran</h2>
                    
                    @if($anggaran->status == 'berjalan')
                    <button onclick="showAddRealisasiModal()" 
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        <i class="fas fa-plus mr-2"></i> Tambah Realisasi
                    </button>
                    @endif
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Harga Satuan</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Bukti</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($anggaran->details as $detail)
                            <tr>
                                <td class="px-4 py-3">
                                    <div>
                                        <p class="font-medium">{{ $detail->item }}</p>
                                        <p class="text-sm text-gray-500">{{ $detail->deskripsi }}</p>
                                    </div>
                                </td>
                                <td class="px-4 py-3">{{ number_format($detail->jumlah, 2) }}</td>
                                <td class="px-4 py-3">Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 font-medium">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-3 py-1 text-xs rounded-full bg-{{ $detail->status_color }}-100 text-{{ $detail->status_color }}-800">
                                        {{ $detail->status_label }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    @if($detail->bukti)
                                    <a href="{{ asset('storage/' . $detail->bukti) }}" 
                                       target="_blank" 
                                       class="text-blue-600 hover:underline text-sm">
                                        Lihat Bukti
                                    </a>
                                    @else
                                    <span class="text-gray-400 text-sm">-</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Summary -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <div class="flex justify-between">
                        <div>
                            <h3 class="font-bold">Total Rencana</h3>
                            <p class="text-2xl font-bold text-blue-600">Rp {{ number_format($totalRencana, 0, ',', '.') }}</p>
                        </div>
                        <div class="text-right">
                            <h3 class="font-bold">Total Realisasi</h3>
                            <p class="text-2xl font-bold text-green-600">Rp {{ number_format($totalRealisasi, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Admin Actions -->
            @if(auth()->user()->isAdmin() && in_array($anggaran->status, ['draft', 'diajukan', 'disahkan', 'berjalan']))
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4">Aksi Admin</h2>
                
                <form action="{{ route('anggaran.update-status', $anggaran) }}" method="POST" class="space-y-4">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Update Status
                            </label>
                            <select name="status" 
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2"
                                    onchange="toggleAnggaranInput(this.value)">
                                <option value="">Pilih Status</option>
                                @if($anggaran->status == 'draft')
                                <option value="diajukan">Diajukan</option>
                                @endif
                                @if($anggaran->status == 'diajukan')
                                <option value="disahkan">Disahkan</option>
                                <option value="ditolak">Ditolak</option>
                                @endif
                                @if($anggaran->status == 'disahkan')
                                <option value="berjalan">Berjalan</option>
                                @endif
                                @if($anggaran->status == 'berjalan')
                                <option value="selesai">Selesai</option>
                                @endif
                            </select>
                        </div>
                        
                        <div id="anggaran-disahkan-container" class="hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Anggaran Disahkan (Rp)
                            </label>
                            <input type="number" name="anggaran_disahkan" 
                                   value="{{ $anggaran->anggaran_diajukan }}"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2"
                                   min="0" step="1">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Catatan
                        </label>
                        <textarea name="catatan" rows="2" 
                                  placeholder="Tambahkan catatan untuk status baru..."
                                  class="w-full border border-gray-300 rounded-lg px-4 py-2">{{ $anggaran->catatan }}</textarea>
                    </div>
                    
                    <div class="flex justify-end">
                        <button type="submit" 
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            <i class="fas fa-save mr-2"></i> Update Status
                        </button>
                    </div>
                </form>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Timeline -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4">Timeline</h2>
                
                <div class="space-y-4">
                    <div class="flex items-start">
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-plus text-green-600"></i>
                        </div>
                        <div>
                            <p class="font-medium">Dibuat</p>
                            <p class="text-sm text-gray-500">{{ $anggaran->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                    
                    @if($anggaran->tanggal_mulai)
                    <div class="flex items-start">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-play text-blue-600"></i>
                        </div>
                        <div>
                            <p class="font-medium">Mulai</p>
                            <p class="text-sm text-gray-500">{{ $anggaran->tanggal_mulai->format('d/m/Y') }}</p>
                        </div>
                    </div>
                    @endif
                    
                    @if($anggaran->tanggal_selesai)
                    <div class="flex items-start">
                        <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-flag-checkered text-purple-600"></i>
                        </div>
                        <div>
                            <p class="font-medium">Target Selesai</p>
                            <p class="text-sm text-gray-500">{{ $anggaran->tanggal_selesai->format('d/m/Y') }}</p>
                        </div>
                    </div>
                    @endif
                    
                    <div class="flex items-start">
                        <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-history text-gray-600"></i>
                        </div>
                        <div>
                            <p class="font-medium">Terakhir Diupdate</p>
                            <p class="text-sm text-gray-500">{{ $anggaran->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Laporan Terkait -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4">Laporan Terkait</h2>
                
                <div class="space-y-3">
                    @forelse($laporanTerkait as $laporan)
                    <a href="{{ route('laporan.show', $laporan) }}" 
                       class="block p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="font-medium text-sm">{{ Str::limit($laporan->judul, 40) }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ $laporan->created_at->format('d/m/Y') }}</p>
                            </div>
                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                Rp {{ number_format($laporan->biaya, 0, ',', '.') }}
                            </span>
                        </div>
                    </a>
                    @empty
                    <p class="text-gray-500 text-sm">Belum ada laporan terkait</p>
                    @endforelse
                </div>
                
                @if($laporanTerkait->count() > 0)
                <div class="mt-4">
                    <a href="{{ route('laporan.index') }}?kategori={{ $anggaran->kategori }}" 
                       class="text-blue-600 hover:underline text-sm">
                        Lihat Semua Laporan {{ ucfirst($anggaran->kategori) }}
                    </a>
                </div>
                @endif
            </div>

            <!-- Quick Stats -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                <h3 class="font-bold text-blue-800 mb-4">Statistik Cepat</h3>
                
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-blue-700">Total Item</span>
                        <span class="font-medium">{{ $anggaran->details->count() }}</span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="text-sm text-blue-700">Item Realisasi</span>
                        <span class="font-medium">{{ $anggaran->realisasiDetails->count() }}</span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="text-sm text-blue-700">Persentase Realisasi</span>
                        <span class="font-medium">{{ number_format($anggaran->persentase_terpakai, 1) }}%</span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="text-sm text-blue-700">Sisa Hari</span>
                        @if($anggaran->tanggal_selesai)
                            @php
                                $sisaHari = max(0, now()->diffInDays($anggaran->tanggal_selesai, false));
                            @endphp
                            <span class="font-medium {{ $sisaHari < 30 ? 'text-red-600' : 'text-green-600' }}">
                                {{ $sisaHari }} hari
                            </span>
                        @else
                            <span class="font-medium">-</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Add Realisasi -->
<div id="addRealisasiModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold">Tambah Realisasi Anggaran</h3>
            <button onclick="closeAddRealisasiModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form action="{{ route('anggaran.add-realisasi', $anggaran) }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Item *</label>
                    <input type="text" name="item" 
                           class="w-full border border-gray-300 rounded-lg px-4 py-2"
                           required>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi *</label>
                    <textarea name="deskripsi" rows="2" 
                              class="w-full border border-gray-300 rounded-lg px-4 py-2"
                              required></textarea>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah *</label>
                        <input type="number" name="jumlah" 
                               class="w-full border border-gray-300 rounded-lg px-4 py-2"
                               min="0" step="0.01" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Harga Satuan *</label>
                        <input type="number" name="harga_satuan" 
                               class="w-full border border-gray-300 rounded-lg px-4 py-2"
                               min="0" step="1" required>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bukti (Opsional)</label>
                    <input type="file" name="bukti" 
                           class="w-full border border-gray-300 rounded-lg px-4 py-2"
                           accept="image/*,.pdf">
                </div>
                
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeAddRealisasiModal()"
                            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        Simpan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function toggleAnggaranInput(status) {
    const container = document.getElementById('anggaran-disahkan-container');
    if (status === 'disahkan') {
        container.classList.remove('hidden');
    } else {
        container.classList.add('hidden');
    }
}

function showAddRealisasiModal() {
    document.getElementById('addRealisasiModal').classList.remove('hidden');
}

function closeAddRealisasiModal() {
    document.getElementById('addRealisasiModal').classList.add('hidden');
}
</script>
@endpush
@endsection