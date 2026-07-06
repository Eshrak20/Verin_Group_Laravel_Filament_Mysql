<?php

namespace App\Services;

use App\Models\AttributeValue;
use Illuminate\Validation\ValidationException;

class ProductValidator
{
    /**
     * Validate product data before create/update.
     */
    public static function validate(array $data): void
    {
        self::validateVariants($data);
        self::validateVariantAttributes($data);
    }

    /**
     * Every product must contain at least one variant.
     */
    protected static function validateVariants(array $data): void
    {
        if (empty($data['variants'])) {
            throw ValidationException::withMessages([
                'variants' => 'A product must contain at least one variant.',
            ]);
        }
    }

    /**
     * Every selected product attribute must be used
     * by at least one variant.
     */
    protected static function validateVariantAttributes(array $data): void
    {
        $selectedAttributes = collect($data['attributes'] ?? []);

        // No product options selected → nothing to validate.
        if ($selectedAttributes->isEmpty()) {
            return;
        }

        $usedAttributes = collect($data['variants'] ?? [])
            ->flatMap(function ($variant) {

                $attributeValueIds = $variant['attribute_values'] ?? [];

                if (empty($attributeValueIds)) {
                    return [];
                }

                return AttributeValue::whereIn('id', $attributeValueIds)
                    ->pluck('attribute_id');
            })
            ->unique();

        $missing = $selectedAttributes->diff($usedAttributes);

        if ($missing->isNotEmpty()) {

            $attributeNames = \App\Models\Attribute::whereIn('id', $missing)
                ->pluck('name')
                ->implode(', ');

            throw ValidationException::withMessages([
                'attributes' => "These product options are not used by any variant: {$attributeNames}",
            ]);
        }
    }
}