<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'jenis_dokumen', 'jenis_no_hak_di_208', 'nomor_dokumen', 'regency_id', 'district_id', 'village_id', 
        'peminjam', 'keperluan', 'tanggal_peminjaman', 'tanggal_pengembalian', 'di_301', 'di_302_303'
    ];

    // Untuk memastikan jenis_dokumen disimpan dalam format JSON
    protected $casts = [
        'jenis_dokumen' => 'array',
    ];
    
}   