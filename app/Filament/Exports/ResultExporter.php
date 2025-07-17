<?php

namespace App\Filament\Exports;

use App\Models\Result;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ResultExporter extends Exporter
{
    protected static ?string $model = Result::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('umkm.nama_umkm')
                ->label('Nama UMKM'),
            ExportColumn::make('final_cluster')
                ->label('Cluster')
                ->formatStateUsing(function ($state) {
                    switch ($state) {
                        case 0:
                            return "Rendah";
                        case 1:
                            return "Sedang";
                        case 2:
                            return "Tinggi";
                        default:
                            return "Klaster tidak ditentukan";
                    }
                }),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your result export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
