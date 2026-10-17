<?php

namespace App\Services\Product;

use Illuminate\Database\Eloquent\Builder;

class ProductFilterService
{
    /**
     * Generate filters from the current category/context.
     *
     * Attribute selections are intentionally NOT applied.
     * Options contain unique attribute values without counts.
     */
    public function getDynamicFilters(Builder $query): array
    {
        $products = $query
            ->with([
                'variants.attributeValues.attribute',
            ])
            ->get();

        $filters = [];

        foreach ($products as $product) {
            foreach ($product->variants as $variant) {
                foreach ($variant->attributeValues as $attributeValue) {
                    $attribute = $attributeValue->attribute;

                    if (!$attribute) {
                        continue;
                    }

                    $attributeId = $attribute->id;
                    $value = $attributeValue->value;

                    if (!isset($filters[$attributeId])) {
                        $filters[$attributeId] = [
                            'id' => $attributeId,
                            'name' => $attribute->name,
                            'options' => [],
                        ];
                    }

                    $filters[$attributeId]['options'][$value] = $value;
                }
            }
        }

        foreach ($filters as &$filter) {
            $filter['options'] = array_values($filter['options']);
        }

        unset($filter);

        return array_values($filters);
    }
}