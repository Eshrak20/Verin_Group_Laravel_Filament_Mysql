<?php

namespace App\Services\Product;

use Illuminate\Database\Eloquent\Builder;
use App\Models\ProductVariant;

class ProductPriceService
{
    /**
     * Get minimum and maximum product prices.
     */
    public function getPriceRange(Builder $query): array
    {
        // Get only the IDs of products matching
        // the current filters.
        $productIds = $query->clone()
            ->select('products.id');

        $priceQuery = ProductVariant::query()
            ->whereIn('product_id', $productIds)
            ->where('status', true);

        $min = $priceQuery
            ->selectRaw("
                MIN(
                    CASE
                        WHEN sale_price IS NOT NULL
                            THEN sale_price
                        ELSE price
                    END
                ) as min_price
            ")
            ->value('min_price');

        $max = $priceQuery
            ->selectRaw("
                MAX(
                    CASE
                        WHEN sale_price IS NOT NULL
                            THEN sale_price
                        ELSE price
                    END
                ) as max_price
            ")
            ->value('max_price');

        return [
            'min' => $min !== null ? (float) $min : 0,
            'max' => $max !== null ? (float) $max : 0,
        ];
    }
}