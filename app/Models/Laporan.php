<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laporan extends Model
{
    use HasFactory;

    protected $table = 'laporan';
    
    protected $fillable = [
        'user_id',
        'judul',
        'deskripsi',
        'kategori',
        'subkategori',
        'latitude',
        'longitude',
        'foto',
        'status',
        'nomor_tiket',
        'alasan_ditolak',
        'tanggal_selesai',
        'biaya',
        'sumber_dana',
        'foto_bukti'
    ];

    protected $casts = [
        'foto' => 'array',
        'foto_bukti' => 'array',
        'biaya' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'menunggu' => 'gray',
            'diterima' => 'red',
            'ditolak' => 'gray',
            'dikerjakan' => 'yellow',
            'selesai' => 'green',
            default => 'gray'
        };
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'menunggu' => 'Menunggu Verifikasi',
            'diterima' => 'Valid - Dalam Antrian',
            'ditolak' => 'Ditolak',
            'dikerjakan' => 'Sedang Dikerjakan',
            'selesai' => 'Selesai',
            default => 'Tidak Diketahui'
        };
    }
}