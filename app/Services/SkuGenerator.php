<?php

namespace App\Services;

use App\Models\AttributeValue;
use App\Models\Brand;
use App\Models\Category;
use App\Models\ProductVariant;
use App\Models\SubCategory;

class SkuGenerator
{
    /**
     * Generate a unique SKU.
     *
     * Example:
     * ELE-PHO-APP-REXL-4F7A
     */
    public static function generate(
        ?int $categoryId,
        ?int $subCategoryId,
        ?int $brandId,
        array $attributeValueIds = [],
    ): ?string {

        if (! $categoryId || ! $subCategoryId) {
            return null;
        }

        $category = Category::find($categoryId);
        $subCategory = SubCategory::find($subCategoryId);
        $brand = $brandId ? Brand::find($brandId) : null;

        $catCode = self::shortCode($category?->name, 'CAT');
        $subCode = self::shortCode($subCategory?->name, 'SUB');
        $brandCode = self::shortCode($brand?->name, 'GEN');

        /*
        |--------------------------------------------------------------------------
        | Attribute Code
        |--------------------------------------------------------------------------
        */

        $attributeCode = 'GEN';

        if (! empty($attributeValueIds)) {

            $values = AttributeValue::whereIn('id', $attributeValueIds)
                ->pluck('value')
                ->toArray();

            if (! empty($values)) {

                $attributeCode = collect($values)
                    ->take(2)
                    ->map(fn($value) => self::shortCode($value, '', 2))
                    ->implode('');
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Ensure uniqueness
        |--------------------------------------------------------------------------
        */

        do {

            // 4 random uppercase hex characters
            $random = strtoupper(bin2hex(random_bytes(2)));

            $sku = "{$catCode}-{$subCode}-{$brandCode}-{$attributeCode}-{$random}";
        } while (
            ProductVariant::where('sku', $sku)->exists()
        );

        return $sku;
    }

    /**
     * Create shortcode.
     */
    protected static function shortCode(
        ?string $text,
        string $default,
        int $length = 3,
    ): string {

        if (blank($text)) {
            return $default;
        }

        $text = preg_replace('/[^A-Za-z0-9]/', '', strtoupper($text));

        return substr($text, 0, $length) ?: $default;
    }
    public static function generateCloneSku(string $sku): string
    {
        // Match the trailing numeric portion
        if (! preg_match('/^(.*?)-(\d+)$/', $sku, $matches)) {
            return $sku;
        }

        $prefix = $matches[1];
        $number = (int) $matches[2];
        $length = strlen($matches[2]);

        do {

            $number++;

            $newSku = $prefix . '-' . str_pad(
                (string) $number,
                $length,
                '0',
                STR_PAD_LEFT
            );
        } while (
            ProductVariant::where('sku', $newSku)->exists()
        );

        return $newSku;
    }
}
