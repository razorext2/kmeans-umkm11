<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UmkmResource\Pages;
use App\Filament\Resources\UmkmResource\RelationManagers;
use App\Models\Umkm;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TextArea;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

class UmkmResource extends Resource
{
    protected static ?string $model = Umkm::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    protected static ?string $navigationLabel = 'Data UMKM';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nama_umkm')
                    ->label('Nama UMKM')
                    ->placeholder('Cth: Toko Sejahtera')
                    ->required()
                    ->maxLength(255),
                TextInput::make('nama_pemilik')
                    ->label('Nama Pemilik')
                    ->placeholder('Cth: Budi')
                    ->required()
                    ->maxLength(255),
                TextArea::make('alamat')
                    ->label('Alamat')
                    ->placeholder('Cth: Jl. Raya No. 123')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull()
                    ->rows(5),
                TextInput::make('jenis_umkm')
                    ->label('Jenis UMKM')
                    ->placeholder('Cth: Toko Baju')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('modal')
                    ->label('Modal')
                    ->placeholder('Cth: 1000000')
                    ->required()
                    ->numeric()
                    ->prefix('Rp. '),
                TextInput::make('penghasilan')
                    ->label('Penghasilan')
                    ->placeholder('Cth: 1000000')
                    ->required()
                    ->numeric()
                    ->prefix('Rp. '),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_umkm')
                    ->label('Nama UMKM')
                    ->searchable(),
                TextColumn::make('nama_pemilik')
                    ->label('Nama Pemilik')
                    ->searchable(),
                TextColumn::make('alamat')
                    ->label('Alamat')
                    ->searchable(),
                TextColumn::make('jenis_umkm')
                    ->label('Jenis UMKM')
                    ->searchable(),
                TextColumn::make('modal')
                    ->label('Modal')
                    ->money('Rp. '),
                TextColumn::make('penghasilan')
                    ->label('Penghasilan')
                    ->money('Rp. '),
                TextColumn::make('cluster')
                    ->label('Cluster')
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('cluster')
                    ->label('Cluster')
                    ->options([
                        '0' => 'Cluster 0',
                        '1' => 'Cluster 1',
                        '2' => 'Cluster 2',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListUmkms::route('/'),
            'create' => Pages\CreateUmkm::route('/create'),
            'edit' => Pages\EditUmkm::route('/{record}/edit'),
        ];
    }
}
