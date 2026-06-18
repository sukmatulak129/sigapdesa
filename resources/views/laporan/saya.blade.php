@extends('layouts.app')

@section('title', 'Laporan Saya')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Laporan Saya</h1>
            <p class="text-gray-600">Riwayat laporan yang Anda buat</p>
        </div>
        <a href="{{ route('laporan.create') }}" 
           class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            <i class="fas fa-plus mr-2"></i> Buat Laporan Baru
        </a>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-center">
                <div class="text-3xl font-bold text-blue-600">{{ $laporan->total() }}</div>
                <p class="text-gray-600 mt-2">Total Laporan</p>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-center">
                <div class="text-3xl font-bold text-yellow-600">
                    {{ $laporan->where('status', 'menunggu')->count() }}
                </div>
                <p class="text-gray-600 mt-2">Menunggu</p>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-center">
                <div class="text-3xl font-bold text-green-600">
                    {{ $laporan->where('status', 'selesai')->count() }}
                </div>
                <p class="text-gray-600 mt-2">Selesai</p>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-center">
                <div class="text-3xl font-bold text-gray-600">
                    {{ $laporan->where('status', 'ditolak')->count() }}
                </div>
                <p class="text-gray-600 mt-2">Ditolak</p>
            </div>
        </div>
    </div>

    <!-- Reports Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. Tiket</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Judul</th>
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
                                {{ Str::limit($item->judul, 40) }}
                            </a>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                {{ ucfirst($item->kategori) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="status-badge status-{{ $item->status }}">
                                {{ $item->status_label }}
                            </span>
                            @if($item->status == 'ditolak' && $item->alasan_ditolak)
                            <p class="text-sm text-red-600 mt-1">{{ $item->alasan_ditolak }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-4">{{ $item->created_at->format('d/m/Y') }}</td>
                        <td class="px-6 py-4">
                            <a href="{{ route('laporan.show', $item) }}" 
                               class="px-3 py-1 bg-blue-100 text-blue-700 rounded text-sm hover:bg-blue-200">
                                <i class="fas fa-eye"></i> Lihat
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            Anda belum membuat laporan. 
                            <a href="{{ route('laporan.create') }}" class="text-blue-600 hover:underline">
                                Buat laporan pertama Anda
                            </a>
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
@endsection