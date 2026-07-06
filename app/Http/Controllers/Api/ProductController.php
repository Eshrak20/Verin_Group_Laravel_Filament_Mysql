<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Traits\FilterAndPaginate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // Inject the reusable filtering and pagination engine
    use FilterAndPaginate;

    /**
     * Display a listing of products with dynamic filters.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Product::query()
            ->with([
                'category',
                'subCategory',
                'brand',
                'variants.images',
                'variants.videos',
            ]);

        // Featured
        if ($request->filled('is_featured')) {
            $query->where('is_featured', $request->boolean('is_featured'));
        }

        // Category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Sub Category
        if ($request->filled('sub_category_id')) {
            $query->where('sub_category_id', $request->sub_category_id);
        }

        // Brand
        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        // Price Range
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Stock
        if ($request->filled('in_stock')) {
            if ($request->boolean('in_stock')) {
                $query->where('stock', '>', 0);
            } else {
                $query->where('stock', 0);
            }
        }

        // Random Order
        $query->inRandomOrder();

        $products = $this->scopeFilterSortPaginate(
            $query,
            $request,
            ['name', 'short_description']
        );

        return response()->json([
            'success' => true,
            'message' => 'Products retrieved successfully.',
            'data' => $products->items(),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
        ]);
    }
    public function show(string $slug): JsonResponse
    {
        $product = Product::with([
            'category',
            'subCategory',
            'brand',
            'variants.images',
            'variants.videos',
            'attributes',
            'attributes' => function ($query) {
                $query->with('values');
            },
        ])
            ->where('slug', $slug)
            ->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Product retrieved successfully.',
            'data' => $product,
        ]);
    }
}
