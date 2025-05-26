<?php

namespace App\Livewire;

use App\Models\Centroid;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Livewire\Component;

class CentroidTable extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public function table(Table $table)
    {
        return $table
            ->query(Centroid::query()->orderBy('iteration', 'asc'))
            ->columns([
                TextColumn::make('iteration')
                    ->label('Iterasi Ke -'),
                TextColumn::make('cluster_number')
                    ->label('Cluster'),
                TextColumn::make('centroid_modal')
                    ->label('Modal')
                    ->money('Rp. '),
                TextColumn::make('centroid_penghasilan')
                    ->label('Penghasilan')
                    ->money('Rp. '),
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
        return view('livewire.centroid-table');
    }
}
