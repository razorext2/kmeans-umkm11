<?php

namespace App\Livewire;

use App\Models\Result;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;

class ResultTable extends Component implements HasTable, HasForms
{
    use InteractsWithForms;
    use InteractsWithTable;

    public function table(Table $table)
    {
        return $table
            ->query(Result::query()->orderBy('final_cluster', 'asc'))
            ->columns([
                TextColumn::make('umkm.nama_umkm')
                    ->label('Nama UMKM'),
                TextColumn::make('umkm.jenis_umkm')
                    ->label('Jenis UMKM'),
                TextColumn::make('umkm.nama_pemilik')
                    ->label('Nama Pemilik'),
                TextColumn::make('final_cluster')
                    ->label('Cluster'),
            ])
            ->filters([
                //
            ])
            ->actions([
                //
            ])
            ->bulkActions([
                //
            ]);
    }

    public function render()
    {
        return view('livewire.result-table');
    }
}
