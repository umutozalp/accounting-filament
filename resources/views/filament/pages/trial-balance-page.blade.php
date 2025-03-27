<x-filament-panels::page>
    <x-filament::card>
{{ $this->form }}
<br>
<x-filament::button size="lg" icon="heroicon-m-magnifying-glass" wire:click="search">
    Ara
</x-filament::button>
    </x-filament::card>

{{ $this->table }}

</x-filament-panels::page>

