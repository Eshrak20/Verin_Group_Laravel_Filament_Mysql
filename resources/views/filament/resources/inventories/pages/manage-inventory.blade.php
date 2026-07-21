<x-filament-panels::page>

    <div class="grid grid-cols-4 gap-4">

        <x-filament::section>
            <h2 class="text-lg font-bold">
                Current Stock
            </h2>

            <p class="text-3xl">
                {{ $this->record->stock }}
            </p>
        </x-filament::section>

        <x-filament::section>
            <h2 class="text-lg font-bold">
                Reserved
            </h2>

            <p class="text-3xl">
                {{ $this->record->reserved_stock }}
            </p>
        </x-filament::section>

        <x-filament::section>
            <h2 class="text-lg font-bold">
                Available
            </h2>

            <p class="text-3xl">
                {{ $this->record->available_stock }}
            </p>
        </x-filament::section>

        <x-filament::section>
            <h2 class="text-lg font-bold">
                Branch
            </h2>

            <p>
                {{ $this->record->branch->name }}
            </p>
        </x-filament::section>

    </div>

</x-filament-panels::page>