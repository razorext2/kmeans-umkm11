<?php

namespace App\Filament\Resources\UmkmResource\Pages;

use App\Filament\Resources\UmkmResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUmkms extends ListRecords
{
    protected static string $resource = UmkmResource::class;

    protected static ?string $title = 'Data UMKM';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah UMKM'),
            \EightyNine\ExcelImport\ExcelImportAction::make()
                ->color('primary')
                ->label('Import Data UMKM')
                ->validateUsing([
                    'nama_umkm' => 'required|string',
                    'nama_pemilik' => 'required|string',
                    'alamat' => 'required|string',
                    'jenis_umkm' => 'required|string',
                    'modal' => 'required|numeric',
                    'penghasilan' => 'required|numeric',
                ]),
        ];
    }
}
