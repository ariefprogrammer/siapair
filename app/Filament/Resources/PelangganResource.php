<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PelangganResource\Pages;
use App\Filament\Resources\PelangganResource\RelationManagers;
use App\Models\Pelanggan;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use App\Filament\Imports\PelangganImporter;
use Filament\Tables\Actions\ImportAction;

class PelangganResource extends Resource
{
    protected static ?string $model = Pelanggan::class;
    protected static ?string $navigationIcon  = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Data Master';
    protected static ?string $navigationLabel = 'Pelanggan';
    protected static ?string $modelLabel      = 'Pelanggan';
    protected static ?int $navigationSort     = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Data Sambungan')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('nomor_sambungan')
                        ->label('Nomor Sambungan')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(20)
                        ->placeholder('SA-0001'),

                    Forms\Components\DatePicker::make('tanggal_daftar')
                        ->label('Tanggal Daftar')
                        ->required()
                        ->default(now()),

                    Forms\Components\TextInput::make('nama')
                        ->label('Nama Pelanggan')
                        ->required()
                        ->maxLength(100)
                        ->columnSpanFull(),

                    Forms\Components\Textarea::make('alamat')
                        ->label('Alamat')
                        ->required()
                        ->rows(3)
                        ->columnSpanFull(),

                    Forms\Components\TextInput::make('rt')
                        ->label('RT')
                        ->maxLength(5),

                    Forms\Components\TextInput::make('rw')
                        ->label('RW')
                        ->maxLength(5),

                    Forms\Components\TextInput::make('wilayah')
                        ->label('Wilayah / Blok')
                        ->maxLength(100),

                    Forms\Components\Select::make('status')
                        ->label('Status')
                        ->options([
                            'aktif'    => 'Aktif',
                            'nonaktif' => 'Nonaktif',
                        ])
                        ->default('aktif')
                        ->required(),
                ]),

            Forms\Components\Section::make('Koordinat (Opsional)')
                ->collapsed()
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('latitude')
                        ->label('Latitude')
                        ->numeric()
                        ->step(0.00000001),

                    Forms\Components\TextInput::make('longitude')
                        ->label('Longitude')
                        ->numeric()
                        ->step(0.00000001),
                ]),

            Forms\Components\Section::make('Akun Login PWA')
                ->description('Opsional — isi jika pelanggan akan login mandiri via aplikasi.')
                ->collapsed()
                ->schema([
                    Forms\Components\Select::make('user_id')
                        ->label('Akun User')
                        ->relationship(
                            name: 'user',
                            titleAttribute: 'email',
                            modifyQueryUsing: fn (Builder $query) => $query->where('role', 'pelanggan')
                        )
                        ->searchable()
                        ->preload()
                        ->nullable()
                        ->createOptionForm([
                            Forms\Components\TextInput::make('name')
                                ->label('Nama')
                                ->required(),
                            Forms\Components\TextInput::make('email')
                                ->label('Email')
                                ->email()
                                ->required()
                                ->unique('users', 'email'),
                            Forms\Components\TextInput::make('password')
                                ->label('Password')
                                ->password()
                                ->required()
                                ->minLength(8)
                                ->dehydrateStateUsing(fn ($state) => Hash::make($state)),
                        ])
                        ->createOptionUsing(function (array $data) {
                            return User::create([
                                ...$data,
                                'role'      => 'pelanggan',
                                'is_active' => true,
                            ])->id;
                        }),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nomor_sambungan')
                    ->label('No. Sambungan')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('wilayah')
                    ->label('Wilayah')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('rt')
                    ->label('RT/RW')
                    ->formatStateUsing(fn ($record) => "RT {$record->rt} / RW {$record->rw}")
                    ->sortable(),

                Tables\Columns\IconColumn::make('user_id')
                    ->label('Akun PWA')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray'),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => 'aktif',
                        'danger'  => 'nonaktif',
                    ]),

                Tables\Columns\TextColumn::make('tanggal_daftar')
                    ->label('Tgl. Daftar')
                    ->date('d M Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'aktif'    => 'Aktif',
                        'nonaktif' => 'Nonaktif',
                    ]),

                Tables\Filters\SelectFilter::make('wilayah')
                    ->label('Wilayah')
                    ->options(
                        fn () => Pelanggan::distinct()
                            ->pluck('wilayah', 'wilayah')
                            ->filter()
                            ->toArray()
                    ),

                Tables\Filters\Filter::make('belum_punya_akun')
                    ->label('Belum Punya Akun PWA')
                    ->query(fn (Builder $query) => $query->whereNull('user_id')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                ImportAction::make()
                    ->importer(PelangganImporter::class)
                    ->label('Import Excel'),
            ])
            ->defaultSort('nomor_sambungan');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\TagihanRelationManager::class,
            RelationManagers\PengaduanRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPelanggans::route('/'),
            'create' => Pages\CreatePelanggan::route('/create'),
            'view'   => Pages\ViewPelanggan::route('/{record}'),
            'edit'   => Pages\EditPelanggan::route('/{record}/edit'),
        ];
    }

    public static function getGlobalSearchResultDetails(\Illuminate\Database\Eloquent\Model $record): array
    {
        return [
            'No. Sambungan' => $record->nomor_sambungan,
            'Wilayah'       => $record->wilayah,
        ];
    }
}