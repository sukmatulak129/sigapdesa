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