@extends('layouts.app')

@section('title', 'Beranda - Sigap Desa')

@section('content')
<!-- Hero Section -->
<section class="hero bg-gradient-to-r from-green-600 to-green-700 text-white py-12 md:py-20 rounded-2xl mb-12">
    <div class="container mx-auto px-4 text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-6">SIGAP DESA</h1>
        <p class="text-xl md:text-2xl mb-8 opacity-90">
            Sistem Informasi Geografis & Aspirasi Pembangunan Desa
        </p>
        <p class="text-lg mb-10 max-w-2xl mx-auto">
            Lapor masalah, pantau progres perbaikan, dan lihat transparansi anggaran 
            desa Anda dalam satu platform terpadu.
        </p>
        <div class="flex flex-col sm:flex-row justify-center gap-4">
            @auth
                <a href="{{ route('laporan.create') }}" 
                   class="bg-white text-green-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition text-lg">
                    <i class="fas fa-plus-circle mr-2"></i>Buat Laporan Baru
                </a>
                <a href="{{ route('peta') }}" 
                   class="bg-green-800 text-white px-8 py-3 rounded-lg font-semibold hover:bg-green-900 transition text-lg">
                    <i class="fas fa-map-marked-alt mr-2"></i>Lihat Peta
                </a>
            @else
                <a href="{{ route('login') }}" 
                   class="bg-white text-green-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition text-lg">
                    <i class="fas fa-sign-in-alt mr-2"></i>Login ke Dashboard
                </a>
                <a href="{{ route('peta') }}" 
                   class="bg-green-800 text-white px-8 py-3 rounded-lg font-semibold hover:bg-green-900 transition text-lg">
                    <i class="fas fa-eye mr-2"></i>Jelajahi Peta
                </a>
            @endauth
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="mb-12">
    <h2 class="text-2xl font-bold text-center mb-8 text-gray-800">Statistik Desa</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow p-6 text-center">
            <div class="text-3xl font-bold text-green-600 mb-2">{{ $stats['total_laporan'] }}</div>
            <div class="text-gray-600">Total Laporan</div>
        </div>
        <div class="bg-white rounded-xl shadow p-6 text-center">
            <div class="text-3xl font-bold text-yellow-500 mb-2">{{ $stats['laporan_diproses'] }}</div>
            <div class="text-gray-600">Sedang Diproses</div>
        </div>
        <div class="bg-white rounded-xl shadow p-6 text-center">
            <div class="text-3xl font-bold text-green-500 mb-2">{{ $stats['laporan_selesai'] }}</div>
            <div class="text-gray-600">Telah Selesai</div>
        </div>
        <div class="bg-white rounded-xl shadow p-6 text-center">
            <div class="text-3xl font-bold text-blue-600 mb-2">{{ $stats['total_anggaran'] }}</div>
            <div class="text-gray-600">Total Anggaran</div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="mb-12">
    <h2 class="text-2xl font-bold text-center mb-8 text-gray-800">Fitur Utama</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition">
            <div class="text-green-600 text-4xl mb-4">
                <i class="fas fa-map-marked-alt"></i>
            </div>
            <h3 class="text-xl font-bold mb-3">Peta Interaktif</h3>
            <p class="text-gray-600">
                Klik langsung di peta untuk melaporkan masalah. Akurasi lokasi terjamin.
            </p>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition">
            <div class="text-green-600 text-4xl mb-4">
                <i class="fas fa-chart-line"></i>
            </div>
            <h3 class="text-xl font-bold mb-3">Transparansi Anggaran</h3>
            <p class="text-gray-600">
                Lihat langsung penggunaan dana per proyek perbaikan di peta.
            </p>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition">
            <div class="text-green-600 text-4xl mb-4">
                <i class="fas fa-robot"></i>
            </div>
            <h3 class="text-xl font-bold mb-3">Chatbot AI</h3>
            <p class="text-gray-600">
                Tanya jawab 24 jam dengan chatbot cerdas untuk informasi desa.
            </p>
        </div>
    </div>
</section>

<!-- Recent Reports -->
@if($recent_laporans->count() > 0)
<section class="mb-12">
    <h2 class="text-2xl font-bold text-center mb-8 text-gray-800">Perbaikan Terbaru</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach($recent_laporans as $laporan)
        <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition">
            @if($laporan->foto && count($laporan->foto) > 0)
                <img src="{{ asset('storage/' . $laporan->foto[0]) }}" 
                     alt="{{ $laporan->judul }}" 
                     class="w-full h-48 object-cover">
            @else
                <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                    <i class="fas fa-image text-gray-400 text-4xl"></i>
                </div>
            @endif
            <div class="p-6">
                <h3 class="font-bold text-lg mb-2">{{ $laporan->judul }}</h3>
                <p class="text-gray-600 text-sm mb-3">{{ $laporan->kategori->nama }}</p>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500">{{ $laporan->created_at->format('d M Y') }}</span>
                    <a href="{{ route('laporan.show', $laporan) }}" 
                       class="text-green-600 hover:text-green-800 font-semibold">
                        Lihat Detail
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</section>
@endif

<!-- How It Works -->
<section class="mb-12">
    <h2 class="text-2xl font-bold text-center mb-8 text-gray-800">Cara Kerja</h2>
    <div class="bg-white rounded-xl shadow-lg p-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center">
                <div class="bg-green-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-map-marker-alt text-3xl text-green-600"></i>
                </div>
                <h3 class="text-xl font-bold mb-2">1. Pilih Lokasi</h3>
                <p class="text-gray-600">Klik lokasi di peta atau izinkan akses lokasi</p>
            </div>
            <div class="text-center">
                <div class="bg-green-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-edit text-3xl text-green-600"></i>
                </div>
                <h3 class="text-xl font-bold mb-2">2. Isi Formulir</h3>
                <p class="text-gray-600">Deskripsikan masalah dan upload foto bukti</p>
            </div>
            <div class="text-center">
                <div class="bg-green-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-check-circle text-3xl text-green-600"></i>
                </div>
                <h3 class="text-xl font-bold mb-2">3. Pantau Progress</h3>
                <p class="text-gray-600">Lihat status laporan dan transparansi anggaran</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="text-center">
    <h2 class="text-2xl font-bold mb-6 text-gray-800">Siap Membuat Perubahan?</h2>
    <p class="text-gray-600 mb-8 max-w-2xl mx-auto">
        Bergabunglah dengan warga lainnya dalam membangun desa yang lebih baik 
        melalui transparansi dan partisipasi aktif.
    </p>
    <a href="{{ auth()->check() ? route('laporan.create') : route('login') }}" 
       class="inline-block bg-green-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-green-700 transition text-lg">
        Mulai Sekarang <i class="fas fa-arrow-right ml-2"></i>
    </a>
</section>
@endsection