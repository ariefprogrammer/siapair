<?php

namespace App\Filament\Resources\PelangganResource\RelationManagers;

use App\Models\Pembayaran;
use App\Models\Tagihan;
use App\Services\PembayaranService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class TagihanRelationManager extends RelationManager
{
    protected static string $relationship = 'tagihan';
    protected static ?string $title       = 'Riwayat Tagihan';
    protected static ?string $icon        = 'heroicon-o-document-text';

    public function form(Form $form): Form
    {
        // Tagihan tidak dibuat manual dari sini — generated dari catatan meter
        return $form->schema([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('periode.label')
                    ->label('Periode')
                    ->getStateUsing(fn (Tagihan $record) => $record->periode->labelBulan())
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('total_pemakaian')
                    ->label('Pemakaian')
                    ->formatStateUsing(fn ($state) => number_format($state, 2) . ' m³')
                    ->sortable(),

                Tables\Columns\TextColumn::make('biaya_air')
                    ->label('Biaya Air')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('biaya_admin')
                    ->label('Biaya Admin')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('total_tagihan')
                    ->label('Total')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->sortable()
                    ->weight('semibold'),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => 'lunas',
                        'warning' => 'menunggu_verifikasi',
                        'danger'  => 'belum_dibayar',
                    ])
                    ->formatStateUsing(fn (Tagihan $record) => $record->labelStatus()),

                Tables\Columns\TextColumn::make('pembayaran.metode')
                    ->label('Metode')
                    ->badge()
                    ->colors([
                        'info'    => 'tunai',
                        'success' => 'qris',
                    ])
                    ->formatStateUsing(fn ($state) => $state ? strtoupper($state) : '-')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('tanggal_jatuh_tempo')
                    ->label('Jatuh Tempo')
                    ->date('d M Y')
                    ->placeholder('-')
                    ->color(fn (Tagihan $record) =>
                        $record->tanggal_jatuh_tempo?->isPast() && ! $record->isLunas()
                            ? 'danger'
                            : 'gray'
                    ),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'belum_dibayar'       => 'Belum Dibayar',
                        'menunggu_verifikasi' => 'Menunggu Verifikasi',
                        'lunas'               => 'Lunas',
                    ]),
            ])
            ->headerActions([
                // Tagihan tidak dibuat manual, hanya informasi
            ])
            ->actions([
                // Lihat breakdown tarif
                Tables\Actions\Action::make('detail')
                    ->label('Breakdown')
                    ->icon('heroicon-o-calculator')
                    ->color('info')
                    ->modalHeading(fn (Tagihan $record) => 'Rincian Tagihan — ' . $record->periode->labelBulan())
                    ->modalContent(fn (Tagihan $record) => view(
                        'filament.modals.breakdown-tagihan',
                        ['tagihan' => $record]
                    ))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup'),

                // Lihat bukti bayar QRIS jika ada
                Tables\Actions\Action::make('bukti_bayar')
                    ->label('Bukti Bayar')
                    ->icon('heroicon-o-photo')
                    ->color('gray')
                    ->visible(fn (Tagihan $record) =>
                        $record->pembayaran?->isQris() && $record->pembayaran->bukti_bayar_path
                    )
                    ->modalHeading('Bukti Pembayaran QRIS')
                    ->modalContent(fn (Tagihan $record) => view(
                        'filament.modals.bukti-bayar',
                        [
                            'pembayaran' => $record->pembayaran,
                            'url'        => asset('storage/' . $record->pembayaran->bukti_bayar_path),
                        ]
                    ))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup'),

                // Verifikasi QRIS langsung dari sini
                Tables\Actions\Action::make('setujui_qris')
                    ->label('Setujui QRIS')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Setujui pembayaran QRIS?')
                    ->visible(fn (Tagihan $record) =>
                        $record->isMenungguVerifikasi() && $record->pembayaran?->isPending()
                    )
                    ->action(function (Tagihan $record) {
                        app(PembayaranService::class)->verifikasiQris(
                            pembayaran: $record->pembayaran,
                            disetujui: true,
                            adminId: auth()->id(),
                        );

                        Notification::make()
                            ->title('Pembayaran disetujui — tagihan berubah menjadi Lunas.')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('tolak_qris')
                    ->label('Tolak QRIS')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Tagihan $record) =>
                        $record->isMenungguVerifikasi() && $record->pembayaran?->isPending()
                    )
                    ->form([
                        Forms\Components\Textarea::make('catatan_verifikasi')
                            ->label('Alasan Penolakan')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (Tagihan $record, array $data) {
                        app(PembayaranService::class)->verifikasiQris(
                            pembayaran: $record->pembayaran,
                            disetujui: false,
                            adminId: auth()->id(),
                            catatan: $data['catatan_verifikasi'],
                        );

                        Notification::make()
                            ->title('Pembayaran ditolak.')
                            ->danger()
                            ->send();
                    }),

                // Tandai lunas manual (override — untuk kasus luar biasa)
                Tables\Actions\Action::make('tandai_lunas')
                    ->label('Tandai Lunas')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Tandai tagihan ini sebagai Lunas?')
                    ->modalDescription('Gunakan hanya jika pembayaran dilakukan di luar sistem (transfer langsung, dsb.). Tindakan ini tidak dapat dibatalkan.')
                    ->visible(fn (Tagihan $record) => $record->isBelumDibayar())
                    ->action(function (Tagihan $record) {
                        Pembayaran::create([
                            'tagihan_id'    => $record->id,
                            'metode'        => 'tunai',
                            'jumlah_bayar'  => $record->total_tagihan,
                            'teller_id'     => auth()->id(),
                            'nomor_nota'    => 'MANUAL-' . now()->format('YmdHis'),
                            'tanggal_bayar' => now(),
                        ]);

                        $record->update(['status' => 'lunas']);

                        Notification::make()
                            ->title('Tagihan berhasil ditandai Lunas.')
                            ->success()
                            ->send();
                    }),
            ])
            ->emptyStateHeading('Belum ada tagihan')
            ->emptyStateDescription('Tagihan akan muncul setelah operator menginput catatan meter.');
    }
}