<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OperatorPelangganResource\Pages;
use App\Models\OperatorPelanggan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\CheckboxList;
use App\Models\Pelanggan; 

class OperatorPelangganResource extends Resource
{
    protected static ?string $model = OperatorPelanggan::class;
    protected static ?string $navigationIcon  = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Data Master';
    protected static ?string $navigationLabel = 'Operator Mapping';
    protected static ?string $modelLabel      = 'Operator Mapping';
    protected static ?string $pluralModelLabel = 'Operator Mappings';
    protected static ?int $navigationSort     = 3;

    public static function getEloquentQuery(): Builder
    {
        // Eager load relasi operator dan pelanggan agar performa lebih cepat
        return parent::getEloquentQuery()->with(['operator', 'pelanggan']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Select::make('operator_id')
                            ->label('Operator')
                            ->relationship(
                                name: 'operator', 
                                titleAttribute: 'name',
                                modifyQueryUsing: fn (Builder $query) => $query->where('role', 'operator')
                            )
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\CheckboxList::make('pelanggan_id')
                            ->label('Pilih Pelanggan (Bisa pilih banyak)')
                            ->options(function () {
                                $assignedPelangganIds = OperatorPelanggan::pluck('pelanggan_id')->toArray();

                                return Pelanggan::whereNotIn('id', $assignedPelangganIds)
                                    ->pluck('nama', 'id'); 
                            })
                            ->bulkToggleable()
                            ->columns(1)
                            ->required()
                            ->hidden(fn (string $operation): bool => $operation === 'edit'),

                        Forms\Components\Hidden::make('assigned_at')
                            ->default(now())
                            ->required(), 
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('operator.name')
                    ->label('Nama Operator')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('pelanggan.nama')
                    ->label('Nama Pelanggan')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('pelanggan.nomor_sambungan')
                    ->label('No. Sambungan')
                    ->searchable()
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('assigned_at')
                    ->label('Tgl. Ditugaskan')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->filters([
                // Filter berdasarkan Operator tertentu
                Tables\Filters\SelectFilter::make('operator_id')
                    ->label('Filter Operator')
                    ->relationship('operator', 'name')
                    ->searchable(),
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
            'index'  => Pages\ListOperatorPelanggans::route('/'),
            'create' => Pages\CreateOperatorPelanggan::route('/create'),
            'edit'   => Pages\EditOperatorPelanggan::route('/{record}/edit'),
        ];
    }
}