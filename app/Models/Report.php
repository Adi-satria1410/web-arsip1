<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Report extends Model
{
    use HasFactory;
    protected $table = 'peminjamen';
    protected $dates = ['tanggal_peminjaman', 'tanggal_pengembalian'];

    protected $fillable = [
        'jenis_dokumen',
        'nomor_dokumen',
        'peminjam',
        'tanggal_peminjaman',
        'tanggal_pengembalian',
        'status',
        'regency_id',
        'district_id',   // Menyimpan ID Kecamatan
        'village_id',

    ];

    public function pinjam()
    {
        $this->status = 'Dipinjam';
        $this->save();
    }

    public function kembalikan()
    {
        $this->tanggal_pengembalian = now();
        $this->status = 'Dikembalikan';
        $this->save();
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
    
    public function getDendaAttribute()
{
    // Jika status adalah 'Dikembalikan', tampilkan pesan 'sudah dikembalikan'
    if ($this->status === 'Dikembalikan') {
        return 'sudah dikembalikan';
    }

    // Jika status adalah 'Dipinjam' atau 'Terlambat' dan sudah ada tanggal pengembalian, periksa keterlambatan
    if (($this->status === 'Dipinjam' || $this->status === 'Terlambat') && $this->tanggal_pengembalian !== null) {
        // Pastikan $this->tanggal_pengembalian adalah objek Carbon
        $tanggalPengembalian = Carbon::parse($this->tanggal_pengembalian);

        // Hitung jumlah hari keterlambatan, gunakan nilai positif jika lewat dari tanggal pengembalian
        $hariTerlambat = now()->diffInDays($tanggalPengembalian, false);

        // Tentukan kategori denda berdasarkan keterlambatan
        if ($hariTerlambat < 0) {
            $hariTerlambat = abs($hariTerlambat); // Konversi nilai negatif menjadi positif

            if ($hariTerlambat <= 7) {
                return 'Peringatan'; // Keterlambatan 1-7 hari
            } elseif ($hariTerlambat <= 14) {
                return 'Penundaan Hak'; // Keterlambatan 8-14 hari
            } else {
                return 'Skorsing'; // Keterlambatan lebih dari 14 hari
            }
        } else {
            return 'Tidak ada sanksi'; // Tidak ada denda jika masih dalam masa pinjam atau tepat waktu
        }
    }

    // Jika tidak ada denda atau status tidak memenuhi syarat untuk penghitungan denda
    return 'Tidak ada sanksi';
}




}
