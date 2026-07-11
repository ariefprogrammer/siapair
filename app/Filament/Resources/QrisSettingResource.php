<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QrisSettingResource\Pages;
use App\Models\QrisSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class QrisSettingResource extends Resource
{
    protected static ?string $model = QrisSetting::class;
    protected static ?string $navigationIcon = 'heroicon-o-qr-code';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $navigationLabel = 'Pengaturan QRIS';
    protected static ?string $modelLabel = 'Pengaturan QRIS';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('nama_pemilik')
                            ->label('Nama Pemilik Akun / Merchant')
                            ->placeholder('Contoh: SIAP AIR MANAGEMENT')
                            ->maxLength(255),

                        Forms\Components\FileUpload::make('image_path')
                            ->label('Gambar Barcode QRIS')
                            ->image()
                            ->directory('qris-settings') // Akan disimpan di storage/app/public/qris-settings
                            ->imageEditor()
                            ->required(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_path')
                    ->label('Barcode QRIS'),
                    
                Tables\Columns\TextColumn::make('nama_pemilik')
                    ->label('Nama Pemilik / Merchant')
                    ->searchable(),
                    
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
            'index' => Pages\ListQrisSettings::route('/'),
            'create' => Pages\CreateQrisSetting::route('/create'),
            'edit' => Pages\EditQrisSetting::route('/{record}/edit'),
        ];
    }
}