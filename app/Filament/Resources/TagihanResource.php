<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TagihanResource\Pages;
use App\Models\Tagihan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TagihanResource extends Resource
{
    protected static ?string $model = Tagihan::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-check';
    protected static ?string $navigationGroup = 'Pembayaran'; 
    protected static ?string $navigationLabel = 'Data Tagihan';
    protected static ?string $modelLabel = 'Tagihan';
    protected static ?string $pluralModelLabel = 'Data Tagihan';

    public static function getEloquentQuery(): Builder
    {
        // Optimasi performa database dengan eager loading relasi terkait
        return parent::getEloquentQuery()->with(['pelanggan', 'periode']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('pelanggan_nama')
                            ->label('Nama Pelanggan')
                            ->placeholder(fn (Tagihan $record) => $record->pelanggan?->nama)
                            ->disabled(),

                        Forms\Components\TextInput::make('periode_label')
                            ->label('Periode Bulan')
                            ->placeholder(fn (Tagihan $record) => $record->periode?->labelBulan())
                            ->disabled(),

                        Forms\Components\TextInput::make('catatanMeter.angka_meter_lalu')
                            ->label('Angka Meter Lalu (m³)')
                            ->numeric()
                            ->required(),

                        Forms\Components\TextInput::make('catatanMeter.angka_meter_sekarang')
                            ->label('Angka Meter Sekarang (m³)')
                            ->numeric()
                            ->required(),

                        Forms\Components\TextInput::make('biaya_air')
                            ->label('Biaya Air')
                            ->numeric()
                            ->prefix('Rp')
                            ->disabled(),

                        Forms\Components\TextInput::make('catatanMeter.status_kondisi')
                            ->label('Status Kondisi Meter')
                            ->disabled(),

                        Forms\Components\TextInput::make('total_tagihan')
                            ->label('Total Tagihan')
                            ->numeric()
                            ->prefix('Rp')
                            ->disabled()
                            ->columnSpanFull(),

                        Forms\Components\Select::make('status')
                            ->label('Status Pembayaran')
                            ->options([
                                'belum_dibayar' => 'Belum Dibayar',
                                'menunggu_verifikasi' => 'Menunggu Verifikasi',
                                'lunas' => 'Lunas',
                            ])
                            ->native(false)
                            ->required(),

                        Forms\Components\DatePicker::make('tanggal_jatuh_tempo')
                            ->label('Tanggal Jatuh Tempo')
                            ->required(),

                        Forms\Components\TextInput::make('diubahOleh.name')
                            ->label('Terakhir Diubah Oleh')
                            ->placeholder(fn (Tagihan $record) => $record->diubahOleh
                                ? $record->diubahOleh->name . ' - Pada ' . $record->updated_at->format('d M Y, H:i')
                                : 'Belum pernah diubah')
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('pelanggan.nama')
                    ->label('Pelanggan')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn (Tagihan $record) => $record->pelanggan?->nomor_sambungan),

                Tables\Columns\TextColumn::make('periode_id')
                    ->label('Periode')
                    ->formatStateUsing(function ($record) {
                        // Memastikan relasi periode ada, lalu gabungkan kolom 'bulan' dan 'tahun'
                        return $record->periode 
                            ? "{$record->periode->bulan}/{$record->periode->tahun}" 
                            : '—';
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_pemakaian')
                    ->label('Pemakaian')
                    ->suffix(' m³')
                    ->alignEnd(),

                Tables\Columns\TextColumn::make('total_tagihan')
                    ->label('Total Tagihan')
                    ->money('IDR', locale: 'id') // Format otomatis ke mata uang Rupiah
                    ->sortable()
                    ->alignEnd(),

                Tables\Columns\SelectColumn::make('status')
                    ->label('Status')
                    ->options([
                        'belum_dibayar' => 'Belum Dibayar',
                        'menunggu_verifikasi' => 'Menunggu Verifikasi',
                        'lunas' => 'Lunas',
                    ])
                    ->selectablePlaceholder(false),

                Tables\Columns\TextColumn::make('tanggal_jatuh_tempo')
                    ->label('Jatuh Tempo')
                    ->date('d M Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                // Filter cepat berdasarkan status tagihan
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'belum_dibayar' => 'Belum Dibayar',
                        'menunggu_verifikasi' => 'Menunggu Verifikasi',
                        'lunas' => 'Lunas',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTagihans::route('/'),
            'edit' => Pages\EditTagihan::route('/{record}/edit'),
            // Karena kita tidak memakai halaman create, route create sengaja dihilangkan di sini
        ];
    }
}