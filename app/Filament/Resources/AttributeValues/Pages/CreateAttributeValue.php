<?php

namespace App\Filament\Resources\AttributeValues\Pages;

use App\Models\AttributeValue;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\AttributeValues\AttributeValueResource;
use Illuminate\Validation\ValidationException;

class CreateAttributeValue extends CreateRecord
{
    protected static string $resource = AttributeValueResource::class;

    protected function handleRecordCreation(array $data): AttributeValue
    {
        $values = collect($data['values'])
            ->map(fn ($value) => trim($value))
            ->filter()
            ->unique();

        // Check duplicates in database
        $existing = AttributeValue::query()
            ->where('attribute_id', $data['attribute_id'])
            ->whereIn('value', $values)
            ->pluck('value')
            ->toArray();

        if (! empty($existing)) {
            throw ValidationException::withMessages([
                'values' => 'These values already exist: ' . implode(', ', $existing),
            ]);
        }

        $first = null;

        foreach ($values as $value) {
            $record = AttributeValue::create([
                'attribute_id' => $data['attribute_id'],
                'value' => $value,
            ]);

            $first ??= $record;
        }

        return $first;
    }
}