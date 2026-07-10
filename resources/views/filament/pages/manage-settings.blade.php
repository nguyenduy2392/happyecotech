<x-filament-panels::page>
    <form wire:submit="save" class="grid gap-y-6">
        {{ $this->form }}

        <div class="flex justify-end">
            <x-filament::button type="submit" icon="heroicon-o-check">
                Lưu cài đặt
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
