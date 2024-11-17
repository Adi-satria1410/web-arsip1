<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NotificationsResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Notification;


class NotificationsResource extends Resource
{
    protected static ?string $model = Notification::class;
    protected static ?string $navigationIcon = 'heroicon-o-bell';
    protected static ?string $pluralLabel = 'Nottification';
    protected static ?string $navigationGroup = 'Core';


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
                Tables\Columns\TextColumn::make('status_permohonan')
                    ->label('Status Permohonan')
                    ->formatStateUsing(fn($state) => $state ?? 'Belum Diterima'),

                Tables\Columns\TextColumn::make('aksi')
                    ->label('Aksi')


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
            ->actions([
                Tables\Actions\Action::make('accept')
                    ->label('✓ Terima')
                    ->color('success')
                    ->action(function (Notification $record) {
                        $record->update(['status_permohonan' => 'Diterima']);
                    })
                    ->requiresConfirmation(), // Konfirmasi sebelum mengubah status

                Tables\Actions\Action::make('reject')
                    ->label('✕ Tolak')
                    ->color('danger')
                    ->action(function (Notification $record) {
                        $record->update(['status_permohonan' => 'Ditolak']);
                        // Ambil data peminjaman terkait
                        $peminjaman = \App\Models\Peminjaman::where('nomor_dokumen', $record->nomor_dokumen)->first();

                        if ($peminjaman) {
                            // Update status peminjaman menjadi 'Ditolak'
                            $peminjaman->update(['status' => 'Ditolak']);
                        }
                    })
                    ->requiresConfirmation(), // Konfirmasi sebelum mengubah status

                Tables\Actions\Action::make('print_pdf')
                    ->label('Cetak PDF')
                    ->icon('heroicon-o-printer')
                    ->action(function (Notification $record) {
                        $pdf = Pdf::loadView('notifications.pdf', [
                            'notification' => $record,
                        ]);
                        $namaPeminjam = preg_replace('/[^A-Za-z0-9_\-]/', '_', $record->peminjam); 
        $fileName = "Peminjaman_{$namaPeminjam}.pdf";

        // Kembalikan PDF untuk diunduh
        return response()->streamDownload(
            fn() => print($pdf->output()),
            $fileName
        );
                    })
                    ->color('primary'),

            ])


            ->bulkActions([

            ]);


    }




    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNotifications::route('/'),
            'create' => Pages\CreateNotifications::route('/create'),
            'edit' => Pages\EditNotifications::route('/{record}/edit'),
        ];
    }
}
