<?php

namespace App\Filament\Pages;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use App\Models\Umkm;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;

class ProcessResults extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static string $view = 'filament.pages.process-results';

    protected static ?string $navigationLabel = 'Proses KMeans';

    protected static ?string $title = 'Proses K-Means';

    protected static ?string $slug = 'process-results';

    protected static ?int $navigationSort = 2;

    public ?int $clusters = 3;

    public ?int $iterations = 100;

    public ?bool $showIterations = false;

    public ?bool $showRefreshButton = false;

    public array $centroidLogs = [];

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

    private function runKmeansPlusPlus(array $data, int $clusters, int $iterations): array
    {
        $points = array_map(function ($item) {
            return [$item['modal'], $item['penghasilan']];
        }, $data);

        $ids = array_column($data, 'id');
        $n = count($points);
        $centroids = [];
        $this->centroidLogs = [];

        // Pilih centroid pertama secara manual dari data index ke-3
        if (!isset($points[2])) {
            throw new \Exception("Data index ke-3 tidak tersedia, data kurang dari 4 elemen.");
        }

        $centroids[] = $points[2];
        $this->centroidLogs[] = [
            'step' => 1,
            'chosen_index' => 2,
            'centroid' => $points[2],
            'note' => 'Centroid pertama ditentukan manual'
        ];

        // Pilih centroid berikutnya berdasarkan probabilitas
        while (count($centroids) < $clusters) {
            $distances = [];

            foreach ($points as $point) {
                $minDist = INF;
                foreach ($centroids as $c) {
                    $d = pow($point[0] - $c[0], 2) + pow($point[1] - $c[1], 2);
                    $minDist = min($minDist, $d);
                }
                $distances[] = $minDist;
            }

            mt_srand(41);
            $total = array_sum($distances);
            $probabilities = array_map(fn($d) => $d / $total, $distances);
            $r = mt_rand() / mt_getrandmax();
            $acc = 0;

            foreach ($probabilities as $i => $p) {
                $acc += $p;
                if ($r <= $acc) {
                    $centroids[] = $points[$i];
                    $this->centroidLogs[] = [
                        'step' => count($centroids),
                        'chosen_index' => $i,
                        'centroid' => $points[$i],
                        'probability' => round($p, 4),
                        'note' => 'Dipilih berdasarkan probabilitas akumulatif'
                    ];
                    break;
                }
            }
        }

        $history = [];
        $labels = array_fill(0, $n, -1);

        for ($iter = 1; $iter <= $iterations; $iter++) {
            $iterationData = ['iteration' => $iter, 'centroids' => $centroids, 'points' => []];

            // Hitung jarak & assign cluster
            foreach ($points as $i => $point) {
                $distances = array_map(fn($c) => sqrt(pow($point[0] - $c[0], 2) + pow($point[1] - $c[1], 2)), $centroids);
                $minIndex = array_keys($distances, min($distances))[0];

                $labels[$i] = $minIndex;
                $iterationData['points'][] = [
                    'umkm_id' => $ids[$i],
                    'distances' => $distances,
                    'assigned_cluster' => $minIndex
                ];
            }

            // Update centroid
            $newCentroids = [];
            for ($k = 0; $k < $clusters; $k++) {
                $clusterPoints = array_values(array_filter($points, fn($_, $i) => $labels[$i] == $k, ARRAY_FILTER_USE_BOTH));
                if (count($clusterPoints) > 0) {
                    $x = array_sum(array_column($clusterPoints, 0)) / count($clusterPoints);
                    $y = array_sum(array_column($clusterPoints, 1)) / count($clusterPoints);
                    $newCentroids[] = [$x, $y];
                } else {
                    $newCentroids[] = $centroids[$k]; // tetap
                }
            }

            $history[] = $iterationData;

            if ($newCentroids === $centroids) {
                break;
            }

            $centroids = $newCentroids;
        }

        $finalLabels = [];
        foreach ($labels as $i => $cluster) {
            $finalLabels[] = [
                'id' => $ids[$i],
                'cluster' => $cluster
            ];
        }

        return [
            'history' => $history,
            'final_labels' => $finalLabels,
            'final_centroids' => $centroids
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

            $results = $this->runKmeansPlusPlus($umkm->toArray(), $data['clusters'], $data['iterations']);

            \App\Models\Centroid::truncate();
            \App\Models\Iteration::truncate();
            \App\Models\Result::truncate();

            DB::beginTransaction();

            foreach ($results['history'] as $iterationData) {
                $iter = $iterationData['iteration'];

                foreach ($iterationData['centroids'] as $i => $centroid) {
                    \App\Models\Centroid::create([
                        'iteration' => $iter,
                        'cluster_number' => $i,
                        'centroid_modal' => $centroid[0],
                        'centroid_penghasilan' => $centroid[1],
                    ]);
                }

                foreach ($iterationData['points'] as $point) {
                    \App\Models\Iteration::create([
                        'iteration' => $iter,
                        'umkm_id' => $point['umkm_id'],
                        'distances' => json_encode($point['distances']),
                        'assigned_cluster' => $point['assigned_cluster'],
                    ]);
                }
            }

            foreach ($results['final_labels'] as $result) {
                \App\Models\Result::create([
                    'umkm_id' => $result['id'],
                    'final_cluster' => $result['cluster'],
                ]);
            }

            DB::commit();
            $this->showIterations = true;
            $this->getSuccessMessage('Berhasil', 'Data berhasil diproses.');
            $this->showRefreshButton = true;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->getErrorMessage('Terjadi kesalahan', 'Terjadi kesalahan: ' . $e->getMessage());
            Log::error('KMeans processing error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    public function getSuccessMessage($title, $text)
    {
        Notification::make()
            ->icon('heroicon-o-check-circle')
            ->iconColor('success')
            ->title($title)
            ->body($text)
            ->send();
    }

    public function getErrorMessage($title, $text)
    {
        Notification::make()
            ->icon('heroicon-o-x-circle')
            ->iconColor('danger')
            ->title($title)
            ->body($text)
            ->send();
    }
}
