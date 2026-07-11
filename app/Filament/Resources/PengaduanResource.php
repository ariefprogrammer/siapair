<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PengaduanResource\Pages;
use App\Models\Pengaduan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use App\Models\PengaduanPesan;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PengaduanResource extends Resource
{
    protected static ?string $model = Pengaduan::class;
    protected static ?string $navigationIcon  = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $navigationGroup = 'Operasional';
    protected static ?string $navigationLabel = 'Pengaduan Pelanggan';
    protected static ?string $modelLabel      = 'Pengaduan';
    protected static ?int $navigationSort     = 2;

    // Badge jumlah pengaduan masuk yang belum direspons
    public static function getNavigationBadge(): ?string
    {
        $count = Pengaduan::where('status', 'masuk')->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): string
    {
        return 'danger';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make('pelanggan.nama')
                    ->label('Pelanggan')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn (Pengaduan $record) => $record->pelanggan?->nomor_sambungan ?? '-'),

                Tables\Columns\TextColumn::make('pelanggan.wilayah')
                    ->label('Wilayah')
                    ->badge()
                    ->color('info')
                    ->placeholder('-'),

                Tables\Columns\BadgeColumn::make('kategori')
                    ->label('Kategori')
                    ->colors([
                        'danger'  => 'teknis',
                        'info'    => 'administrasi',
                        'gray'    => 'lainnya',
                    ])
                    ->formatStateUsing(fn (Pengaduan $record) => $record->labelKategori()),

                Tables\Columns\TextColumn::make('deskripsi')
                    ->label('Deskripsi')
                    ->limit(60)
                    ->tooltip(fn (Pengaduan $record) => $record->deskripsi)
                    ->wrap(),

                Tables\Columns\IconColumn::make('lampiran_path')
                    ->label('Lampiran')
                    ->boolean()
                    ->trueIcon('heroicon-o-paper-clip')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('info')
                    ->falseColor('gray'),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'danger'  => 'masuk',
                        'warning' => 'diproses',
                        'success' => 'selesai',
                    ])
                    ->formatStateUsing(fn (Pengaduan $record) => $record->labelStatus()),

                Tables\Columns\TextColumn::make('admin.name')
                    ->label('Direspons Oleh')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('tanggal_respons')
                    ->label('Tgl. Respons')
                    ->dateTime('d M Y, H:i')
                    ->placeholder('—')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'masuk'    => 'Masuk',
                        'diproses' => 'Diproses',
                        'selesai'  => 'Selesai',
                    ])
                    ->default('masuk'),

                Tables\Filters\SelectFilter::make('kategori')
                    ->label('Kategori')
                    ->options([
                        'teknis'       => 'Teknis',
                        'administrasi' => 'Administrasi',
                        'lainnya'      => 'Lainnya',
                    ]),

                Tables\Filters\Filter::make('belum_direspons')
                    ->label('Belum Direspons')
                    ->query(fn (Builder $q) => $q->whereNull('respons_admin'))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\Action::make('thread')
                    ->label('Buka Thread')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('info')
                    ->modalHeading(fn (Pengaduan $record) =>
                        'Thread Pengaduan — ' . $record->pelanggan->nama .
                        ' (' . $record->labelKategori() . ')'
                    )
                    ->modalContent(function (Pengaduan $record) {
                        $record->load(['pesan.user']);
                        return view('filament.modals.thread-pengaduan', ['pengaduan' => $record]);
                    })
                    ->form(fn (Pengaduan $record) => $record->isSelesai() ? [] : [
                        Forms\Components\Textarea::make('pesan')
                            ->label('Balas')
                            ->required()
                            ->rows(3)
                            ->placeholder('Tulis balasan...'),

                        Forms\Components\Select::make('status')
                            ->label('Ubah Status')
                            ->options([
                                'masuk'    => 'Masuk',
                                'diproses' => 'Diproses',
                                'selesai'  => 'Selesai (Tutup Thread)',
                            ])
                            ->default($record->status)  // ← fix: tanpa closure
                            ->native(false)
                            ->required(),
                    ])
                    ->modalSubmitActionLabel('Kirim Balasan')
                    ->modalSubmitAction(fn (\Filament\Actions\StaticAction $action, Pengaduan $record) =>
                        $record->isSelesai() ? $action->hidden() : $action
                    )
                    ->modalCancelActionLabel('Tutup')
                    ->action(function (Pengaduan $record, array $data) {
                        if (empty($data['pesan'])) return;

                        PengaduanPesan::create([
                            'pengaduan_id' => $record->id,
                            'user_id'      => auth()->id(),
                            'pesan'        => $data['pesan'],
                        ]);

                        $record->update([
                            'status'          => $data['status'],
                            'respons_admin'   => $data['pesan'],
                            'admin_id'        => auth()->id(),
                            'tanggal_respons' => now(),
                        ]);

                        Notification::make()
                            ->title('Balasan terkirim.')
                            ->body($data['status'] === 'selesai' ? 'Thread pengaduan ditutup.' : 'Status diperbarui.')
                            ->success()
                            ->send();
                    }),

                // Tutup thread langsung tanpa balas
                Tables\Actions\Action::make('tutup')
                    ->label('Tutup')
                    ->icon('heroicon-o-lock-closed')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Tutup pengaduan ini?')
                    ->modalDescription('Thread akan dikunci dan pelanggan tidak bisa mengirim pesan baru.')
                    ->visible(fn (Pengaduan $record) => ! $record->isSelesai())
                    ->action(function (Pengaduan $record) {
                        $record->update([
                            'status'   => 'selesai',
                            'admin_id' => auth()->id(),
                        ]);

                        Notification::make()
                            ->title('Pengaduan ditutup.')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('tandai_diproses')
                    ->label('Tandai Diproses')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->deselectRecordsAfterCompletion()
                    ->action(function (\Illuminate\Support\Collection $records) {
                        $records
                            ->filter(fn (Pengaduan $r) => $r->status === 'masuk')
                            ->each(fn (Pengaduan $r) => $r->update([
                                'status'   => 'diproses',
                                'admin_id' => auth()->id(),
                            ]));

                        Notification::make()
                            ->title('Status berhasil diubah menjadi Diproses.')
                            ->success()
                            ->send();
                    }),
            ])
            ->emptyStateHeading('Tidak ada pengaduan')
            ->emptyStateDescription('Semua pengaduan sudah ditangani atau belum ada pengaduan masuk.');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPengaduans::route('/'),
        ];
    }
}