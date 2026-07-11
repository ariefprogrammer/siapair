<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ConfigGeneralResource\Pages;
use App\Models\ConfigGeneral;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ConfigGeneralResource extends Resource
{
    protected static ?string $model = ConfigGeneral::class;
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $navigationLabel = 'Setting Umum';
    protected static ?string $modelLabel = 'Setting Umum';
    protected static ?string $pluralModelLabel = 'Setting Umum';
    protected static ?int $navigationSort     = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('app_name')
                            ->label('Nama Aplikasi')
                            ->placeholder('SIAP AIR')
                            ->maxLength(255)
                            ->required(),

                        Forms\Components\FileUpload::make('app_logo')
                            ->label('Logo Aplikasi')
                            ->image()
                            ->directory('app-logo')
                            ->disk('public')
                            ->imagePreviewHeight('120')
                            ->nullable(),
                    ])
                    ->columns(2),

                Forms\Components\Card::make()
                    ->schema([
                        // Input Biaya Admin (Format Rupiah)
                        Forms\Components\TextInput::make('admin_fee')
                            ->label('Biaya Admin')
                            ->numeric()
                            ->prefix('Rp')
                            ->placeholder('0')
                            ->required(),

                        // Input Biaya Beban (Format Rupiah)
                        Forms\Components\TextInput::make('biaya_beban')
                            ->label('Biaya Beban')
                            ->numeric()
                            ->prefix('Rp')
                            ->placeholder('0')
                            ->required(),

                        // Input PPN (Format Persentase)
                        Forms\Components\TextInput::make('ppn')
                            ->label('PPN (%)')
                            ->numeric()
                            ->suffix('%')
                            ->placeholder('0')
                            ->minValue(0)
                            ->maxValue(100)
                            ->required(),
                    ])
                    ->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('app_logo')
                    ->label('Logo')
                    ->disk('public')
                    ->circular(),

                Tables\Columns\TextColumn::make('app_name')
                    ->label('Nama Aplikasi')
                    ->searchable(),

                Tables\Columns\TextColumn::make('admin_fee')
                    ->label('Biaya Admin')
                    ->money('IDR', locale: 'id') // Otomatis format ke Rp xxx.xxx
                    ->sortable(),

                Tables\Columns\TextColumn::make('biaya_beban')
                    ->label('Biaya Beban')
                    ->money('IDR', locale: 'id')
                    ->sortable(),

                Tables\Columns\TextColumn::make('ppn')
                    ->label('PPN')
                    ->suffix('%')
                    ->sortable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Terakhir Diperbarui')
                    ->dateTime('d M Y, H:i'),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListConfigGenerals::route('/'),
            'create' => Pages\CreateConfigGeneral::route('/create'),
            'edit' => Pages\EditConfigGeneral::route('/{record}/edit'),
        ];
    }
}