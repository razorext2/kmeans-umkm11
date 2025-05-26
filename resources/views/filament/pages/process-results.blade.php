<x-filament-panels::page>
    <form wire:submit.prevent="process" class="grid gap-y-6">
        {{ $this->form }}

        <x-filament::button type="submit" class="flex flex-col items-center">
            <span wire:loading.remove> Proses K-Means </span>
            <span wire:loading> Memproses... </span>
        </x-filament::button>
    </form>

    {{-- @if ($showIterations) --}}
    <div class="flex flex-col gap-4">
        <livewire:iteration-table />
        <livewire:centroid-table />
        <livewire:result-table />
    </div>
    {{-- @endif --}}
</x-filament-panels::page>
