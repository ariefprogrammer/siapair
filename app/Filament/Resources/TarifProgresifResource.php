<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TarifProgresifResource\Pages;
use App\Models\TarifProgresif;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TarifProgresifResource extends Resource
{
    protected static ?string $model = TarifProgresif::class;
    protected static ?string $navigationIcon  = 'heroicon-o-currency-dollar';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $navigationLabel = 'Tarif Progresif';
    protected static ?string $modelLabel      = 'Tarif';
    protected static ?int $navigationSort     = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->columns(2)->schema([
                Forms\Components\TextInput::make('tier')
                    ->label('Tier')
                    ->integer()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->minValue(1),

                Forms\Components\Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),

                Forms\Components\TextInput::make('batas_bawah')
                    ->label('Batas Bawah (m³)')
                    ->numeric()
                    ->required()
                    ->minValue(0)
                    ->suffix('m³')
                    ->hint('Inklusif'),

                Forms\Components\TextInput::make('batas_atas')
                    ->label('Batas Atas (m³)')
                    ->numeric()
                    ->nullable()
                    ->minValue(0)
                    ->suffix('m³')
                    ->hint('Kosongkan jika tier terakhir (unlimited)'),

                Forms\Components\TextInput::make('harga_per_m3')
                    ->label('Harga per m³')
                    ->numeric()
                    ->required()
                    ->minValue(0)
                    ->prefix('Rp')
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('keterangan')
                    ->label('Keterangan')
                    ->maxLength(100)
                    ->placeholder('Contoh: Pemakaian dasar (0–10 m³)')
                    ->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tier')
                    ->label('Tier')
                    ->sortable()
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('batas_bawah')
                    ->label('Batas Bawah')
                    ->formatStateUsing(fn ($state) => number_format($state, 0) . ' m³'),

                Tables\Columns\TextColumn::make('batas_atas')
                    ->label('Batas Atas')
                    ->formatStateUsing(fn ($state) => $state ? number_format($state, 0) . ' m³' : '∞'),

                Tables\Columns\TextColumn::make('harga_per_m3')
                    ->label('Harga / m³')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.')),

                Tables\Columns\TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->wrap(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->defaultSort('tier')
            ->reorderable('tier')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListTarifProgresifs::route('/'),
            'create' => Pages\CreateTarifProgresif::route('/create'),
            'edit'   => Pages\EditTarifProgresif::route('/{record}/edit'),
        ];
    }
}