<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PembayaranTunaiResource\Pages;
use App\Models\Pembayaran;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PembayaranTunaiResource extends Resource
{
    protected static ?string $model = Pembayaran::class; 
    protected static ?string $navigationIcon  = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Pembayaran';
    protected static ?string $navigationLabel = 'Cash Payments'; 
    protected static ?string $modelLabel      = 'Cash Payments';
    protected static ?int $navigationSort     = 1;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('metode', 'tunai')
            ->with(['tagihan.pelanggan', 'tagihan.periode', 'adminVerifikasi', 'teller']);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tagihan.pelanggan.nama')
                    ->label('Pelanggan')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('tagihan.pelanggan.nomor_sambungan')
                    ->label('No. Sambungan')
                    ->searchable()
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('tagihan.periode.label')
                    ->label('Periode')
                    ->getStateUsing(fn (Pembayaran $record) => $record->tagihan->periode->labelBulan()),

                Tables\Columns\TextColumn::make('jumlah_bayar')
                    ->label('Jumlah')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('teller.name')
                    ->label('Teller')
                    ->placeholder('-')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('tanggal_bayar')
                    ->label('Tgl. Bayar')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('adminVerifikasi.name')
                    ->label('Diverifikasi Oleh')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('tanggal_verifikasi')
                    ->label('Tgl. Verifikasi')
                    ->dateTime('d M Y, H:i')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('tanggal_bayar', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status_verifikasi')
                    ->label('Status')
                    ->options([
                        'pending'   => 'Menunggu Verifikasi',
                        'disetujui' => 'Disetujui',
                        'ditolak'   => 'Ditolak',
                    ]),

                // Filter 4 Bulan Terakhir Aktif secara Default
                Tables\Filters\Filter::make('tanggal_bayar')
                    ->label('4 Bulan Terakhir')
                    ->default() 
                    ->query(function (Builder $query) {
                        return $query->where('tanggal_bayar', '>=', now()->subMonths(4)->startOfMonth());
                    })
            ])
            ->actions([
                // ... (Sesuaikan tindakan/actions yang relevan untuk tunai)
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPembayaranTunais::route('/'),
        ];
    }
}