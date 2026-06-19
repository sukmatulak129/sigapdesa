<?php

use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\FAQController;
use App\Http\Controllers\TransparansiController;
use App\Http\Controllers\AnggaranController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Setup Google OAuth Page
Route::get('/setup-google-oauth', function () {
    return view('auth.google-setup');
})->name('google.setup');

// Admin Login Routes
Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AdminLoginController::class, 'login'])->name('admin.login.submit');
    Route::post('/logout', [AdminLoginController::class, 'logout'])->name('admin.logout');
});

// Main Login Route
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return view('auth.login');
})->name('login');

// Google OAuth Routes
Route::get('auth/google', [GoogleController::class, 'redirect'])->name('google.login');
Route::get('auth/google/callback', [GoogleController::class, 'callback']);

// Logout Route
Route::post('logout', function () {
    Auth::logout();
    return redirect('/');
})->name('logout');

// Authenticated Routes
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/peta', [DashboardController::class, 'peta'])->name('peta');
    Route::get('/api/laporan-peta', [DashboardController::class, 'getLaporanPeta'])->name('api.laporan.peta');
    
    // Laporan Routes
    Route::prefix('laporan')->group(function () {
        Route::get('/', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/buat', [LaporanController::class, 'create'])->name('laporan.create');
        Route::post('/buat', [LaporanController::class, 'store'])->name('laporan.store');
        Route::get('/saya', [LaporanController::class, 'laporanSaya'])->name('laporan.saya');
        Route::get('/{laporan}', [LaporanController::class, 'show'])->name('laporan.show');
        
        // Admin only
        Route::middleware(['admin'])->group(function () {
            Route::post('/{laporan}/verifikasi', [LaporanController::class, 'verifikasi'])->name('laporan.verifikasi');
            Route::post('/{laporan}/update-status', [LaporanController::class, 'updateStatus'])->name('laporan.update-status');
        });
    });
    
    // Chatbot Routes
    Route::prefix('chatbot')->group(function () {
        Route::get('/', [ChatbotController::class, 'index'])->name('chatbot.index');
        Route::post('/send', [ChatbotController::class, 'handleMessage'])->name('chatbot.send');
        Route::get('/history/{sessionId}', [ChatbotController::class, 'getHistory'])->name('chatbot.history');
    });
    
    // FAQ Routes (Admin only)
    Route::middleware(['admin'])->prefix('faq')->group(function () {
        Route::get('/', [FAQController::class, 'index'])->name('faq.index');
        Route::get('/buat', [FAQController::class, 'create'])->name('faq.create');
        Route::post('/buat', [FAQController::class, 'store'])->name('faq.store');
        Route::get('/{faq}/edit', [FAQController::class, 'edit'])->name('faq.edit');
        Route::put('/{faq}', [FAQController::class, 'update'])->name('faq.update');
        Route::delete('/{faq}', [FAQController::class, 'destroy'])->name('faq.destroy');
    });
    
    // Transparansi Routes
    Route::prefix('transparansi')->group(function () {
        Route::get('/', [TransparansiController::class, 'index'])->name('transparansi.index');
        Route::get('/anggaran', [TransparansiController::class, 'anggaran'])->name('transparansi.anggaran');
        Route::get('/api/data', [TransparansiController::class, 'getData'])->name('transparansi.data');
    });
    
    // Anggaran Routes (Admin only)
    Route::middleware(['admin'])->prefix('anggaran')->group(function () {
        Route::get('/', [AnggaranController::class, 'index'])->name('anggaran.index');
        Route::get('/dashboard', [AnggaranController::class, 'dashboard'])->name('anggaran.dashboard');
        Route::get('/buat', [AnggaranController::class, 'create'])->name('anggaran.create');
        Route::post('/buat', [AnggaranController::class, 'store'])->name('anggaran.store');
        Route::get('/{anggaran}', [AnggaranController::class, 'show'])->name('anggaran.show');
        Route::get('/{anggaran}/edit', [AnggaranController::class, 'edit'])->name('anggaran.edit');
        Route::put('/{anggaran}', [AnggaranController::class, 'update'])->name('anggaran.update');
        Route::delete('/{anggaran}', [AnggaranController::class, 'destroy'])->name('anggaran.destroy');
        Route::post('/{anggaran}/status', [AnggaranController::class, 'updateStatus'])->name('anggaran.update-status');
        Route::post('/{anggaran}/realisasi', [AnggaranController::class, 'addRealisasi'])->name('anggaran.add-realisasi');
        Route::get('/export', [AnggaranController::class, 'export'])->name('anggaran.export');
    });
});

// API Routes
Route::prefix('api')->group(function () {
    Route::get('/laporan', function() {
        return \App\Models\Laporan::with('user')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'judul' => $item->judul,
                    'lat' => $item->latitude,
                    'lng' => $item->longitude,
                    'status' => $item->status,
                    'kategori' => $item->kategori,
                    'warna' => $item->status_color,
                    'tanggal' => $item->created_at->format('d/m/Y'),
                    'biaya' => $item->biaya
                ];
            });
    })->name('api.laporan');
    
    Route::get('/stats', function() {
        $total = \App\Models\Laporan::count();
        $selesai = \App\Models\Laporan::where('status', 'selesai')->count();
        $totalBiaya = \App\Models\Laporan::where('status', 'selesai')->sum('biaya');
        
        return response()->json([
            'total_laporan' => $total,
            'laporan_selesai' => $selesai,
            'total_biaya' => $totalBiaya,
            'persentase' => $total > 0 ? round(($selesai / $total) * 100, 2) : 0
        ]);
    })->name('api.stats');
});

// Catch all route
Route::get('/{any}', function () {
    return redirect()->route('dashboard');
})->where('any', '.*');