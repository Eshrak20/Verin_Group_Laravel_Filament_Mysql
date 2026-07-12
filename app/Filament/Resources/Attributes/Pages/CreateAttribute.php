<?php

namespace App\Filament\Resources\Attributes\Pages;

use App\Filament\Resources\Attributes\AttributeResource;
use App\Models\Attribute;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\ValidationException;

class CreateAttribute extends CreateRecord
{
    protected static string $resource = AttributeResource::class;
    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
    protected function handleRecordCreation(array $data): Attribute
    {
        $values = collect($data['values'] ?? [])
            ->map(fn($value) => trim($value))
            ->filter()
            ->unique()
            ->values();

        // Remove values before creating the Attribute
        unset($data['values']);

        // Create the Attribute
        $attribute = Attribute::create($data);

        // Create the Attribute Values
        foreach ($values as $value) {
            $attribute->values()->create([
                'value' => $value,
            ]);
        }

        return $attribute;
    }
}
