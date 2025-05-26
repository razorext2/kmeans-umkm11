<?php

namespace App\Filament\Pages;

use App\Models\Umkm;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;

class ProcessResults extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static string $view = 'filament.pages.process-results';

    protected static ?string $navigationLabel = 'Proses KMeans';

    protected static ?string $title = 'Proses K-Means';

    protected static ?string $slug = 'process-results';

    protected static ?int $navigationSort = 2;

    public ?int $clusters = 3;

    public ?int $iterations = 100;

    public function mount(): void
    {
        $this->form->fill([
            'iterations' => $this->iterations,
            'clusters' => $this->clusters,
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('clusters')
                ->label('Jumlah Cluster')
                ->required()
                ->numeric(),
            TextInput::make('iterations')
                ->label('Jumlah Iterasi')
                ->required()
                ->numeric(),
        ];
    }

    public function process()
    {
        try {
            $data = $this->validate([
                'iterations' => ['required', 'integer', 'min:1', 'max:1000'],
                'clusters' => ['required', 'integer', 'min:2', 'max:10']
            ]);

            $umkm = Umkm::all(['id', 'modal', 'penghasilan']);

            if ($umkm->count() < $data['clusters']) {
                $this->getErrorMessage('Jumlah data UMKM harus lebih dari atau sama dengan jumlah cluster', 'Jumlah data UMKM harus lebih dari atau sama dengan jumlah cluster.');
                return;
            }

            if ($umkm->isEmpty()) {
                $this->getErrorMessage('Tidak ada data UMKM yang ditemukan', 'Tidak ada data UMKM yang ditemukan.');
                return;
            }

            $inputData = [
                'iterations' => (int)$data['iterations'],
                'clusters' => (int)$data['clusters'],
                'data' => $umkm->toArray()
            ];

            $inputPath = storage_path('app/kmeans_input.json');
            $jsonData = json_encode($inputData, JSON_PRETTY_PRINT);

            if ($jsonData === false) {
                $this->getErrorMessage('Gagal mengencode data UMKM', 'Gagal mengencode data UMKM.');
                return;
            }

            if (file_put_contents($inputPath, $jsonData) === false) {
                $this->getErrorMessage('Gagal menyimpan file input untuk pemrosesan', 'Gagal menyimpan file input untuk pemrosesan.');
                return;
            }

            $pythonScript = base_path('python/kmeans.py');
            if (!file_exists($pythonScript)) {
                $this->getErrorMessage('File script Python tidak ditemukan', 'File script Python tidak ditemukan.');
                return;
            }

            $command = sprintf('python %s %s', escapeshellarg($pythonScript), escapeshellarg($inputPath));
            $output = Process::run($command);

            if ($output === null) {
                $this->getErrorMessage('Gagal menjalankan script Python', 'Gagal menjalankan script Python.');
                return;
            }

            $results = json_decode($output->output(), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->getErrorMessage('Format output tidak valid', 'Format output tidak valid: ' . json_last_error_msg());
                return;
            }

            if (!isset($results['labels']) || !isset($results['centroids'])) {
                $this->getErrorMessage('Format output tidak sesuai ekspektasi', 'Format output tidak sesuai ekspektasi.');
                return;
            }

            dd($results);
        } catch (\Exception $e) {
            $this->getErrorMessage('Terjadi kesalahan', 'Terjadi kesalahan: ' . $e->getMessage());
            Log::error('KMeans processing error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    public function getSuccessMessage($title, $text)
    {
        Notification::make()
            ->success()
            ->title($title)
            ->body($text)
            ->send();
    }

    public function getErrorMessage($title, $text)
    {
        Notification::make()
            ->error()
            ->title($title)
            ->body($text)
            ->send();
    }
}
