<?php

namespace App\Filament\Resources\PeminjamanResource\Pages;

use App\Filament\Resources\PeminjamanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\View;

class ListPeminjaman extends ListRecords
{
    protected static string $resource = PeminjamanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Tambah Peminjaman'),
            
        ];
    }
    
    public function getTabs(): array
    {
        return [
            'peminjaman' => ListRecords\Tab::make('Semua Peminjaman')
            ->query(fn($query) => $query),
    
            'data_diterima' => ListRecords\Tab::make('Data Diterima')
                ->query(fn($query) => $query->where('status_permohonan', 'Diterima')),
                    
            'data_ditolak' => ListRecords\Tab::make('Data Ditolak')
                ->query(fn($query) => $query->where('status_permohonan', 'Ditolak'))
        ];
    }
    
    
}
