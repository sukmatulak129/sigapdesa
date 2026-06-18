@extends('layouts.app')

@section('title', 'Detail Anggaran')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Detail Anggaran</h1>
            <p class="text-gray-600">Rincian penggunaan dana untuk perbaikan infrastruktur desa</p>
        </div>
        <a href="{{ route('transparansi.index') }}" 
           class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-lg">
                    <i class="fas fa-wallet text-blue-600 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-gray-500">Total Anggaran Digunakan</p>
                    <p class="text-2xl font-bold">Rp {{ number_format($totalAnggaran, 0, ',', '.') }}</p>
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
                    <p class="text-2xl font-bold">{{ $anggaran->total() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-lg">
                    <i class="fas fa-calculator text-purple-600 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-gray-500">Rata-rata Biaya</p>
                    <p class="text-2xl font-bold">Rp {{ number_format($avgBiaya, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-100 rounded-lg">
                    <i class="fas fa-chart-line text-yellow-600 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-gray-500">Efisiensi</p>
                    <p class="text-2xl font-bold">{{ number_format($anggaran->total() > 0 ? ($totalAnggaran / ($anggaran->total() * $avgBiaya)) * 100 : 0, 1) }}%</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold mb-4">Filter Data</h2>
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                <select name="kategori" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                    <option value="">Semua Kategori</option>
                    <option value="infrastruktur" {{ request('kategori') == 'infrastruktur' ? 'selected' : '' }}>Infrastruktur</option>
                    <option value="lingkungan" {{ request('kategori') == 'lingkungan' ? 'selected' : '' }}>Lingkungan</option>
                    <option value="sosial" {{ request('kategori') == 'sosial' ? 'selected' : '' }}>Sosial</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Sumber Dana</label>
                <select name="sumber_dana" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                    <option value="">Semua Sumber</option>
                    <option value="dana_desa" {{ request('sumber_dana') == 'dana_desa' ? 'selected' : '' }}>Dana Desa</option>
                    <option value="apbd_provinsi" {{ request('sumber_dana') == 'apbd_provinsi' ? 'selected' : '' }}>APBD Provinsi</option>
                    <option value="apbd_kabupaten" {{ request('sumber_dana') == 'apbd_kabupaten' ? 'selected' : '' }}>APBD Kabupaten</option>
                    <option value="swadaya" {{ request('sumber_dana') == 'swadaya' ? 'selected' : '' }}>Swadaya</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Bulan</label>
                <select name="bulan" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                    <option value="">Semua Bulan</option>
                    @for($i = 1; $i <= 12; $i++)
                    <option value="{{ $i }}" {{ request('bulan') == $i ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month($i)->monthName }}
                    </option>
                    @endfor
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tahun</label>
                <select name="tahun" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                    <option value="">Semua Tahun</option>
                    @for($i = date('Y'); $i >= 2020; $i--)
                    <option value="{{ $i }}" {{ request('tahun') == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
            </div>
            
            <div class="md:col-span-4 flex justify-end space-x-2">
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-filter mr-2"></i> Filter
                </button>
                <a href="{{ route('transparansi.anggaran') }}" 
                   class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Anggaran Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6 border-b">
            <h2 class="text-xl font-bold">Rincian Penggunaan Anggaran</h2>
            <p class="text-gray-600 mt-1">Detail biaya per laporan yang sudah selesai</p>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. Tiket</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Judul Laporan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sumber Dana</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Biaya</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Selesai</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($anggaran as $item)
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
                            @php
                                $sumberDanaLabels = [
                                    'dana_desa' => 'Dana Desa',
                                    'apbd_provinsi' => 'APBD Provinsi',
                                    'apbd_kabupaten' => 'APBD Kabupaten',
                                    'swadaya' => 'Swadaya',
                                    'lainnya' => 'Lainnya'
                                ];
                            @endphp
                            <span class="px-3 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                {{ $sumberDanaLabels[$item->sumber_dana] ?? $item->sumber_dana }}
                            </span>
                        </td>
                        <td class="px-6 py-4 font-medium">
                            Rp {{ number_format($item->biaya, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $item->tanggal_selesai ? $item->tanggal_selesai->format('d/m/Y') : '-' }}
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('laporan.show', $item) }}" 
                               class="px-3 py-1 bg-blue-100 text-blue-700 rounded text-sm hover:bg-blue-200">
                                <i class="fas fa-eye"></i> Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                            Belum ada data anggaran
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-6 py-4 border-t">
            {{ $anggaran->links() }}
        </div>
    </div>

    <!-- Chart Section -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold mb-4">Visualisasi Anggaran</h2>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- By Category -->
            <div>
                <h3 class="font-bold mb-4">Anggaran per Kategori</h3>
                <div class="space-y-4">
                    @foreach($anggaranPerKategori as $item)
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="font-medium">{{ ucfirst($item->kategori) }}</span>
                            <span class="font-bold">Rp {{ number_format($item->total, 0, ',', '.') }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            @php
                                $percentage = $totalAnggaran > 0 ? ($item->total / $totalAnggaran) * 100 : 0;
                                $colors = [
                                    'infrastruktur' => 'bg-blue-600',
                                    'lingkungan' => 'bg-green-600',
                                    'sosial' => 'bg-yellow-600'
                                ];
                                $color = $colors[$item->kategori] ?? 'bg-gray-600';
                            @endphp
                            <div class="{{ $color }} h-3 rounded-full" style="width: {{ $percentage }}%"></div>
                        </div>
                        <div class="flex justify-between text-sm text-gray-500 mt-1">
                            <span>{{ $item->jumlah }} laporan</span>
                            <span>{{ number_format($percentage, 1) }}%</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            
            <!-- By Source -->
            <div>
                <h3 class="font-bold mb-4">Anggaran per Sumber Dana</h3>
                <div class="space-y-4">
                    @foreach($anggaranPerSumber as $item)
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="font-medium">
                                @php
                                    $sumberLabels = [
                                        'dana_desa' => 'Dana Desa',
                                        'apbd_provinsi' => 'APBD Provinsi',
                                        'apbd_kabupaten' => 'APBD Kabupaten',
                                        'swadaya' => 'Swadaya',
                                        'lainnya' => 'Lainnya'
                                    ];
                                @endphp
                                {{ $sumberLabels[$item->sumber_dana] ?? $item->sumber_dana }}
                            </span>
                            <span class="font-bold">Rp {{ number_format($item->total, 0, ',', '.') }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            @php
                                $percentage = $totalAnggaran > 0 ? ($item->total / $totalAnggaran) * 100 : 0;
                                $colors = [
                                    'dana_desa' => 'bg-purple-600',
                                    'apbd_provinsi' => 'bg-red-600',
                                    'apbd_kabupaten' => 'bg-orange-600',
                                    'swadaya' => 'bg-green-600',
                                    'lainnya' => 'bg-gray-600'
                                ];
                                $color = $colors[$item->sumber_dana] ?? 'bg-gray-600';
                            @endphp
                            <div class="{{ $color }} h-3 rounded-full" style="width: {{ $percentage }}%"></div>
                        </div>
                        <div class="flex justify-between text-sm text-gray-500 mt-1">
                            <span>{{ $item->jumlah }} laporan</span>
                            <span>{{ number_format($percentage, 1) }}%</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Export Section -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="font-bold text-blue-800">Ekspor Data Anggaran</h3>
                <p class="text-blue-700 mt-1">Download data anggaran dalam format Excel atau PDF</p>
            </div>
            <div class="flex space-x-3">
                <button onclick="exportToExcel()" 
                        class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 flex items-center">
                    <i class="fas fa-file-excel mr-2"></i> Excel
                </button>
                <button onclick="exportToPDF()" 
                        class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 flex items-center">
                    <i class="fas fa-file-pdf mr-2"></i> PDF
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function exportToExcel() {
    alert('Fitur ekspor Excel akan segera tersedia!');
}

function exportToPDF() {
    alert('Fitur ekspor PDF akan segera tersedia!');
}
</script>
@endpush
@endsection