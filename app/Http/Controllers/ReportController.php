<?
namespace App\Http\Controllers;

use App\Models\Peminjaman;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Report; 
use Request;// Pastikan model sesuai dengan data yang diambil

class ReportController extends Controller
{
    public function generatePdf($id)
    {
        // Ambil data report berdasarkan ID
        $report = Report::find($id);

        // Pastikan data ditemukan
        if (!$report) {
            return response()->json(['message' => 'Report tidak ditemukan'], 404);
        }

        // Persiapkan data untuk dikirim ke view
        $data = [
            'report' => $report,
        ];

        // Generate PDF menggunakan DomPDF
        $pdf = Pdf::loadView('filament.reports.pdf', $data);

        // Kembalikan file PDF untuk di-download
        return $pdf->download('report_' . $id . '.pdf');
    }
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
            'denda' => 'required|numeric',
        ]);

        // Cari peminjaman berdasarkan ID
        $peminjaman = Peminjaman::findOrFail($id);
// Jika status berubah menjadi 'Dikembalikan', reset denda ke 0
if ($request->status == 'Dikembalikan') {
    $validated['denda'] = 0;
}

        // Update data peminjaman
        $peminjaman->update($validated);

        // Kembali ke halaman laporan dengan pesan sukses
        return redirect()->route('report.index')->with('success', 'Peminjaman berhasil diperbarui!');
    }
}
