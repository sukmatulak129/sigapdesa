App/Http/Kernel.php
<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array<int, class-string|string>
     */
    protected $middleware = [
        // \App\Http\Middleware\TrustHosts::class,
        \App\Http\Middleware\TrustProxies::class,
        \Illuminate\Http\Middleware\HandleCors::class,
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            \Illuminate\Routing\Middleware\ThrottleRequests::class.':api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * The application's middleware aliases.
     *
     * Aliases may be used instead of class names to conveniently assign middleware to routes and groups.
     *
     * @var array<string, class-string|string>
     */
    protected $middlewareAliases = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'admin' => \App\Http\Middleware\AdminMiddleware::class,
    ];
}
App/Http/Middleware/AdminMiddleware.php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->withErrors([
                'message' => 'Silakan login terlebih dahulu.'
            ]);
        }

        if (!Auth::user()->isAdmin()) {
            return redirect()->route('dashboard')->withErrors([
                'message' => 'Akses ditolak. Hanya admin yang dapat mengakses halaman ini.'
            ]);
        }

        return $next($request);
    }
}
App/Http/Controllers/AnggaranController.php
<?php

namespace App\Http\Controllers;

use App\Models\Anggaran;
use App\Models\AnggaranDetail;
use App\Models\Laporan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnggaranController extends Controller
{
    public function index()
    {
        $anggaran = Anggaran::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $totalAnggaran = Anggaran::sum('anggaran_disahkan');
        $totalTerpakai = Anggaran::sum('anggaran_terpakai');
        $totalProgram = Anggaran::count();

        return view('anggaran.index', compact(
            'anggaran',
            'totalAnggaran',
            'totalTerpakai',
            'totalProgram'
        ));
    }

    public function create()
    {
        return view('anggaran.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_program' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'kategori' => 'required|in:infrastruktur,sosial,ekonomi,pendidikan,kesehatan,lainnya',
            'sumber_dana' => 'required|string|max:255',
            'anggaran_diajukan' => 'required|numeric|min:0',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'catatan' => 'nullable|string',
            'details' => 'required|array|min:1',
            'details.*.item' => 'required|string|max:255',
            'details.*.deskripsi' => 'required|string',
            'details.*.jumlah' => 'required|numeric|min:0',
            'details.*.harga_satuan' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $anggaran = Anggaran::create([
                'nama_program' => $validated['nama_program'],
                'deskripsi' => $validated['deskripsi'],
                'kategori' => $validated['kategori'],
                'sumber_dana' => $validated['sumber_dana'],
                'anggaran_diajukan' => $validated['anggaran_diajukan'],
                'user_id' => auth()->id(),
                'tanggal_mulai' => $validated['tanggal_mulai'],
                'tanggal_selesai' => $validated['tanggal_selesai'],
                'catatan' => $validated['catatan'],
                'status' => 'draft'
            ]);

            $totalSubtotal = 0;
            foreach ($validated['details'] as $detail) {
                $subtotal = $detail['jumlah'] * $detail['harga_satuan'];
                $totalSubtotal += $subtotal;

                AnggaranDetail::create([
                    'anggaran_id' => $anggaran->id,
                    'item' => $detail['item'],
                    'deskripsi' => $detail['deskripsi'],
                    'jumlah' => $detail['jumlah'],
                    'harga_satuan' => $detail['harga_satuan'],
                    'subtotal' => $subtotal,
                    'status' => 'rencana'
                ]);
            }

            // Update dengan total subtotal
            $anggaran->update(['anggaran_diajukan' => $totalSubtotal]);

            DB::commit();

            return redirect()->route('anggaran.index')
                ->with('success', 'Program anggaran berhasil dibuat!');
                
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function show(Anggaran $anggaran)
    {
        $anggaran->load(['details', 'user']);
        
        $totalRealisasi = $anggaran->realisasiDetails()->sum('subtotal');
        $totalRencana = $anggaran->details()->where('status', 'rencana')->sum('subtotal');

        // Laporan terkait (laporan yang sudah selesai dengan kategori yang sama)
        $laporanTerkait = Laporan::where('status', 'selesai')
            ->where('kategori', $anggaran->kategori)
            ->whereBetween('created_at', [
                $anggaran->tanggal_mulai ?? '2000-01-01',
                $anggaran->tanggal_selesai ?? now()->addYear()
            ])
            ->paginate(10);

        return view('anggaran.show', compact(
            'anggaran',
            'totalRealisasi',
            'totalRencana',
            'laporanTerkait'
        ));
    }

    public function edit(Anggaran $anggaran)
    {
        if ($anggaran->status != 'draft') {
            return redirect()->route('anggaran.index')
                ->withErrors(['error' => 'Hanya anggaran dengan status draft yang dapat diedit.']);
        }

        $anggaran->load('details');
        return view('anggaran.edit', compact('anggaran'));
    }

    public function update(Request $request, Anggaran $anggaran)
    {
        if ($anggaran->status != 'draft') {
            return redirect()->back()
                ->withErrors(['error' => 'Hanya anggaran dengan status draft yang dapat diedit.']);
        }

        $validated = $request->validate([
            'nama_program' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'kategori' => 'required|in:infrastruktur,sosial,ekonomi,pendidikan,kesehatan,lainnya',
            'sumber_dana' => 'required|string|max:255',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'catatan' => 'nullable|string',
            'details' => 'required|array|min:1',
            'details.*.item' => 'required|string|max:255',
            'details.*.deskripsi' => 'required|string',
            'details.*.jumlah' => 'required|numeric|min:0',
            'details.*.harga_satuan' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $anggaran->update([
                'nama_program' => $validated['nama_program'],
                'deskripsi' => $validated['deskripsi'],
                'kategori' => $validated['kategori'],
                'sumber_dana' => $validated['sumber_dana'],
                'tanggal_mulai' => $validated['tanggal_mulai'],
                'tanggal_selesai' => $validated['tanggal_selesai'],
                'catatan' => $validated['catatan']
            ]);

            // Hapus detail lama
            $anggaran->details()->delete();

            // Buat detail baru
            $totalSubtotal = 0;
            foreach ($validated['details'] as $detail) {
                $subtotal = $detail['jumlah'] * $detail['harga_satuan'];
                $totalSubtotal += $subtotal;

                AnggaranDetail::create([
                    'anggaran_id' => $anggaran->id,
                    'item' => $detail['item'],
                    'deskripsi' => $detail['deskripsi'],
                    'jumlah' => $detail['jumlah'],
                    'harga_satuan' => $detail['harga_satuan'],
                    'subtotal' => $subtotal,
                    'status' => 'rencana'
                ]);
            }

            // Update total anggaran
            $anggaran->update(['anggaran_diajukan' => $totalSubtotal]);

            DB::commit();

            return redirect()->route('anggaran.index')
                ->with('success', 'Program anggaran berhasil diperbarui!');
                
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function destroy(Anggaran $anggaran)
    {
        if ($anggaran->status != 'draft') {
            return redirect()->back()
                ->withErrors(['error' => 'Hanya anggaran dengan status draft yang dapat dihapus.']);
        }

        $anggaran->delete();

        return redirect()->route('anggaran.index')
            ->with('success', 'Program anggaran berhasil dihapus!');
    }

    public function updateStatus(Request $request, Anggaran $anggaran)
    {
        $request->validate([
            'status' => 'required|in:diajukan,disahkan,ditolak,berjalan,selesai',
            'anggaran_disahkan' => 'required_if:status,disahkan|numeric|min:0',
            'catatan' => 'nullable|string'
        ]);

        $data = [
            'status' => $request->status,
            'catatan' => $request->catatan ?? $anggaran->catatan
        ];

        if ($request->status == 'disahkan') {
            $data['anggaran_disahkan'] = $request->anggaran_disahkan;
            $data['status'] = 'disahkan';
        }

        $anggaran->update($data);

        return redirect()->route('anggaran.show', $anggaran)
            ->with('success', 'Status anggaran berhasil diperbarui!');
    }

    public function addRealisasi(Request $request, Anggaran $anggaran)
    {
        if ($anggaran->status != 'berjalan') {
            return redirect()->back()
                ->withErrors(['error' => 'Hanya anggaran dengan status berjalan yang dapat ditambah realisasi.']);
        }

        $request->validate([
            'item' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'jumlah' => 'required|numeric|min:0',
            'harga_satuan' => 'required|numeric|min:0',
            'bukti' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048'
        ]);

        $subtotal = $request->jumlah * $request->harga_satuan;

        // Cek apakah melebihi anggaran disahkan
        $totalRealisasi = $anggaran->realisasiDetails()->sum('subtotal');
        if (($totalRealisasi + $subtotal) > $anggaran->anggaran_disahkan) {
            return redirect()->back()
                ->withErrors(['error' => 'Total realisasi melebihi anggaran yang disahkan!']);
        }

        $buktiPath = null;
        if ($request->hasFile('bukti')) {
            $buktiPath = $request->file('bukti')->store('anggaran/realisasi', 'public');
        }

        AnggaranDetail::create([
            'anggaran_id' => $anggaran->id,
            'item' => $request->item,
            'deskripsi' => $request->deskripsi,
            'jumlah' => $request->jumlah,
            'harga_satuan' => $request->harga_satuan,
            'subtotal' => $subtotal,
            'status' => 'realisasi',
            'bukti' => $buktiPath
        ]);

        // Update total terpakai
        $anggaran->update([
            'anggaran_terpakai' => $totalRealisasi + $subtotal
        ]);

        return redirect()->route('anggaran.show', $anggaran)
            ->with('success', 'Realisasi anggaran berhasil ditambahkan!');
    }

    public function dashboard()
    {
        $totalAnggaran = Anggaran::sum('anggaran_disahkan');
        $totalTerpakai = Anggaran::sum('anggaran_terpakai');
        $totalSisa = $totalAnggaran - $totalTerpakai;

        $anggaranPerKategori = Anggaran::selectRaw('kategori, COUNT(*) as jumlah, SUM(anggaran_disahkan) as total_diajukan, SUM(anggaran_terpakai) as total_terpakai')
            ->groupBy('kategori')
            ->get();

        $anggaranPerStatus = Anggaran::selectRaw('status, COUNT(*) as jumlah')
            ->groupBy('status')
            ->get();

        $recentAnggaran = Anggaran::with('user')
            ->orderBy('updated_at', 'desc')
            ->take(10)
            ->get();

        return view('anggaran.dashboard', compact(
            'totalAnggaran',
            'totalTerpakai',
            'totalSisa',
            'anggaranPerKategori',
            'anggaranPerStatus',
            'recentAnggaran'
        ));
    }

    public function export(Request $request)
    {
        $query = Anggaran::with(['details', 'user']);

        if ($request->has('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('tahun')) {
            $query->whereYear('created_at', $request->tahun);
        }

        $anggaran = $query->get();

        // In a real application, you would generate Excel or PDF here
        // For now, we'll just return a success message
        
        return redirect()->route('anggaran.index')
            ->with('success', 'Data anggaran berhasil diekspor! Total data: ' . $anggaran->count());
    }
}
App/Models/User.php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'avatar',
        'role',
        'phone',
        'address'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function laporan()
    {
        return $this->hasMany(Laporan::class);
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isWarga()
    {
        return $this->role === 'warga';
    }
}
App/Models/Anggaran.php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anggaran extends Model
{
    use HasFactory;

    protected $table = 'anggaran';
    
    protected $fillable = [
        'nama_program',
        'deskripsi',
        'kategori',
        'sumber_dana',
        'anggaran_diajukan',
        'anggaran_disahkan',
        'anggaran_terpakai',
        'status',
        'tanggal_mulai',
        'tanggal_selesai',
        'dokumen',
        'user_id',
        'catatan'
    ];

    protected $casts = [
        'dokumen' => 'array',
        'anggaran_diajukan' => 'decimal:2',
        'anggaran_disahkan' => 'decimal:2',
        'anggaran_terpakai' => 'decimal:2',
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(AnggaranDetail::class);
    }

    public function realisasiDetails()
    {
        return $this->details()->where('status', 'realisasi');
    }

    public function getSisaAnggaranAttribute()
    {
        return $this->anggaran_disahkan - $this->anggaran_terpakai;
    }

    public function getPersentaseTerpakaiAttribute()
    {
        if ($this->anggaran_disahkan <= 0) return 0;
        return ($this->anggaran_terpakai / $this->anggaran_disahkan) * 100;
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'draft' => 'gray',
            'diajukan' => 'blue',
            'disahkan' => 'green',
            'ditolak' => 'red',
            'berjalan' => 'yellow',
            'selesai' => 'purple',
            default => 'gray'
        };
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'draft' => 'Draft',
            'diajukan' => 'Diajukan',
            'disahkan' => 'Disahkan',
            'ditolak' => 'Ditolak',
            'berjalan' => 'Berjalan',
            'selesai' => 'Selesai',
            default => 'Tidak Diketahui'
        };
    }
}
App/Models/AnggaranDetail.php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnggaranDetail extends Model
{
    use HasFactory;

    protected $table = 'anggaran_details';
    
    protected $fillable = [
        'anggaran_id',
        'item',
        'deskripsi',
        'jumlah',
        'harga_satuan',
        'subtotal',
        'status',
        'bukti'
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'harga_satuan' => 'decimal:2',
        'subtotal' => 'decimal:2'
    ];

    public function anggaran()
    {
        return $this->belongsTo(Anggaran::class);
    }

    public function getStatusColorAttribute()
    {
        return $this->status == 'realisasi' ? 'green' : 'blue';
    }

    public function getStatusLabelAttribute()
    {
        return $this->status == 'realisasi' ? 'Realisasi' : 'Rencana';
    }
}
views/anggaran/index.blade.php
@extends('layouts.app')

@section('title', 'Manajemen Anggaran')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Manajemen Anggaran</h1>
            <p class="text-gray-600">Kelola program dan realisasi anggaran desa</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('anggaran.dashboard') }}" 
               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <i class="fas fa-chart-bar mr-2"></i> Dashboard
            </a>
            <a href="{{ route('anggaran.create') }}" 
               class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                <i class="fas fa-plus mr-2"></i> Buat Program
            </a>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-lg">
                    <i class="fas fa-wallet text-blue-600 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-gray-500">Total Anggaran Disahkan</p>
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
                    <p class="text-gray-500">Total Terpakai</p>
                    <p class="text-2xl font-bold">Rp {{ number_format($totalTerpakai, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-100 rounded-lg">
                    <i class="fas fa-coins text-yellow-600 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-gray-500">Sisa Anggaran</p>
                    <p class="text-2xl font-bold">Rp {{ number_format($totalAnggaran - $totalTerpakai, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-lg">
                    <i class="fas fa-list-alt text-purple-600 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-gray-500">Total Program</p>
                    <p class="text-2xl font-bold">{{ $totalProgram }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-lg shadow p-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                    <option value="">Semua Status</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="diajukan" {{ request('status') == 'diajukan' ? 'selected' : '' }}>Diajukan</option>
                    <option value="disahkan" {{ request('status') == 'disahkan' ? 'selected' : '' }}>Disahkan</option>
                    <option value="berjalan" {{ request('status') == 'berjalan' ? 'selected' : '' }}>Berjalan</option>
                    <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                <select name="kategori" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                    <option value="">Semua Kategori</option>
                    <option value="infrastruktur" {{ request('kategori') == 'infrastruktur' ? 'selected' : '' }}>Infrastruktur</option>
                    <option value="sosial" {{ request('kategori') == 'sosial' ? 'selected' : '' }}>Sosial</option>
                    <option value="ekonomi" {{ request('kategori') == 'ekonomi' ? 'selected' : '' }}>Ekonomi</option>
                    <option value="pendidikan" {{ request('kategori') == 'pendidikan' ? 'selected' : '' }}>Pendidikan</option>
                    <option value="kesehatan" {{ request('kategori') == 'kesehatan' ? 'selected' : '' }}>Kesehatan</option>
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
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Sumber Dana</label>
                <input type="text" name="sumber_dana" 
                       value="{{ request('sumber_dana') }}"
                       placeholder="Cari sumber dana..."
                       class="w-full border border-gray-300 rounded-lg px-4 py-2">
            </div>
            
            <div class="md:col-span-4 flex justify-end space-x-2">
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-filter mr-2"></i> Filter
                </button>
                <a href="{{ route('anggaran.index') }}" 
                   class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Anggaran Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Program</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Anggaran Disahkan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Terpakai</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dibuat Oleh</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($anggaran as $item)
                    <tr>
                        <td class="px-6 py-4">
                            <div>
                                <a href="{{ route('anggaran.show', $item) }}" class="font-medium text-blue-600 hover:underline">
                                    {{ $item->nama_program }}
                                </a>
                                <p class="text-sm text-gray-500 mt-1">{{ Str::limit($item->deskripsi, 50) }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                {{ ucfirst($item->kategori) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 font-medium">
                            Rp {{ number_format($item->anggaran_disahkan, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4">
                            <div>
                                <p class="font-medium">Rp {{ number_format($item->anggaran_terpakai, 0, ',', '.') }}</p>
                                <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                                    <div class="bg-green-600 h-2 rounded-full" style="width: {{ $item->persentase_terpakai }}%"></div>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">{{ number_format($item->persentase_terpakai, 1) }}%</p>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 text-xs rounded-full bg-{{ $item->status_color }}-100 text-{{ $item->status_color }}-800">
                                {{ $item->status_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <img src="{{ $item->user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($item->user->name) }}" 
                                     class="w-8 h-8 rounded-full mr-2">
                                <span>{{ $item->user->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex space-x-2">
                                <a href="{{ route('anggaran.show', $item) }}" 
                                   class="px-3 py-1 bg-blue-100 text-blue-700 rounded text-sm hover:bg-blue-200">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($item->status == 'draft')
                                <a href="{{ route('anggaran.edit', $item) }}" 
                                   class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded text-sm hover:bg-yellow-200">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('anggaran.destroy', $item) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            onclick="return confirm('Hapus program anggaran ini?')"
                                            class="px-3 py-1 bg-red-100 text-red-700 rounded text-sm hover:bg-red-200">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                            Belum ada program anggaran
                            <a href="{{ route('anggaran.create') }}" class="text-blue-600 hover:underline block mt-2">
                                Buat program pertama
                            </a>
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

    <!-- Export Section -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="font-bold text-blue-800">Ekspor Data Anggaran</h3>
                <p class="text-blue-700 mt-1">Download data anggaran lengkap dengan detail</p>
            </div>
            <div class="flex space-x-3">
                <form action="{{ route('anggaran.export') }}" method="GET" class="inline">
                    <input type="hidden" name="export_type" value="excel">
                    <input type="hidden" name="kategori" value="{{ request('kategori') }}">
                    <input type="hidden" name="status" value="{{ request('status') }}">
                    <input type="hidden" name="tahun" value="{{ request('tahun') }}">
                    <button type="submit" 
                            class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 flex items-center">
                        <i class="fas fa-file-excel mr-2"></i> Excel
                    </button>
                </form>
                <form action="{{ route('anggaran.export') }}" method="GET" class="inline">
                    <input type="hidden" name="export_type" value="pdf">
                    <input type="hidden" name="kategori" value="{{ request('kategori') }}">
                    <input type="hidden" name="status" value="{{ request('status') }}">
                    <input type="hidden" name="tahun" value="{{ request('tahun') }}">
                    <button type="submit" 
                            class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 flex items-center">
                        <i class="fas fa-file-pdf mr-2"></i> PDF
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
views/anggaran/create.blade.php
@extends('layouts.app')

@section('title', 'Buat Program Anggaran')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Buat Program Anggaran Baru</h1>
        <p class="text-gray-600">Buat program anggaran dengan rincian detail yang lengkap</p>
    </div>

    <form action="{{ route('anggaran.store') }}" method="POST" id="anggaranForm">
        @csrf
        
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
                               value="{{ old('nama_program') }}"
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
                            <option value="infrastruktur" {{ old('kategori') == 'infrastruktur' ? 'selected' : '' }}>Infrastruktur</option>
                            <option value="sosial" {{ old('kategori') == 'sosial' ? 'selected' : '' }}>Sosial</option>
                            <option value="ekonomi" {{ old('kategori') == 'ekonomi' ? 'selected' : '' }}>Ekonomi</option>
                            <option value="pendidikan" {{ old('kategori') == 'pendidikan' ? 'selected' : '' }}>Pendidikan</option>
                            <option value="kesehatan" {{ old('kategori') == 'kesehatan' ? 'selected' : '' }}>Kesehatan</option>
                            <option value="lainnya" {{ old('kategori') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
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
                            <option value="dana_desa" {{ old('sumber_dana') == 'dana_desa' ? 'selected' : '' }}>Dana Desa</option>
                            <option value="apbd_provinsi" {{ old('sumber_dana') == 'apbd_provinsi' ? 'selected' : '' }}>APBD Provinsi</option>
                            <option value="apbd_kabupaten" {{ old('sumber_dana') == 'apbd_kabupaten' ? 'selected' : '' }}>APBD Kabupaten</option>
                            <option value="bansos" {{ old('sumber_dana') == 'bansos' ? 'selected' : '' }}>Bantuan Sosial</option>
                            <option value="swadaya" {{ old('sumber_dana') == 'swadaya' ? 'selected' : '' }}>Swadaya Masyarakat</option>
                            <option value="lainnya" {{ old('sumber_dana') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
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
                               value="{{ old('tanggal_mulai') }}"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Tanggal Selesai
                        </label>
                        <input type="date" name="tanggal_selesai" 
                               value="{{ old('tanggal_selesai') }}"
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
                              required>{{ old('deskripsi') }}</textarea>
                </div>
                
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Catatan (Opsional)
                    </label>
                    <textarea name="catatan" rows="2" 
                              placeholder="Tambahkan catatan jika diperlukan..."
                              class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('catatan') }}</textarea>
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
                <a href="{{ route('anggaran.index') }}" 
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

@push('scripts')
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
@endpush
@endsection
views/anggaran/show.blade.php
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
routes/web.php
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