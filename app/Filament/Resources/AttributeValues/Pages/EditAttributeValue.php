<?php

namespace App\Filament\Resources\AttributeValues\Pages;

use App\Filament\Resources\AttributeValues\AttributeValueResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAttributeValue extends EditRecord
{
    protected static string $resource = AttributeValueResource::class;

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
        $values = collect($this->data['values'])
            ->map(fn($v) => trim($v))
            ->filter()
            ->unique();

        $attribute = $this->record->attribute;

        // Delete removed values
        $attribute->values()
            ->whereNotIn('value', $values)
            ->delete();

        // Add new values
        foreach ($values as $value) {
            $attribute->values()->firstOrCreate([
                'value' => $value,
            ]);
        }
    }
}
