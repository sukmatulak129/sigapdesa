@extends('layouts.app')

@section('title', 'Kelola Laporan')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Kelola Laporan</h1>
            <p class="text-gray-600">Verifikasi dan kelola semua laporan warga</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('dashboard') }}" 
               class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                    <option value="">Semua Status</option>
                    <option value="menunggu" {{ request('status') == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                    <option value="diterima" {{ request('status') == 'diterima' ? 'selected' : '' }}>Diterima</option>
                    <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    <option value="dikerjakan" {{ request('status') == 'dikerjakan' ? 'selected' : '' }}>Dikerjakan</option>
                    <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                <select name="kategori" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                    <option value="">Semua Kategori</option>
                    <option value="infrastruktur" {{ request('kategori') == 'infrastruktur' ? 'selected' : '' }}>Infrastruktur</option>
                    <option value="lingkungan" {{ request('kategori') == 'lingkungan' ? 'selected' : '' }}>Lingkungan</option>
                    <option value="sosial" {{ request('kategori') == 'sosial' ? 'selected' : '' }}>Sosial</option>
                    <option value="administrasi" {{ request('kategori') == 'administrasi' ? 'selected' : '' }}>Administrasi</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                <input type="date" name="start_date" 
                       value="{{ request('start_date') }}"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Akhir</label>
                <input type="date" name="end_date" 
                       value="{{ request('end_date') }}"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2">
            </div>
            
            <div class="md:col-span-4 flex justify-end space-x-2">
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-filter mr-2"></i> Filter
                </button>
                <a href="{{ route('laporan.index') }}" 
                   class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Reports Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. Tiket</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Judul</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pelapor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($laporan as $item)
                    <tr>
                        <td class="px-6 py-4">
                            <span class="font-mono text-sm">{{ $item->nomor_tiket }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('laporan.show', $item) }}" class="text-blue-600 hover:underline">
                                {{ Str::limit($item->judul, 30) }}
                            </a>
                        </td>
                        <td class="px-6 py-4">{{ $item->user->name }}</td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                {{ ucfirst($item->kategori) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="status-badge status-{{ $item->status }}">
                                {{ $item->status_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4">{{ $item->created_at->format('d/m/Y') }}</td>
                        <td class="px-6 py-4">
                            <div class="flex space-x-2">
                                <a href="{{ route('laporan.show', $item) }}" 
                                   class="px-3 py-1 bg-blue-100 text-blue-700 rounded text-sm hover:bg-blue-200">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($item->status == 'menunggu')
                                <form action="{{ route('laporan.verifikasi', $item) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" name="status" value="diterima" 
                                            class="px-3 py-1 bg-green-100 text-green-700 rounded text-sm hover:bg-green-200">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                                <button type="button" onclick="quickReject('{{ $item->id }}')"
                                        class="px-3 py-1 bg-red-100 text-red-700 rounded text-sm hover:bg-red-200">
                                    <i class="fas fa-times"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                            Tidak ada laporan ditemukan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-6 py-4 border-t">
            {{ $laporan->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script>
function quickReject(laporanId) {
    const reason = prompt('Masukkan alasan penolakan:');
    if (reason) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/laporan/${laporanId}/verifikasi`;
        
        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = '{{ csrf_token() }}';
        
        const status = document.createElement('input');
        status.type = 'hidden';
        status.name = 'status';
        status.value = 'ditolak';
        
        const reasonInput = document.createElement('input');
        reasonInput.type = 'hidden';
        reasonInput.name = 'alasan_ditolak';
        reasonInput.value = reason;
        
        form.appendChild(csrf);
        form.appendChild(status);
        form.appendChild(reasonInput);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush
@endsection