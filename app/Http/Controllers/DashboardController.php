<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_laporan' => Laporan::count(),
            'laporan_menunggu' => Laporan::where('status', 'menunggu')->count(),
            'laporan_dikerjakan' => Laporan::where('status', 'dikerjakan')->count(),
            'laporan_selesai' => Laporan::where('status', 'selesai')->count(),
        ];

        $laporan_terbaru = Laporan::with('user')
            ->latest()
            ->take(10)
            ->get();

        return view('dashboard', compact('stats', 'laporan_terbaru'));
    }

    public function peta()
    {
        return view('peta');
    }

    public function getLaporanPeta()
    {
        $laporan = Laporan::select(
                'id',
                'judul',
                'latitude',
                'longitude',
                'status',
                'kategori',
                'created_at'
            )
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
                    'popup' => view('components.popup-laporan', ['laporan' => $item])->render()
                ];
            });

        return response()->json($laporan);
    }
}