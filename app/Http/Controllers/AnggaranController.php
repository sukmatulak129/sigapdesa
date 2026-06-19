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