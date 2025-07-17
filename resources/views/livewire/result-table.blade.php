<div class="flex flex-col gap-4 w-full">
    <div class="w-full">
        <h2 class="text-xl font-bold">Hasil Akhir</h2>
    </div>

    <div class="flex space-x-4 gap-2">
        @foreach (['all', '0', '1', '2'] as $status)
            <x-filament::button wire:click="$set('statusFilter', '{{ $status }}')" :color="$statusFilter === $status ? 'primary' : 'gray'">
                @if ($status === 'all')
                    Semua
                @else
                    Klaster {{ $status }}
                @endif
            </x-filament::button>
        @endforeach
    </div>

    <div class="w-full">
        {{ $this->table }}
    </div>
</div>
