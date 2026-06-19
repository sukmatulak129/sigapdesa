<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LaporanController extends Controller
{
    public function index()
    {
        $laporan = Laporan::with('user')
            ->latest()
            ->paginate(20);

        return view('laporan.index', compact('laporan'));
    }

    public function create()
    {
        return view('laporan.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'kategori' => 'required|in:infrastruktur,lingkungan,sosial,administrasi',
            'subkategori' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'foto' => 'nullable|array',
            'foto.*' => 'image|max:2048'
        ]);

        $validated['user_id'] = auth()->id();
        $validated['nomor_tiket'] = 'TKT-' . strtoupper(Str::random(8));
        $validated['status'] = 'menunggu';

        if ($request->hasFile('foto')) {
            $fotoPaths = [];
            foreach ($request->file('foto') as $foto) {
                $path = $foto->store('laporan', 'public');
                $fotoPaths[] = $path;
            }
            $validated['foto'] = $fotoPaths;
        }

        Laporan::create($validated);

        return redirect()->route('laporan.saya')
            ->with('success', 'Laporan berhasil dikirim. Nomor tiket: ' . $validated['nomor_tiket']);
    }

    public function show(Laporan $laporan)
    {
        return view('laporan.show', compact('laporan'));
    }

    public function verifikasi(Request $request, Laporan $laporan)
    {
        $request->validate([
            'status' => 'required|in:diterima,ditolak',
            'alasan_ditolak' => 'required_if:status,ditolak'
        ]);

        $laporan->update([
            'status' => $request->status,
            'alasan_ditolak' => $request->alasan_ditolak
        ]);

        $status = $request->status == 'diterima' ? 'diterima' : 'ditolak';
        
        return back()->with('success', "Laporan berhasil di{$status}.");
    }

    public function updateStatus(Request $request, Laporan $laporan)
    {
        $request->validate([
            'status' => 'required|in:dikerjakan,selesai'
        ]);

        $data = ['status' => $request->status];
        
        if ($request->status == 'selesai') {
            $request->validate([
                'biaya' => 'required|numeric|min:0',
                'sumber_dana' => 'required|string',
                'foto_bukti' => 'nullable|array',
                'foto_bukti.*' => 'image|max:2048'
            ]);

            $data['biaya'] = $request->biaya;
            $data['sumber_dana'] = $request->sumber_dana;
            $data['tanggal_selesai'] = now();

            if ($request->hasFile('foto_bukti')) {
                $fotoPaths = [];
                foreach ($request->file('foto_bukti') as $foto) {
                    $path = $foto->store('bukti', 'public');
                    $fotoPaths[] = $path;
                }
                $data['foto_bukti'] = $fotoPaths;
            }
        }

        $laporan->update($data);

        return back()->with('success', 'Status laporan berhasil diperbarui.');
    }

    public function laporanSaya()
    {
        $laporan = Laporan::where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('laporan.saya', compact('laporan'));
    }
}