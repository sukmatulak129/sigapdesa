<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use Illuminate\Http\Request;

class TransparansiController extends Controller
{
    public function index()
    {
        $totalAnggaran = Laporan::where('status', 'selesai')->sum('biaya');
        $laporanSelesai = Laporan::where('status', 'selesai')->count();
        $avgBiaya = $laporanSelesai > 0 ? $totalAnggaran / $laporanSelesai : 0;

        $anggaranPerKategori = Laporan::where('status', 'selesai')
            ->selectRaw('kategori, COUNT(*) as jumlah, SUM(biaya) as total')
            ->groupBy('kategori')
            ->get();

        return view('transparansi.index', compact(
            'totalAnggaran',
            'laporanSelesai',
            'avgBiaya',
            'anggaranPerKategori'
        ));
    }

    public function anggaran()
    {
        $query = Laporan::where('status', 'selesai')
            ->with('user')
            ->orderBy('created_at', 'desc');

        // Filter kategori
        if (request('kategori')) {
            $query->where('kategori', request('kategori'));
        }

        // Filter sumber dana
        if (request('sumber_dana')) {
            $query->where('sumber_dana', request('sumber_dana'));
        }

        // Filter bulan
        if (request('bulan')) {
            $query->whereMonth('tanggal_selesai', request('bulan'));
        }

        // Filter tahun
        if (request('tahun')) {
            $query->whereYear('tanggal_selesai', request('tahun'));
        }

        $anggaran = $query->paginate(20);
        
        $totalAnggaran = $query->sum('biaya');
        $avgBiaya = $anggaran->count() > 0 ? $totalAnggaran / $anggaran->count() : 0;

        $anggaranPerKategori = Laporan::where('status', 'selesai')
            ->selectRaw('kategori, COUNT(*) as jumlah, SUM(biaya) as total')
            ->groupBy('kategori')
            ->get();

        $anggaranPerSumber = Laporan::where('status', 'selesai')
            ->selectRaw('sumber_dana, COUNT(*) as jumlah, SUM(biaya) as total')
            ->groupBy('sumber_dana')
            ->get();

        return view('transparansi.anggaran', compact(
            'anggaran',
            'totalAnggaran',
            'avgBiaya',
            'anggaranPerKategori',
            'anggaranPerSumber'
        ));
    }

    public function getData()
    {
        $data = Laporan::where('status', 'selesai')
            ->selectRaw('DATE(created_at) as tanggal, SUM(biaya) as total')
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();

        $kategori = Laporan::where('status', 'selesai')
            ->selectRaw('kategori, COUNT(*) as jumlah, SUM(biaya) as total')
            ->groupBy('kategori')
            ->get();

        return response()->json([
            'data' => $data,
            'kategori' => $kategori
        ]);
    }
}