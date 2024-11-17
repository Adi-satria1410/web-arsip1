<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class notification extends Model
{
    protected $table = 'peminjamen';
    protected $dates = ['tanggal_peminjaman', 'tanggal_pengembalian','tanggal_dikembalikan'];

    // Kolom yang bisa diisi
    protected $fillable = [
        'jenis_dokumen',
        'nomor_dokumen',
        'peminjam',
        'tanggal_peminjaman',
        'tanggal_pengembalian',
        'tanggal_dikembalikan',
        'regency_id',
        'district_id',   
        'village_id',
        'status_permohonan',

    ];
    public function getJenisDokumenAttribute($value)
{
    // Mengubah JSON array menjadi string yang dipisahkan koma
    return implode(', ', json_decode($value));
}
    // Relasi ke model Kabupaten (Regency)
    public function regency()
    {
        return $this->belongsTo(Regency::class, 'regency_id');
    }

    // Relasi ke model Kecamatan (District)
    public function district()
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    // Relasi ke model Desa (Village)
    public function village()
    {
        return $this->belongsTo(Village::class, 'village_id');
    }
}
