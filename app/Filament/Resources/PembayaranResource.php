<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PembayaranResource\Pages;
use App\Models\Pembayaran;
use App\Services\PembayaranService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class PembayaranResource extends Resource
{
    protected static ?string $model = Pembayaran::class;
    protected static ?string $navigationIcon  = 'heroicon-o-qr-code';
    protected static ?string $navigationGroup = 'Pembayaran';
    protected static ?string $navigationLabel = 'QRIS Payments';
    protected static ?string $modelLabel      = 'QRIS Payments';
    protected static ?int $navigationSort     = 1;

    // Hanya tampilkan pembayaran QRIS
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('metode', 'qris')
            ->with(['tagihan.pelanggan', 'tagihan.periode', 'adminVerifikasi']);
    }

    // Badge di nav untuk menunjukkan jumlah pending
    public static function getNavigationBadge(): ?string
    {
        $count = Pembayaran::where('metode', 'qris')
            ->where('status_verifikasi', 'pending')
            ->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): string
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            // Read-only — verifikasi dilakukan via Action, bukan form edit
        ]);
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

                Tables\Columns\BadgeColumn::make('status_verifikasi')
                    ->label('Status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'disetujui',
                        'danger'  => 'ditolak',
                    ])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'pending'   => 'Menunggu',
                        'disetujui' => 'Disetujui',
                        'ditolak'   => 'Ditolak',
                        default     => '-',
                    }),

                Tables\Columns\TextColumn::make('tanggal_bayar')
                    ->label('Tgl. Upload')
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

                // Filter default untuk 4 bulan terakhir
                Tables\Filters\Filter::make('tanggal_bayar')
                    ->label('4 Bulan Terakhir')
                    ->default() // Mengaktifkan filter ini secara default saat halaman dimuat
                    ->query(function (Builder $query) {
                        return $query->where('tanggal_bayar', '>=', now()->subMonths(4)->startOfMonth());
                    })
            ])
            ->actions([
                // Tombol lihat bukti bayar
                Tables\Actions\Action::make('lihat_bukti')
                    ->label('Lihat Bukti')
                    ->icon('heroicon-o-photo')
                    ->color('info')
                    ->modalHeading('Bukti Pembayaran QRIS')
                    ->modalContent(function (Pembayaran $record) {
                        $url = $record->bukti_bayar_path
                            ? asset('storage/' . $record->bukti_bayar_path)
                            : null;

                        return view('filament.modals.bukti-bayar', [
                            'pembayaran' => $record,
                            'url'        => $url,
                        ]);
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup'),

                // Tombol setujui
                Tables\Actions\Action::make('setujui')
                    ->label('Setujui')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Setujui Pembayaran?')
                    ->modalDescription(fn (Pembayaran $record) =>
                        "Setujui pembayaran QRIS sebesar Rp " .
                        number_format($record->jumlah_bayar, 0, ',', '.') .
                        " dari " . $record->tagihan->pelanggan->nama . "? " .
                        "Status tagihan akan berubah menjadi Lunas."
                    )
                    ->visible(fn (Pembayaran $record) => $record->isPending())
                    ->action(function (Pembayaran $record) {
                        app(PembayaranService::class)->verifikasiQris(
                            pembayaran: $record,
                            disetujui: true,
                            adminId: auth()->id(),
                        );

                        Notification::make()
                            ->title('Pembayaran disetujui.')
                            ->body($record->tagihan->pelanggan->nama . ' — tagihan berubah menjadi Lunas.')
                            ->success()
                            ->send();
                    }),

                // Tombol tolak + form catatan
                Tables\Actions\Action::make('tolak')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Pembayaran $record) => $record->isPending())
                    ->form([
                        Forms\Components\Textarea::make('catatan_verifikasi')
                            ->label('Alasan Penolakan')
                            ->placeholder('Contoh: Bukti bayar tidak jelas / nominal tidak sesuai.')
                            ->required()
                            ->rows(3),
                    ])
                    ->modalHeading('Tolak Pembayaran')
                    ->modalDescription('Masukkan alasan penolakan. Pelanggan akan melihat catatan ini.')
                    ->action(function (Pembayaran $record, array $data) {
                        app(PembayaranService::class)->verifikasiQris(
                            pembayaran: $record,
                            disetujui: false,
                            adminId: auth()->id(),
                            catatan: $data['catatan_verifikasi'],
                        );

                        Notification::make()
                            ->title('Pembayaran ditolak.')
                            ->body('Tagihan ' . $record->tagihan->pelanggan->nama . ' kembali ke Belum Dibayar.')
                            ->danger()
                            ->send();
                    }),
            ])
            ->bulkActions([
                // Bulk setujui untuk efisiensi
                Tables\Actions\BulkAction::make('bulk_setujui')
                    ->label('Setujui Dipilih')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Setujui semua pembayaran yang dipilih?')
                    ->deselectRecordsAfterCompletion()
                    ->action(function (\Illuminate\Support\Collection $records) {
                        $service = app(PembayaranService::class);
                        $count   = 0;

                        foreach ($records as $record) {
                            if ($record->isPending()) {
                                $service->verifikasiQris(
                                    pembayaran: $record,
                                    disetujui: true,
                                    adminId: auth()->id(),
                                );
                                $count++;
                            }
                        }

                        Notification::make()
                            ->title("{$count} pembayaran berhasil disetujui.")
                            ->success()
                            ->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPembayarans::route('/'),
        ];
    }
}