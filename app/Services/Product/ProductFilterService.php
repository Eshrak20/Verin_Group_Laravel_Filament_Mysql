<?php

namespace App\Services\Product;

use Illuminate\Database\Eloquent\Builder;

class ProductFilterService
{
    /**
     * Generate filters from the current category/context.
     *
     * Attribute selections are intentionally NOT applied.
     *
     * Counts represent unique products, not variants.
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
                            'type' => 'checkbox',
                            'options' => [],
                        ];
                    }

                    if (!isset($filters[$attributeId]['options'][$value])) {
                        $filters[$attributeId]['options'][$value] = [
                            'value' => $value,
                            'product_ids' => [],
                        ];
                    }

                    $filters[$attributeId]['options'][$value]['product_ids'][] = $product->id;
                }
            }
        }

        foreach ($filters as &$filter) {
            foreach ($filter['options'] as &$option) {
                $option['count'] = count(
                    array_unique($option['product_ids'])
                );

                unset($option['product_ids']);
            }

            $filter['options'] = array_values($filter['options']);
        }

        return array_values($filters);
    }
}
