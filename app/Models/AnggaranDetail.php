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