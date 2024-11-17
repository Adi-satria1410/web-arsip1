<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReportResource\Pages;
use App\Models\Report;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Exports\ReportExport;
use Barryvdh\DomPDF\Facade\Pdf;


class ReportResource extends Resource
{
    protected static ?string $model = Report::class;
    protected static ?string $navigationIcon = 'heroicon-o-document';
    protected static ?string $pluralLabel = 'Laporan';
    protected static ?string $navigationGroup = 'Manajemen Document';
    

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('jenis_dokumen')->label('Jenis Dokumen'),
                Tables\Columns\TextColumn::make('nomor_dokumen')->label('Nomor Dokumen'),
                Tables\Columns\TextColumn::make('peminjam')->label('Nama Peminjam'),
                Tables\Columns\TextColumn::make('tanggal_peminjaman')->label('Tanggal Peminjaman')->date(),
                Tables\Columns\TextColumn::make('tanggal_pengembalian')->label('Tanggal Pengembalian')->date(),
                Tables\Columns\TextColumn::make('tanggal_dikembalikan')->label('Tanggal Dikembalikan')->date(),
                Tables\Columns\TextColumn::make('regency.name')->label('Kabupaten'),
                Tables\Columns\TextColumn::make('district.name')->label('Kecamatan'),
                Tables\Columns\TextColumn::make('village.name')->label('Desa'),
                Tables\Columns\TextColumn::make('status')->label('Status'),
                Tables\Columns\TextColumn::make('denda')
                    ->label('Sanksi')
                    ->getStateUsing(fn($record) => $record->denda),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status Peminjaman')
                    ->options([
                        '1' => 'Dipinjam',
                        '2' => 'Dikembalikan',
                    ]),
                Tables\Filters\Filter::make('tanggal_peminjaman')
                    ->label('Tanggal Peminjaman')
                    ->form([
                        Forms\Components\DatePicker::make('tanggal_peminjaman_start')->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('tanggal_peminjaman_end')->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['tanggal_peminjaman_start'], fn($query, $date) => $query->whereDate('tanggal_peminjaman', '>=', $date))
                            ->when($data['tanggal_peminjaman_end'], fn($query, $date) => $query->whereDate('tanggal_peminjaman', '<=', $date));
                    }),
            ])
            
            
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
           
                
    }




    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReports::route('/'),
            'create' => Pages\CreateReport::route('/create'),
            'edit' => Pages\EditReport::route('/{record}/edit'),
        ];
    }
}
