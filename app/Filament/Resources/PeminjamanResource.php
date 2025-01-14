<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PeminjamanResource\Pages;
use App\Models\Peminjaman;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\DB;


class PeminjamanResource extends Resource
{
    // Menetapkan model dan ikon
    protected static ?string $model = Peminjaman::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Manajemen Document';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Pilihan jenis dokumen
                Forms\Components\Select::make('jenis_dokumen')
                    ->options([
                        'BT' => 'Buku Tanah',
                        'SU' => 'Surat Ukur',
                        'warkah' => 'Warkah',
                    ])
                    ->label('Jenis Dokumen')
                    ->required(),

                Forms\Components\TextInput::make('nomor_dokumen')
                    ->label('Nomor Dokumen')
                    ->required(),

                Forms\Components\TextInput::make('peminjam')
                    ->label('Peminjam')
                    ->required(),

                Forms\Components\DatePicker::make('tanggal_peminjaman')
                    ->label('Tanggal Peminjaman')
                    ->required(),

                Forms\Components\DatePicker::make('tanggal_pengembalian')
                    ->label('Tanggal Pengembalian')
                    ->nullable(),

                // Combo box Kabupaten
                Forms\Components\Select::make('regency_id')
                    ->label('Kabupaten')
                    ->options(\App\Models\Regency::all()->pluck('name', 'id'))
                    ->reactive()
                    ->afterStateUpdated(fn(callable $set) => $set('district_id', null)),

                // Combo box Kecamatan yang bergantung pada Kabupaten
                Forms\Components\Select::make('district_id')
                    ->label('Kecamatan')
                    ->options(function (callable $get) {
                        $regencyId = $get('regency_id');
                        if ($regencyId) {
                            return \App\Models\District::where('regency_id', $regencyId)
                                ->pluck('name', 'id');
                        }
                        return [];
                    })
                    ->reactive()
                    ->afterStateUpdated(fn(callable $set) => $set('village_id', null)),

                // Combo box Desa yang bergantung pada Kecamatan
                Forms\Components\Select::make('village_id')
                    ->label('Desa')
                    ->options(function (callable $get) {
                        $districtId = $get('district_id');
                        if ($districtId) {
                            return \App\Models\Village::where('district_id', $districtId)
                                ->pluck('name', 'id');
                        }
                        return [];
                    })
                    ->reactive()
                    ->required(),

                // Status
                Forms\Components\Select::make('status')
                    ->options([
                        'Dipinjam' => 'Dipinjam',

                    ])
                    ->label('Status')
                    ->default('Dipinjam')
                    ->required(),
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

                // Kolom status pengembalian dengan ikon ceklis
                IconColumn::make('status_pengembalian')
                    ->label('Status Pengembalian')
                    ->boolean()
                    ->trueIcon('heroicon-s-check-circle')
                    ->trueColor('success')
                    ->falseIcon('heroicon-s-x-circle') // Ikon silang untuk status belum dikembalikan
                    ->falseColor('danger')
                    ->sortable()
                    ->default(false),

                // Kolom denda
                Tables\Columns\TextColumn::make('denda')
                    ->label('Sanksi')
                    ->getStateUsing(fn($record) => $record->denda),

                // Kolom aksi
                Tables\Columns\TextColumn::make('aksi')
                    ->label('Aksi')

            ])


            ->actions([
                Tables\Actions\EditAction::make(),

                // Aksi untuk menandai sebagai sudah dikembalikan
                Action::make('Dikembalikan')
                    ->label('Telah Dikembalikan')
                    ->icon('heroicon-s-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (Peminjaman $record) {
                        $hari_terlambat = Carbon::now()->diffInDays($record->tanggal_pengembalian, false);
                        if (!$record->status_pengembalian && $hari_terlambat > 0) {
                            if ($hari_terlambat >= 7) {
                                $sanksi = 'Peringatan';
                            } elseif ($hari_terlambat >= 14) {
                                $sanksi = 'Penundaan Hak';
                            } else {
                                $sanksi = 'Skorsing';
                            }
                        } elseif (!$record->status_pengembalian && $hari_terlambat <= 0) {
                            $sanksi = 'Belum terlambat, peminjaman masih valid';
                        }
                        // Jika sudah dikembalikan, bisa diberikan status normal
                        else {
                            $sanksi = 'Pengembalian sudah dilakukan';
                        }
                        // Update status pengembalian dan tanggal dikembalikan
                        $record->update([
                            'status_pengembalian' => true,
                            'tanggal_dikembalikan' => Carbon::now(), // Set tanggal dikembalikan dengan tanggal hari ini
                            'sanksi' => $sanksi
                        ]);

                        // Ubah status menjadi "Dikembalikan"
                        $record->status = 'Dikembalikan';
                        $record->save();

                    })
                    ->hidden(fn(Peminjaman $record) => $record->status === 'Dikembalikan'),
                // Sembunyikan jika sudah dikembalikan
                Tables\Actions\Action::make('print_pdf')
                    ->label('Cetak PDF')
                    ->icon('heroicon-o-printer')
                    ->action(function (Peminjaman $record) {
                        $pdf = Pdf::loadView('peminjaman.pdf', [
                            'peminjaman' => $record,
                        ]);

                        return response()->streamDownload(
                            fn() => print ($pdf->output()),
                            "peminjaman-{$record->id}.pdf"
                        );
                    })
                    ->color('primary'),

            ])

            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }


    // Label untuk judul halaman
    public static function getPluralLabel(): string
    {
        return 'Peminjaman';
    }

    public static function getLabel(): string
    {
        return 'Peminjaman';
    }

    public static function getRelations(): array
    {
        return [
            // Tambahkan relasi jika diperlukan
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPeminjaman::route('/'),
            'create' => Pages\CreatePeminjaman::route('/create'),
            'edit' => Pages\EditPeminjaman::route('/{record}/edit'),
        ];
    }
}