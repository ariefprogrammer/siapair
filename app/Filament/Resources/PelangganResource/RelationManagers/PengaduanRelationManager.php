<?php

namespace App\Filament\Resources\PelangganResource\RelationManagers;

use App\Models\Pengaduan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PengaduanRelationManager extends RelationManager
{
    protected static string $relationship = 'pengaduan';
    protected static ?string $title       = 'Riwayat Pengaduan';
    protected static ?string $icon        = 'heroicon-o-chat-bubble-left-right';

    public function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('deskripsi')
            ->columns([
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

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'info'    => 'masuk',
                        'warning' => 'diproses',
                        'success' => 'selesai',
                    ])
                    ->formatStateUsing(fn (Pengaduan $record) => $record->labelStatus()),

                Tables\Columns\IconColumn::make('has_lampiran')
                    ->label('Lampiran')
                    ->getStateUsing(fn (Pengaduan $record) => (bool) $record->lampiran_path)
                    ->boolean()
                    ->trueIcon('heroicon-o-paper-clip')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('info')
                    ->falseColor('gray'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dikirim')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('tanggal_respons')
                    ->label('Direspons')
                    ->dateTime('d M Y, H:i')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'masuk'    => 'Masuk',
                        'diproses' => 'Diproses',
                        'selesai'  => 'Selesai',
                    ]),

                Tables\Filters\SelectFilter::make('kategori')
                    ->options([
                        'teknis'       => 'Teknis',
                        'administrasi' => 'Administrasi',
                        'lainnya'      => 'Lainnya',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('thread')
                    ->label('Buka Thread')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('info')
                    ->modalHeading(fn (Pengaduan $record) =>
                        'Thread — ' . $record->labelKategori() . ' · ' . $record->labelStatus()
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
                            ->default($record->status) // ← fix: langsung pakai $record dari outer scope
                            ->native(false)
                            ->required(),
                    ])
                    ->modalSubmitActionLabel('Kirim Balasan')
                    ->modalSubmitAction(function (\Filament\Actions\StaticAction $action, Pengaduan $record) {
                        // ← fix: closure menerima StaticAction, return action atau hidden
                        return $record->isSelesai() ? $action->hidden() : $action;
                    })
                    ->modalCancelActionLabel('Tutup')
                    ->action(function (Pengaduan $record, array $data) {
                        if (empty($data['pesan'])) return;

                        \App\Models\PengaduanPesan::create([
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
            ])
            ->emptyStateHeading('Belum ada pengaduan')
            ->emptyStateDescription('Pelanggan ini belum pernah mengajukan pengaduan.');
    }
}