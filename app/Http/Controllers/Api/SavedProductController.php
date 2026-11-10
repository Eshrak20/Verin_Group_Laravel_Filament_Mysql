<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\SavedProduct;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SavedProductController extends Controller
{
    /**
     * Toggle a product in the authenticated user's saved products.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => [
                'required',
                'integer',
                Rule::exists('products', 'id'),
            ],
        ]);

        $savedProduct = SavedProduct::query()->firstOrCreate([
            'user_id' => $request->user()->id,
            'product_id' => $validated['product_id'],
        ]);

        if (!$savedProduct->wasRecentlyCreated) {
            $savedProduct->delete();

            return $this->response(
                success: true,
                message: 'Product removed from saved products.',
                code: 200,
                data: [
                    'product_id' => (int) $validated['product_id'],
                    'is_saved' => false,
                ],
            );
        }

        return $this->response(
            success: true,
            message: 'Product saved successfully.',
            code: 201,
            data: [
                'product_id' => (int) $savedProduct->product_id,
                'is_saved' => true,
            ],
        );
    }

    /**
     * Get authenticated user's saved products.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = min(
            max((int) $request->query('per_page', 10), 1),
            50
        );

        $savedProducts = SavedProduct::query()
            ->where('user_id', $request->user()->id)
            ->whereHas('product')
            ->with([
                'product.category',
                'product.subCategory',
                'product.brand',
                'product.deliveryCharge',
                'product.variants.inventories',
            ])
            ->latest()
            ->paginate($perPage);

        $products = $savedProducts->getCollection()
            ->map(
                fn(SavedProduct $savedProduct) =>
                    $this->formatProduct(
                        $savedProduct->product,
                        $savedProduct->id
                    )
            )
            ->values()
            ->all();

        return $this->response(
            success: true,
            message: 'Saved products retrieved successfully.',
            code: 200,
            data: [
                'products' => $products,
                'total_wishlist' => $savedProducts->total(),
                'pagination' => [
                    'current_page' => $savedProducts->currentPage(),
                    'last_page' => $savedProducts->lastPage(),
                    'per_page' => $savedProducts->perPage(),
                    'total' => $savedProducts->total(),
                ],
            ],
        );
    }

    /**
     * Get one saved product belonging to the authenticated user.
     */
    public function show(
        Request $request,
        int $productId
    ): JsonResponse {
        $savedProduct = SavedProduct::query()
            ->where('user_id', $request->user()->id)
            ->where('product_id', $productId)
            ->with([
                'product.category',
                'product.subCategory',
                'product.brand',
                'product.deliveryCharge',
                'product.variants.inventories',
            ])
            ->first();

        if (!$savedProduct) {
            return $this->response(
                success: false,
                message: 'This product is not in your saved products.',
                code: 404,
                data: null,
            );
        }

        return $this->response(
            success: true,
            message: 'Saved product retrieved successfully.',
            code: 200,
            data: [
                'product' => $this->formatProduct(
                    $savedProduct->product,
                    $savedProduct->id
                ),
            ],
        );
    }

    /**
     * Format a product specifically for the wishlist frontend.
     */
    private function formatProduct(
        Product $product,
        int $savedProductId
    ): array {
        $variants = $product->variants
            ->where('status', 1);

        // Select the cheapest variant using its effective selling price.
        $selectedVariant = $variants
            ->sortBy(fn($variant) => $this->effectivePrice($variant))
            ->first();

        // Calculate available stock across active variants.
        $availableStock = $variants->sum(
            fn($variant) => $variant->inventories->sum(
                fn($inventory) => max(
                    0,
                    (float) $inventory->stock
                    - (float) $inventory->reserved_stock
                )
            )
        );

        $price = $selectedVariant
            ? (float) $selectedVariant->price
            : 0;

        $variantSalePrice = $selectedVariant
            ? (float) $selectedVariant->sale_price
            : 0;

        $salePrice = (
            $variantSalePrice > 0
            && $variantSalePrice < $price
        ) ? $variantSalePrice : $price;

        $deliveryCharge = $product->deliveryCharge;

        return [
            'saved_product_id' => $savedProductId,
            'product_id' => (int) $product->id,

            'name' => $product->name,
            'slug' => $product->slug,
            'image' => $product->variants->first()?->images->first(),

            'category_name' => $product->category?->name,
            'subcategory_name' => $product->subCategory?->name,
            'brand_name' => $product->brand?->name,

            'sku' => $selectedVariant?->sku,

            'price' => $price,
            'sale_price' => $salePrice,

            'available_stock' => (float) $availableStock,
            'in_stock' => $availableStock > 0,

            'delivery_charge' => $deliveryCharge ? [
                'id' => (int) $deliveryCharge->id,
                'name' => $deliveryCharge->name,
                'inside_dhaka_charge' => (float) $deliveryCharge->inside_dhaka_charge,
                'outside_dhaka_charge' => (float) $deliveryCharge->outside_dhaka_charge,
            ] : null,

            'is_featured' => (bool) $product->is_featured,
        ];
    }

    /**
     * Get the effective selling price of a variant.
     */
    private function effectivePrice($variant): float
    {
        $price = (float) $variant->price;
        $salePrice = (float) $variant->sale_price;

        return (
            $salePrice > 0
            && $salePrice < $price
        ) ? $salePrice : $price;
    }
}
