@extends('layouts.app')

@section('title', 'Transparansi Anggaran')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Transparansi Anggaran</h1>
        <p class="text-gray-600">Data penggunaan dana untuk perbaikan infrastruktur desa</p>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-lg">
                    <i class="fas fa-wallet text-blue-600 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-gray-500">Total Anggaran Digunakan</p>
                    <p class="text-3xl font-bold">Rp {{ number_format($totalAnggaran, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-lg">
                    <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-gray-500">Laporan Selesai</p>
                    <p class="text-3xl font-bold">{{ $laporanSelesai }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-lg">
                    <i class="fas fa-calculator text-purple-600 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-gray-500">Rata-rata Biaya/Laporan</p>
                    <p class="text-3xl font-bold">Rp {{ number_format($avgBiaya, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- By Category -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4">Anggaran per Kategori</h2>
            <div class="space-y-4">
                @foreach($anggaranPerKategori as $item)
                <div>
                    <div class="flex justify-between mb-1">
                        <span class="font-medium">{{ ucfirst($item->kategori) }}</span>
                        <span class="font-bold">Rp {{ number_format($item->total, 0, ',', '.') }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        @php
                            $percentage = $totalAnggaran > 0 ? ($item->total / $totalAnggaran) * 100 : 0;
                            $colors = [
                                'infrastruktur' => 'bg-blue-600',
                                'lingkungan' => 'bg-green-600',
                                'sosial' => 'bg-yellow-600',
                                'administrasi' => 'bg-purple-600'
                            ];
                            $color = $colors[$item->kategori] ?? 'bg-gray-600';
                        @endphp
                        <div class="{{ $color }} h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                    </div>
                    <div class="flex justify-between text-sm text-gray-500 mt-1">
                        <span>{{ $item->jumlah }} laporan</span>
                        <span>{{ number_format($percentage, 1) }}%</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Recent Budget Usage -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4">Detail Anggaran</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Kategori</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Jumlah</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($anggaranPerKategori as $item)
                        <tr>
                            <td class="px-4 py-3">
                                <span class="px-3 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                    {{ ucfirst($item->kategori) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">{{ $item->jumlah }} laporan</td>
                            <td class="px-4 py-3 font-medium">
                                Rp {{ number_format($item->total, 0, ',', '.') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-6">
                <a href="{{ route('transparansi.anggaran') }}" 
                   class="text-blue-600 hover:underline flex items-center">
                    <i class="fas fa-list mr-2"></i> Lihat Detail Lengkap
                </a>
            </div>
        </div>
    </div>

    <!-- Information -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
        <div class="flex items-start">
            <i class="fas fa-info-circle text-blue-600 text-xl mr-3 mt-1"></i>
            <div>
                <h3 class="font-bold text-blue-900 mb-2">Informasi Transparansi</h3>
                <p class="text-blue-800">
                    Data anggaran diambil dari laporan yang sudah selesai dengan status "Selesai". 
                    Setiap laporan yang ditandai selesai wajib diisi dengan informasi biaya dan sumber dana 
                    sebagai bentuk transparansi kepada masyarakat.
                </p>
                <p class="text-blue-800 mt-2">
                    Klik pada laporan di peta untuk melihat detail biaya perbaikan, foto bukti, dan informasi lainnya.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection