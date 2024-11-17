<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Notification;

class CustomStatsWidget extends StatsOverviewWidget
{
    protected function getCards(): array
    {
        // Menghitung total data diterima, ditolak, dan semua notifikasi
        $totalDiterima = Notification::where('status_permohonan', 'Diterima')->count();
        $totalDitolak = Notification::where('status_permohonan', 'Ditolak')->count();
        $totalSemua = Notification::count(); // Menghitung semua notifikasi, tanpa memperhatikan status

        return [
            // Menambahkan warna hijau dengan ikon untuk "Diterima"
            Stat::make('Total Diterima', $totalDiterima)
                ->color('success') // Warna hijau
                ->icon('heroicon-o-check-circle') // Ikon ceklis
                ->description('Notifikasi yang diterima'),

            // Menambahkan warna merah dengan ikon untuk "Ditolak"
            Stat::make('Total Ditolak', $totalDitolak)
                ->color('danger') // Warna merah
                ->icon('heroicon-o-x-circle') // Ikon silang
                ->description('Notifikasi yang ditolak'),

            // Menambahkan warna biru dengan ikon untuk "Total Semua"
            Stat::make('Total Semua', $totalSemua)
                ->color('primary') // Warna biru
                ->icon('heroicon-o-folder') // Ikon folder (sebagai pengganti collection)
                ->description('Semua notifikasi yang ada'),
        ];
    }
}