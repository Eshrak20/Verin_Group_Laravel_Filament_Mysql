<?php

namespace App\Filament\Resources\Attributes\Pages;

use App\Filament\Resources\Attributes\AttributeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAttribute extends EditRecord
{
    protected static string $resource = AttributeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
    protected function afterSave(): void
    {
        $values = collect($this->data['values'] ?? [])
            ->map(fn($value) => trim($value))
            ->filter()
            ->unique()
            ->values();

        // Delete removed values
        $this->record->values()
            ->whereNotIn('value', $values)
            ->delete();

        // Create missing values
        foreach ($values as $value) {
            $this->record->values()->firstOrCreate([
                'value' => $value,
            ]);
        }
    }
}
