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
     * Save if not saved; remove if already saved.
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

        // Already saved: remove it.
        if (! $savedProduct->wasRecentlyCreated) {
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

        // Not saved: save it.
        return $this->response(
            success: true,
            message: 'Product saved successfully.',
            code: 201,
            data: [
                'id' => $savedProduct->id,
                'product_id' => $savedProduct->product_id,
                'saved_at' => $savedProduct->created_at,
                'is_saved' => true,
            ],
        );
    }

    /**
     * Get the authenticated user's saved products.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = min(
            max((int) $request->query('per_page', 10), 1),
            50
        );

        $savedProducts = SavedProduct::query()
            ->where('user_id', $request->user()->id)
            ->with([
                'product.deliveryCharge',
                'product.variants.inventories',
            ])
            ->latest()
            ->paginate($perPage);

        $data = $savedProducts->getCollection()
            ->map(fn (SavedProduct $savedProduct) => [
                'id' => $savedProduct->id,
                'saved_at' => $savedProduct->created_at,
                'product' => $savedProduct->product
                    ? $this->formatProduct($savedProduct->product)
                    : null,
            ])
            ->values()
            ->all();

        return $this->response(
            success: true,
            message: 'Saved products retrieved successfully.',
            code: 200,
            data: [
                'items' => $data,
                'pagination' => [
                    'current_page' => $savedProducts->currentPage(),
                    'last_page' => $savedProducts->lastPage(),
                    'per_page' => $savedProducts->perPage(),
                    'total' => $savedProducts->total(),
                    'has_more_pages' => $savedProducts->hasMorePages(),
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
                'product.deliveryCharge',
                'product.variants.inventories',
            ])
            ->firstOrFail();

        return $this->response(
            success: true,
            message: 'Saved product retrieved successfully.',
            code: 200,
            data: [
                'id' => $savedProduct->id,
                'saved_at' => $savedProduct->created_at,
                'product' => $savedProduct->product
                    ? $this->formatProduct($savedProduct->product)
                    : null,
            ],
        );
    }

    /**
     * Format product data and return delivery charges as JSON numbers.
     */
    private function formatProduct(Product $product): array
    {
        $data = $product->toArray();

        // Eloquent serializes the deliveryCharge relation as delivery_charge.
        if (isset($data['delivery_charge'])) {
            foreach ([
                'inside_dhaka_charge',
                'outside_dhaka_charge',
            ] as $field) {
                if (
                    isset($data['delivery_charge'][$field])
                    && is_numeric($data['delivery_charge'][$field])
                ) {
                    $data['delivery_charge'][$field] =
                        (float) $data['delivery_charge'][$field];
                }
            }
        }

        return $data;
    }
}
