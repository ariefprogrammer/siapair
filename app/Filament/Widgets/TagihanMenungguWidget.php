<?php

namespace App\Filament\Widgets;

use App\Models\Tagihan;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class TagihanMenungguWidget extends BaseWidget
{
    protected static ?string $heading = 'Tagihan Belum Dibayar (Terlama)';
    protected static ?int $sort       = 4;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Tagihan::belumDibayar()
                    ->with(['pelanggan', 'periode'])
                    ->oldest('created_at')
            )
            ->columns([
                Tables\Columns\TextColumn::make('pelanggan.nomor_sambungan')
                    ->label('No. Sambungan')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('pelanggan.nama')
                    ->label('Pelanggan')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('pelanggan.wilayah')
                    ->label('Wilayah')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('periode.label')
                    ->label('Periode')
                    ->getStateUsing(fn (Tagihan $record) => $record->periode->labelBulan()),

                Tables\Columns\TextColumn::make('total_tagihan')
                    ->label('Total')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.')),

                Tables\Columns\TextColumn::make('tanggal_jatuh_tempo')
                    ->label('Jatuh Tempo')
                    ->date('d M Y')
                    ->color(fn (Tagihan $record) =>
                        $record->tanggal_jatuh_tempo?->isPast() ? 'danger' : 'warning'
                    ),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tagihan Dibuat')
                    ->since()
                    ->color('gray'),
            ])
            ->paginated([5, 10, 25])
            ->defaultPaginationPageOption(5);
    }
}