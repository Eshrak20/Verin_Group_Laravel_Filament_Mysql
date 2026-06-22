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
        // Start your query builder with relationships pre-loaded
        $query = Product::query()->with(['category', 'subCategory', 'brand', 'variants.images']);

        // Pass the builder instance, request context, and target search text keys down to the trait
        $products = $this->scopeFilterSortPaginate($query, $request, ['name', 'short_description']);

        return response()->json([
            'success' => true,
            'message' => 'Products retrieved successfully.',
            'data'    => $products->items(),
            'meta'    => [
                'current_page' => $products->currentPage(),
                'last_page'    => $products->lastPage(),
                'per_page'     => $products->perPage(),
                'total'        => $products->total(),
                // 'next_page'    => $products->nextPageUrl(),
                // 'prev_page'    => $products->previousPageUrl(),
            ],
        ], 200);
    }
}