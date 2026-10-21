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
     * Save a product.
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

        return response()->json([
            'success' => true,
            'message' => $savedProduct->wasRecentlyCreated
                ? 'Product saved successfully.'
                : 'Product is already saved.',
            'data' => [
                'id' => $savedProduct->id,
                'product_id' => $savedProduct->product_id,
                'saved_at' => $savedProduct->created_at,
            ],
        ], $savedProduct->wasRecentlyCreated ? 201 : 200);
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

        $savedProducts->through(fn(SavedProduct $savedProduct) => [
            'id' => $savedProduct->id,
            'saved_at' => $savedProduct->created_at,
            'product' => $savedProduct->product,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Saved products retrieved successfully.',
            'data' => $savedProducts->items(),
            'pagination' => [
                'current_page' => $savedProducts->currentPage(),
                'last_page' => $savedProducts->lastPage(),
                'per_page' => $savedProducts->perPage(),
                'total' => $savedProducts->total(),
                'has_more_pages' => $savedProducts->hasMorePages(),
            ],
        ]);
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

        return response()->json([
            'success' => true,
            'message' => 'Saved product retrieved successfully.',
            'data' => [
                'id' => $savedProduct->id,
                'saved_at' => $savedProduct->created_at,
                'product' => $savedProduct->product,
            ],
        ]);
    }

    /**
     * Remove a saved product.
     */
    public function destroy(
        Request $request,
        int $productId
    ): JsonResponse {
        $savedProduct = SavedProduct::query()
            ->where('user_id', $request->user()->id)
            ->where('product_id', $productId)
            ->firstOrFail();

        $savedProduct->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product removed from saved products.',
        ]);
    }
}
