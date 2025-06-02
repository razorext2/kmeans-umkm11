<x-filament-panels::page>
    <div class="grid gap-y-4">
        <form wire:submit.prevent="process" class="grid gap-y-4">
            {{ $this->form }}

            <x-filament::button type="submit" class="flex flex-col items-center">
                <span wire:loading.remove wire:target="process"> Proses K-Means </span>
                <span wire:loading wire:target="process"> Memproses... </span>
            </x-filament::button>
        </form>

        @if ($showRefreshButton)
            <x-filament::button wire:click="$refresh()">
                <span wire:loading.remove wire:target="$refresh"> Refresh Tabel </span>
                <span wire:loading wire:target="$refresh"> Refreshing... </span>
            </x-filament::button>
        @endif
    </div>

    {{-- @if ($showIterations) --}}
    <div class="flex flex-col gap-4">

        @if ($centroidLogs && count($centroidLogs) > 0)
            @php $log = $centroidLogs[0]; @endphp
            <div class="p-3 bg-gray-100 dark:bg-gray-800 rounded-md">
                <p>{{ $log['note'] }}</p>
                <p>Index UMKM: {{ $log['chosen_index'] }}</p>
                <p>Centroid: Modal = {{ $log['centroid'][0] }}, Penghasilan = {{ $log['centroid'][1] }}</p>
                @if (isset($log['probability']))
                    <p>Probabilitas: {{ $log['probability'] }}</p>
                @endif
            </div>
        @endif

        <livewire:centroid-table />
        <livewire:iteration-table />
        <livewire:result-table />
    </div>
    {{-- @endif --}}
</x-filament-panels::page>
