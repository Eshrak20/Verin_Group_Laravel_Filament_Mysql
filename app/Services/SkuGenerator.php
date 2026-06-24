<?php

namespace App\Services;

use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Brand;
use App\Models\AttributeValue;


class SkuGenerator
{
    /**
     * Generate a unique SKU based on product details and variant attributes.
     */
    public static function generate(?int $categoryId, ?int $subCategoryId, ?int $brandId, array $attributeValueIds): ?string
    {
        // 1. Enforce rule: Must have category and sub-category
        if (!$categoryId || !$subCategoryId) {
            return null;
        }

        // 2. Fetch records efficiently
        $category = Category::find($categoryId);
        $subCategory = SubCategory::find($subCategoryId);
        $brand = $brandId ? Brand::find($brandId) : null;

        // 3. Create shortcodes (First 3 letters)
        $catCode = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $category?->name ?? 'CAT'), 0, 3));
        $subCode = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $subCategory?->name ?? 'SUB'), 0, 3));
        $brandCode = $brand ? strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $brand->name), 0, 3)) : 'XXX';

        // 4. Extract Attribute codes (e.g., "XL", "RED") to make the variant unique
        $attributeCode = 'GEN'; // Default generic code if no options selected
        if (!empty($attributeValueIds)) {
            $values = AttributeValue::whereIn('id', $attributeValueIds)->pluck('value')->toArray();
            if (!empty($values)) {
                // Take up to 2 attributes, sanitize, grab first 2 letters of each, join them
                $shortValues = array_map(function($val) {
                    return strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $val), 0, 2));
                }, $values);
                $attributeCode = implode('', array_slice($shortValues, 0, 2));
            }
        }

        // 5. High-performance unique identifier (avoids collisions)
        // Using a timestamp slice or random digits prevents variants created at the same microsecond from clashing
        $uniqueId = strtoupper(substr(uniqid(), -4)); 

        // Result Format Example: ELEC-PHON-APPL-REDXL-A2F3
        return sprintf(
            '%s-%s-%s-%s-%s',
            $catCode,
            $subCode,
            $brandCode,
            $attributeCode,
            $uniqueId
        );
    }
}