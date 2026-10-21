<?php

namespace App\Services\Product;

use App\Models\Product;
use Illuminate\Support\Collection;

class ProductResponseService
{
    /**
     * Transform multiple products for product listing/cards.
     */
    public function transformCollection(array|Collection $products): array
    {
        return collect($products)
            ->map(fn(Product $product): array => $this->transformForListing($product))
            ->values()
            ->all();
    }

    /**
     * Transform a product for product listing/card.
     *
     * Only returns the fields required by the product card.
     */
    public function transformForListing(Product $product): array
    {
        $variant = $product->variants->first();

        return [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'short_description' => $product->short_description,
            'is_featured' => $product->is_featured,
            'status' => $product->status,
            'stock' => $variant->stock,
            'category' => $product->category?->name,
            'sub_category' => $product->subCategory?->name,
            'brand' => $product->brand?->name,

            'variant' => $variant
                ? [
                    'sku' => $variant->sku,
                    'price' => $variant->price,
                    'sale_price' => $variant->sale_price,

                    'images' => $variant->images
                        ->take(2)
                        ->pluck('image_url')
                        ->values()
                        ->all(),
                ]
                : null,
        ];
    }

    /**
     * Transform a single product for the product details page.
     */
    public function transformProduct(Product $product): array
    {
        return [
            /*
            |--------------------------------------------------------------------------
            | Product Information
            |--------------------------------------------------------------------------
            */
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,

            'short_description' => $product->short_description,
            'description' => $product->description,

            'is_featured' => $product->is_featured,
            'status' => $product->status,

            /*
            |--------------------------------------------------------------------------
            | Category / Sub Category / Brand
            |--------------------------------------------------------------------------
            */
            'category' => $product->category
                ? [
                    'name' => $product->category->name,
                    'slug' => $product->category->slug,
                ]
                : null,

            'sub_category' => $product->subCategory
                ? [
                    'name' => $product->subCategory->name,
                    'slug' => $product->subCategory->slug,
                ]
                : null,

            'brand' => $product->brand
                ? [
                    'name' => $product->brand->name,
                    'slug' => $product->brand->slug,
                ]
                : null,

            /*
            |--------------------------------------------------------------------------
            | Variants
            |--------------------------------------------------------------------------
            |
            | Every variant is returned because the frontend needs to
            | determine price, stock, images and attribute combinations.
            |
            */
            'variants' => $product->variants
                ->map(fn($variant) => [
                    'sku' => $variant->sku,
                    'price' => $variant->price,
                    'sale_price' => $variant->sale_price,
                    'stock' => $variant->stock,
                    'status' => $variant->status,

                    'images' => $variant->images
                        ->pluck('image_url')
                        ->values()
                        ->all(),

                    'videos' => $variant->videos
                        ->pluck('video_url')
                        ->values()
                        ->all(),

                    'attributes' => $variant->attributeValues
                        ->map(fn($attributeValue) => [
                            'id' => $attributeValue->attribute_id,
                            'name' => $attributeValue->attribute?->name,
                            'value_id' => $attributeValue->id,
                            'value' => $attributeValue->value,
                        ])
                        ->values()
                        ->all(),
                ])
                ->values()
                ->all(),

            /*
            |--------------------------------------------------------------------------
            | Available Attributes
            |--------------------------------------------------------------------------
            |
            | Used by frontend to build variant selectors.
            |
            */
            'attributes' => $this->getActualAttributes($product),
        ];
    }

    /**
     * Get actual attributes used by product variants.
     *
     * Example:
     *
     * Color:
     *   Red
     *   Blue
     *
     * Size:
     *   M
     *   L
     *   XL
     */
    private function getActualAttributes(Product $product): array
    {
        $attributes = [];

        foreach ($product->variants as $variant) {
            foreach ($variant->attributeValues as $attributeValue) {
                $attribute = $attributeValue->attribute;

                if (!$attribute) {
                    continue;
                }

                if (!isset($attributes[$attribute->id])) {
                    $attributes[$attribute->id] = [
                        'id' => $attribute->id,
                        'name' => $attribute->name,
                        'type' => 'checkbox',
                        'values' => [],
                    ];
                }

                $alreadyExists = collect(
                    $attributes[$attribute->id]['values']
                )->contains(
                    'id',
                    $attributeValue->id
                );

                if (!$alreadyExists) {
                    $attributes[$attribute->id]['values'][] = [
                        'id' => $attributeValue->id,
                        'value' => $attributeValue->value,
                    ];
                }
            }
        }

        return array_values($attributes);
    }
}
