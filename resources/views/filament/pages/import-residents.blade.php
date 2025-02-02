<x-filament::page>
       <x-filament::form wire:submit.prevent="import">
           {{ $this->form }}
           <x-filament::button type="submit">Импортировать</x-filament::button>
       </x-filament::form>
   </x-filament::page>
