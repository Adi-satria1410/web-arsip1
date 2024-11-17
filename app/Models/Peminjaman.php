<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Request;
use App\Notifications\PeminjamanNotification;

class Peminjaman extends Model
{
    use HasFactory;

    // Nama tabel yang digunakan
    protected $table = 'peminjamen';

    // Menentukan kolom yang harus diubah menjadi instance Carbon
    protected $dates = ['tanggal_peminjaman', 'tanggal_pengembalian','tanggal_dikembalikan'];

    // Kolom yang bisa diisi
    protected $fillable = [
        'jenis_dokumen',
        'nomor_dokumen',
        'peminjam',
        'tanggal_peminjaman',
        'tanggal_pengembalian',
        'tanggal_dikembalikan',
        'status',
        'regency_id',
        'district_id',   
        'village_id',
        'status_pengembalian',
        'denda', 
    ];
    public function getJenisDokumenAttribute($value)
    {
        // Decode nilai JSON menjadi array
        $decoded = json_decode($value, true);
    
        // Pastikan data yang terdecode adalah array
        if (is_array($decoded)) {
            return implode(', ', $decoded);
        }
    
        // Jika data tidak valid, kembalikan string kosong atau nilai default lainnya
        return '';
    }
    
    
    public function ajukanPeminjaman(Request $request)
{
    // Simpan data peminjaman sementara
    $peminjaman = Peminjaman::create($request->all());

    return redirect()->route('peminjaman.index')->with('success', 'Pengajuan peminjaman berhasil');
}

    // Boot method untuk memanipulasi model sebelum atau setelah aksi tertentu
    protected static function boot()
    {
        parent::boot();

        // Ketika data peminjaman diambil, periksa status dan perbarui jika perlu
        static::retrieved(function ($peminjaman) {
            if ($peminjaman->status === 'Dipinjam' && $peminjaman->tanggal_pengembalian < Carbon::now()) {
                $peminjaman->update(['status' => 'Terlambat']);
            }
            
        });
    }
    

    // Method untuk meminjamkan dokumen
    public function pinjam()
    {
        $this->status = 'Dipinjam';
        $this->save();
    }

    // Method untuk mengembalikan dokumen
    public function kembalikan()
    {
        $this->tanggal_pengembalian = now();
        $this->status = 'Dikembalikan';
        
        // Perbarui denda berdasarkan keterlambatan pengembalian
        $this->denda = $this->getDendaAttribute();
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

    // Method untuk menghitung denda
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

    public function tandaiDikembalikan()
    {
        $this->update([
            'status_pengembalian' => true,
            'tanggal_dikembalikan' => Carbon::now(),
            'sanksi' => $this->denda // Ambil denda sesuai keterlambatan
        ]);
    }
}
