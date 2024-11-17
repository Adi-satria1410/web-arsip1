<?php
namespace App\Filament\Resources\ReportResource\Pages;

use App\Filament\Resources\ReportResource;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Barryvdh\DomPDF\Facade\Pdf;

class ListReports extends ListRecords
{
    protected static string $resource = ReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('cetak_pdf')
                ->label('Cetak PDF')
                ->icon('heroicon-o-printer')
                ->action(function () {
                    // Ambil data peminjaman dari database
                    $reports = \App\Models\Peminjaman::all();

                    $data = [
                        'title' => 'Laporan Peminjaman',
                        'reports' => $reports,
                    ];

                    $pdf = Pdf::loadView('filament.reports.pdf', $data);

                    return response()->streamDownload(function () use ($pdf) {
                        echo $pdf->output();
                    }, 'laporan_peminjaman.pdf');
                    //                 $data = ['title'=>'helloword'];
                    //                 $pdf = Pdf::loadView('filament.reports.pdf', $data);
                    // return $pdf->download('invoice.pdf');
                    // // Mengambil record yang dipilih
                    // $selected = $this->getBulkSelected();  // Memanggil metode getSelectedRecords()
        
                    // // Pastikan ada record yang dipilih
                    // if ($selected->count() > 0) {
                    //     // Panggil controller untuk generate PDF
                    //     $controller = new ReportController();
                    //     return $controller->generatePdf($selected->first()->id); // Panggil fungsi generatePdf dengan ID pertama
                    // }
        
                    // return null; // Jika tidak ada record yang dipilih
                }),


        ];
    }

    public function getTabs(): array
    {
        return [
            'peminjaman' => ListRecords\Tab::make('Data Peminjaman')
                ->query(fn($query) => $query->where('status', 'Dipinjam')),

            'keterlambatan' => ListRecords\Tab::make('Data Keterlambatan')
                ->query(fn($query) => $query->where('status', 'Terlambat')
                    ->where('tanggal_pengembalian', '<', now())),

            'pengembalian' => ListRecords\Tab::make('Data Pengembalian')
                ->query(fn($query) => $query->where('status', 'Dikembalikan')),
        ];
    }

}
