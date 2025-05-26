<?php

namespace App\Livewire;

use App\Models\Iteration;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Livewire\Component;

class IterationTable extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public function table(Table $table)
    {
        return $table
            ->query(Iteration::query()->orderBy('iteration', 'asc'))
            ->columns([
                TextColumn::make('iteration')
                    ->label('Iterasi ke -'),
                TextColumn::make('umkm.nama_umkm')
                    ->label('Nama UMKM'),
                TextColumn::make('umkm.jenis_umkm')
                    ->label('Jenis UMKM'),
                TextColumn::make('umkm.nama_pemilik')
                    ->label('Nama Pemilik'),
                TextColumn::make('distances')
                    ->label('Jarak'),
                TextColumn::make('assigned_cluster')
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
        return view('livewire.iteration-table');
    }
}
