@extends('layouts.app')

@section('title', 'Detail Laporan')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $laporan->judul }}</h1>
                <p class="text-gray-600">No. Tiket: <span class="font-mono">{{ $laporan->nomor_tiket }}</span></p>
            </div>
            <div class="text-right">
                <span class="status-badge status-{{ $laporan->status }}">
                    {{ $laporan->status_label }}
                </span>
                <p class="text-sm text-gray-500 mt-2">{{ $laporan->created_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Report Details -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4">Detail Laporan</h2>
                
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Kategori</p>
                            <p class="font-medium">{{ ucfirst($laporan->kategori) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Sub Kategori</p>
                            <p class="font-medium">{{ str_replace('_', ' ', $laporan->subkategori) }}</p>
                        </div>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-500">Lokasi</p>
                        <p class="font-medium">Lat: {{ $laporan->latitude }}, Lng: {{ $laporan->longitude }}</p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-500">Deskripsi</p>
                        <p class="mt-2 whitespace-pre-line">{{ $laporan->deskripsi }}</p>
                    </div>
                </div>
            </div>

            <!-- Photos -->
            @if($laporan->foto)
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4">Foto Laporan</h2>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach($laporan->foto as $foto)
                    <div>
                        <img src="{{ asset('storage/' . $foto) }}" 
                             alt="Foto laporan" 
                             class="w-full h-48 object-cover rounded-lg">
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Admin Actions -->
            @if(auth()->user()->isAdmin())
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4">Aksi Admin</h2>
                
                @if($laporan->status == 'menunggu')
                <div class="space-y-4">
                    <form action="{{ route('laporan.verifikasi', $laporan) }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-2 gap-4">
                            <button type="submit" name="status" value="diterima" 
                                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                Terima Laporan
                            </button>
                            <button type="button" onclick="showRejectForm()"
                                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                                Tolak Laporan
                            </button>
                        </div>
                        
                        <div id="reject-form" class="mt-4 hidden">
                            <textarea name="alasan_ditolak" 
                                      placeholder="Alasan penolakan..."
                                      class="w-full border border-gray-300 rounded-lg px-4 py-2" 
                                      rows="3"></textarea>
                            <div class="mt-4">
                                <button type="submit" name="status" value="ditolak"
                                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                                    Konfirmasi Penolakan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                @endif
                
                @if($laporan->status == 'diterima' || $laporan->status == 'dikerjakan')
                <form action="{{ route('laporan.update-status', $laporan) }}" method="POST" class="mt-4">
                    @csrf
                    
                    @if($laporan->status == 'diterima')
                    <button type="submit" name="status" value="dikerjakan"
                            class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                        Tandai Sedang Dikerjakan
                    </button>
                    @endif
                    
                    @if($laporan->status == 'dikerjakan')
                    <div class="mt-4">
                        <h3 class="font-bold mb-2">Selesaikan Laporan</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Biaya Perbaikan
                                </label>
                                <input type="number" name="biaya" 
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2"
                                       placeholder="Contoh: 1500000" required>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Sumber Dana
                                </label>
                                <select name="sumber_dana" 
                                        class="w-full border border-gray-300 rounded-lg px-4 py-2" required>
                                    <option value="">Pilih Sumber Dana</option>
                                    <option value="dana_desa">Dana Desa</option>
                                    <option value="apbd_provinsi">APBD Provinsi</option>
                                    <option value="apbd_kabupaten">APBD Kabupaten</option>
                                    <option value="swadaya">Swadaya Masyarakat</option>
                                    <option value="lainnya">Lainnya</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Foto Bukti Perbaikan
                                </label>
                                <input type="file" name="foto_bukti[]" multiple accept="image/*"
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2">
                            </div>
                            
                            <button type="submit" name="status" value="selesai"
                                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                Tandai Selesai
                            </button>
                        </div>
                    </div>
                    @endif
                </form>
                @endif
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Reporter Info -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4">Informasi Pelapor</h2>
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-500">Nama</p>
                        <p class="font-medium">{{ $laporan->user->name }}</p>
                    </div>
                    @if(auth()->user()->isAdmin())
                    <div>
                        <p class="text-sm text-gray-500">Email</p>
                        <p class="font-medium">{{ $laporan->user->email }}</p>
                    </div>
                    @endif
                    <div>
                        <p class="text-sm text-gray-500">Role</p>
                        <p class="font-medium">{{ $laporan->user->role == 'admin' ? 'Administrator' : 'Warga' }}</p>
                    </div>
                </div>
            </div>

            <!-- Status History -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4">Riwayat Status</h2>
                <div class="space-y-4">
                    <div class="flex items-start">
                        <div class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-plus"></i>
                        </div>
                        <div>
                            <p class="font-medium">Dibuat</p>
                            <p class="text-sm text-gray-500">{{ $laporan->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                    
                    @if($laporan->status == 'ditolak')
                    <div class="flex items-start">
                        <div class="w-8 h-8 bg-red-600 text-white rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-times"></i>
                        </div>
                        <div>
                            <p class="font-medium">Ditolak</p>
                            <p class="text-sm text-gray-500">{{ $laporan->updated_at->format('d/m/Y H:i') }}</p>
                            @if($laporan->alasan_ditolak)
                            <p class="text-sm text-red-600 mt-1">{{ $laporan->alasan_ditolak }}</p>
                            @endif
                        </div>
                    </div>
                    @endif
                    
                    @if(in_array($laporan->status, ['diterima', 'dikerjakan', 'selesai']))
                    <div class="flex items-start">
                        <div class="w-8 h-8 bg-green-600 text-white rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-check"></i>
                        </div>
                        <div>
                            <p class="font-medium">Diterima</p>
                            <p class="text-sm text-gray-500">{{ $laporan->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                    @endif
                    
                    @if($laporan->status == 'selesai')
                    <div class="flex items-start">
                        <div class="w-8 h-8 bg-green-600 text-white rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-check-double"></i>
                        </div>
                        <div>
                            <p class="font-medium">Selesai</p>
                            <p class="text-sm text-gray-500">{{ $laporan->tanggal_selesai ? $laporan->tanggal_selesai->format('d/m/Y H:i') : $laporan->updated_at->format('d/m/Y H:i') }}</p>
                            @if($laporan->biaya)
                            <p class="text-sm font-medium mt-1">Biaya: Rp {{ number_format($laporan->biaya, 0, ',', '.') }}</p>
                            <p class="text-sm text-gray-500">Sumber: {{ $laporan->sumber_dana }}</p>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Proof Photos -->
            @if($laporan->status == 'selesai' && $laporan->foto_bukti)
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4">Foto Bukti Perbaikan</h2>
                <div class="grid grid-cols-2 gap-2">
                    @foreach($laporan->foto_bukti as $foto)
                    <div>
                        <img src="{{ asset('storage/' . $foto) }}" 
                             alt="Foto bukti" 
                             class="w-full h-32 object-cover rounded-lg">
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Back Button -->
    <div class="mt-8">
        <a href="{{ url()->previous() }}" 
           class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>
</div>

@push('scripts')
<script>
    function showRejectForm() {
        document.getElementById('reject-form').classList.toggle('hidden');
    }
</script>
@endpush
@endsection