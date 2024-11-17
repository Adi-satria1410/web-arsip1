<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use Barryvdh\DomPDF\Facade\Pdf;
use DB;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PeminjamanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $peminjaman = Peminjaman::query();

        // Filter berdasarkan status
        if ($request->has('status') && $request->status) {
            $peminjaman = $peminjaman->where('status', $request->status);
        }

        // Filter berdasarkan tanggal peminjaman
        if ($request->has('tanggal_peminjaman') && $request->tanggal_peminjaman) {
            $peminjaman = $peminjaman->whereDate('tanggal_peminjaman', $request->tanggal_peminjaman);
        }

        // Ambil data peminjaman dengan pagination
        $peminjamanList = $peminjaman->paginate(10);

        return view('report', compact('peminjamanList'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('create_peminjaman'); // Menampilkan form untuk tambah peminjaman
    }
    public function generatePdf($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);

        $pdf = Pdf::loadView('peminjaman.pdf', compact('peminjaman'));

        $namaPeminjam = preg_replace('/[^A-Za-z0-9_\-]/', '_', $peminjaman->nama_peminjam);
        $fileName = "peminjaman-{$namaPeminjam}.pdf";
    
        // Unduh file PDF
        return $pdf->download($fileName);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi inputan
        $validated = $request->validate([
            'jenis_dokumen' => 'required|string',
            'nomor_dokumen' => 'required|string',
            'peminjam' => 'required|string',
            'tanggal_peminjaman' => 'required|date',
            'tanggal_pengembalian' => 'nullable|date',
            'status' => 'required|string',
            'denda' => 'nullable|string',
        ]);

        // Menyimpan data peminjaman baru
        Peminjaman::create($validated);

        return redirect()->route('report.index')->with('success', 'Peminjaman berhasil disimpan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Menampilkan detail peminjaman berdasarkan ID
        $peminjaman = Peminjaman::findOrFail($id);
        return view('show_peminjaman', compact('peminjaman'));
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        return view('edit_peminjaman', compact('peminjaman'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Validasi data yang diterima dari form
        $validated = $request->validate([
            'jenis_dokumen' => 'required|string',
            'nomor_dokumen' => 'required|string',
            'peminjam' => 'required|string',
            'tanggal_peminjaman' => 'required|date',
            'tanggal_pengembalian' => 'nullable|date',
            'status' => 'required|string',
            'denda' => 'nullable|string',
        ]);

        // Cari peminjaman berdasarkan ID
        $peminjaman = Peminjaman::findOrFail($id);

        // Logika untuk menentukan sanksi hanya jika terlambat
        if ($request->status == 'Dikembalikan') {
            $peminjaman->tanggal_pengembalian = $request->tanggal_pengembalian ?? now(); // Pastikan tanggal pengembalian diisi

            // Hitung selisih hari antara tanggal pengembalian dan tanggal hari ini
            $hariTerlambat = now()->diffInDays($peminjaman->tanggal_pengembalian, false); // false untuk menghitung selisih positif atau negatif

            // Cek apakah sanksi hanya diberikan jika terlambat
            if ($hariTerlambat >= 0) {
                return '0'; // Tidak ada denda jika dikembalikan tepat waktu
            } elseif ($hariTerlambat > 7) {
                return 'Peringatan'; // Keterlambatan 1-7 hari
            } elseif ($hariTerlambat > 10) {
                return 'Penundaan Hak'; // Keterlambatan 8-14 hari
            } elseif ($hariTerlambat > 15) {
                return 'mantap'; // Keterlambatan lebih dari 14 hari
            } else {
                return 'telat bangt';
            }
        }

        // Update data peminjaman
        $peminjaman->update($validated);

        // Kembali ke halaman laporan dengan pesan sukses
        return redirect()->route('report.index')->with('success', 'Peminjaman berhasil diperbarui!');
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
{
    $peminjaman = Peminjaman::findOrFail($id);
    $peminjaman->delete();

    // Jika tabel kosong, reset auto increment
    $lastId = Peminjaman::max('id'); // Ambil id terakhir
    DB::statement('ALTER TABLE peminjamen AUTO_INCREMENT = ' . ($lastId ? $lastId + 1 : 1));

    return redirect()->route('report.index')->with('success', 'Peminjaman berhasil dihapus!');
}




    /**
     * Tampilkan laporan peminjaman.
     */
    public function showLaporan()
    {
        $peminjamanList = Peminjaman::all();
        $peminjamanList->transform(function ($item) {
            $item->tanggal_peminjaman = Carbon::parse($item->tanggal_peminjaman);
            if ($item->tanggal_pengembalian) {
                $item->tanggal_pengembalian = Carbon::parse($item->tanggal_pengembalian);
            }
            return $item;
        });

        $pengembalianList = Peminjaman::whereNotNull('tanggal_pengembalian')->get();
        $keterlambatanList = Peminjaman::where('status', 'Dipinjam')
            ->where('tanggal_pengembalian', '<', now())
            ->get();

        return view('filament.pages.report', compact('peminjamanList', 'pengembalianList', 'keterlambatanList'));
    }

    /**
     * Generate PDF for a specific peminjaman.
     */

    public function ajukanPeminjaman(Request $request)
    {
        // Validasi data peminjaman
        $validated = $request->validate([
            'jenis_dokumen' => 'required|string',
            'nomor_dokumen' => 'required|string',
            'peminjam' => 'required|string',
            'tanggal_peminjaman' => 'required|date',
            'tanggal_pengembalian' => 'required|date',
        ]);

        // Simpan peminjaman
        $peminjaman = Peminjaman::create($validated);


        return redirect()->route('peminjaman.index')->with('success', 'Pengajuan peminjaman berhasil');
    }
}
