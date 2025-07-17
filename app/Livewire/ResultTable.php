<?php

namespace App\Livewire;

use App\Filament\Exports\ResultExporter;
use App\Models\Result;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Resources\Components\Tab;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;

class ResultTable extends Component implements HasTable, HasForms
{
    use InteractsWithForms;
    use InteractsWithTable;

    public string $statusFilter = 'all';

    public function mount($statusFilter = 'all')
    {
        $this->statusFilter = $statusFilter;
    }

    public function table(Table $table)
    {
        return $table
            ->headerActions([
                ExportAction::make()
                    ->exporter(ResultExporter::class)
            ])
            ->query(function () {
                return Result::query()
                    ->when($this->statusFilter !== 'all', function ($query) {
                        $query->where('final_cluster', $this->statusFilter);
                    })
                    ->orderBy('final_cluster', 'asc');
            })
            ->columns([
                TextColumn::make('umkm.nama_umkm')
                    ->label('Nama UMKM'),
                TextColumn::make('umkm.jenis_umkm')
                    ->label('Jenis UMKM'),
                TextColumn::make('umkm.nama_pemilik')
                    ->label('Nama Pemilik'),
                TextColumn::make('final_cluster')
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
