<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use App\Models\Regency;
use App\Models\District;
use App\Models\Village;

class DocumentController extends Controller
{
    public function create()
    {
        $regencies = Regency::all(); // Ambil data kabupaten
        return view('documents.create', compact('regencies'));
    }

    public function getDistricts($regencyId)
    {
        $districts = District::where('regency_id', $regencyId)->pluck('name', 'id');
        return response()->json($districts);
    }


    public function getVillages($districtId)
    {
        $villages = Village::where('district_id', $districtId)->pluck('name', 'id');
        return response()->json($villages);
    }



    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'jenis_dokumen' => 'required|array',
            'jenis_no_hak_di_208' => 'nullable|string', // Validasi input lainnya
            'nomor_dokumen' => 'nullable|string',
             'regency_id' => 'required|integer',
            'district_id' => 'required|integer',
            'village_id' => 'required|integer',
            'peminjam' => 'required|string',
            'keperluan' => 'required|string',
            'tanggal_peminjaman' => 'required|date',
            'tanggal_pengembalian' => 'required|date',
            'di_301' => 'nullable|string',
            'di_302_303' => 'nullable|string',
        ]);
        try {
            // Simpan data peminjaman
            $peminjaman = Peminjaman::create([
                'jenis_dokumen' => json_encode($request->jenis_dokumen), // Menyimpan array jenis dokumen sebagai JSON
                'jenis_no_hak_di_208' => $request->jenis_no_hak_di_208,
                'nomor_dokumen' => $request->nomor_dokumen,
                'regency_id' => $request->regency_id,
                'district_id' => $request->district_id,
                'village_id' => $request->village_id,
                'peminjam' => $request->peminjam,
                'keperluan' => $request->keperluan,
                'tanggal_peminjaman' => $request->tanggal_peminjaman,
                'tanggal_pengembalian' => $request->tanggal_pengembalian,
                'di_301' => $request->di_301,
                'di_302_303' => $request->di_302_303,
            ]);

            return redirect()->route('documents.create')->with('success', 'Dokumen berhasil disimpan');
        } catch (\Exception $e) {
            \Log::error('Error saving document: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}