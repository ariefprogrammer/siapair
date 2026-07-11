<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PeriodePencatatanResource\Pages;
use App\Models\PeriodePencatatan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PeriodePencatatanResource extends Resource
{
    protected static ?string $model = PeriodePencatatan::class;
    protected static ?string $navigationIcon  = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $navigationLabel = 'Periode Pencatatan';
    protected static ?string $modelLabel      = 'Periode';
    protected static ?int $navigationSort     = 1;

    public static function form(Form $form): Form
    {
        $bulan = [
            1 => 'Januari', 2 => 'Februari',  3 => 'Maret',
            4 => 'April',   5 => 'Mei',        6 => 'Juni',
            7 => 'Juli',    8 => 'Agustus',    9 => 'September',
            10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        return $form->schema([
            Forms\Components\Section::make()->columns(2)->schema([
                Forms\Components\Select::make('bulan')
                    ->label('Bulan')
                    ->options($bulan)
                    ->required()
                    ->native(false),

                Forms\Components\TextInput::make('tahun')
                    ->label('Tahun')
                    ->integer()
                    ->required()
                    ->minValue(2020)
                    ->maxValue(2099)
                    ->default(now()->year),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('label')
                    ->label('Periode')
                    ->getStateUsing(fn (PeriodePencatatan $record) => $record->labelBulan())
                    ->sortable(['tahun', 'bulan'])
                    ->weight('bold'),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => 'buka',
                        'gray'    => 'tutup',
                    ])
                    ->formatStateUsing(fn ($state) => ucfirst($state)),

                Tables\Columns\TextColumn::make('dibuka.name')
                    ->label('Dibuka Oleh'),

                Tables\Columns\TextColumn::make('dibuka_at')
                    ->label('Dibuka')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('ditutup.name')
                    ->label('Ditutup Oleh')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('ditutup_at')
                    ->label('Ditutup')
                    ->dateTime('d M Y, H:i')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('catatanMeter_count')
                    ->label('Catatan Masuk')
                    ->counts('catatanMeter')
                    ->badge()
                    ->color('info'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'buka'  => 'Buka',
                        'tutup' => 'Tutup',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('tutup')
                    ->label('Tutup Periode')
                    ->icon('heroicon-o-lock-closed')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Tutup Periode Pencatatan?')
                    ->modalDescription('Operator tidak akan bisa menginput catatan meter setelah periode ditutup.')
                    ->visible(fn (PeriodePencatatan $record) => $record->isBuka())
                    ->action(function (PeriodePencatatan $record) {
                        $record->update([
                            'status'       => 'tutup',
                            'ditutup_oleh' => auth()->id(),
                            'ditutup_at'   => now(),
                        ]);

                        Notification::make()
                            ->title('Periode ' . $record->labelBulan() . ' berhasil ditutup.')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('buka')
                    ->label('Buka Kembali')
                    ->icon('heroicon-o-lock-open')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Buka Periode Ini?')
                    ->modalDescription('Periode lain yang sedang aktif (jika ada) akan otomatis ditutup, karena hanya boleh ada satu periode aktif.')
                    ->visible(fn (PeriodePencatatan $record) => ! $record->isBuka())
                    ->action(function (PeriodePencatatan $record) {
                        \Illuminate\Support\Facades\DB::transaction(function () use ($record) {
                            PeriodePencatatan::where('status', 'buka')
                                ->where('id', '!=', $record->id)
                                ->update([
                                    'status'       => 'tutup',
                                    'ditutup_oleh' => auth()->id(),
                                    'ditutup_at'   => now(),
                                ]);

                            $record->update([
                                'status'       => 'buka',
                                'ditutup_oleh' => null,
                                'ditutup_at'   => null,
                            ]);
                        });

                        Notification::make()
                            ->title('Periode ' . $record->labelBulan() . ' dibuka kembali.')
                            ->body('Periode aktif lainnya otomatis ditutup.')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\EditAction::make()
                    ->visible(fn (PeriodePencatatan $record) => $record->isBuka()),
            ])
            ->defaultSort('tahun', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPeriodePencatatans::route('/'),
            'create' => Pages\CreatePeriodePencatatan::route('/create'),
        ];
    }
}