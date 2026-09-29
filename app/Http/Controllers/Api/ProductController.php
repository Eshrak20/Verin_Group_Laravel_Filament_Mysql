<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\Product\ProductFilterService;
use App\Services\Product\ProductPriceService;
use App\Services\Product\ProductResponseService;
use App\Traits\FilterAndPaginate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    use FilterAndPaginate;

    public function __construct(
        protected ProductFilterService $filterService,
        protected ProductResponseService $responseService,
        protected ProductPriceService $priceService
    ) {
    }

    /**
     * Product listing.
     *
     * Returns lightweight product-card data.
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'category_id' => ['required', 'integer'],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Base Query
        |--------------------------------------------------------------------------
        | Used for dynamic filters.
        |
        | Attribute filters are NOT applied here because the filter sidebar
        | should always show all available values for the current context.
        */
        $baseQuery = Product::query()
            ->with([
                'category:id,name',
                'subCategory:id,name',
                'brand:id,name',

                // Required by ProductFilterService
                'variants.attributeValues.attribute',

                // Required for product-card images
                'variants.images',
            ])
            ->where('category_id', $request->category_id);

        /*
        |--------------------------------------------------------------------------
        | Basic Filters
        |--------------------------------------------------------------------------
        */

        if ($request->filled('is_featured')) {
            $baseQuery->where(
                'is_featured',
                $request->boolean('is_featured')
            );
        }

        if ($request->filled('sub_category_id')) {
            $baseQuery->where(
                'sub_category_id',
                $request->integer('sub_category_id')
            );
        }

        if ($request->filled('brand_id')) {
            $baseQuery->where(
                'brand_id',
                $request->integer('brand_id')
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Product Query
        |--------------------------------------------------------------------------
        | This query receives the actual selected filters.
        */
        $productQuery = clone $baseQuery;

        $this->applyAttributeFilters(
            $productQuery,
            $request
        );

        $this->applyStockFilter(
            $productQuery,
            $request
        );

        $this->applyPriceFilter(
            $productQuery,
            $request
        );

        /*
        |--------------------------------------------------------------------------
        | Dynamic Filters
        |--------------------------------------------------------------------------
        */
        $filters = $this->filterService->getDynamicFilters(
            clone $baseQuery,
            $request
        );

        /*
        |--------------------------------------------------------------------------
        | Price Range
        |--------------------------------------------------------------------------
        */
        $priceRange = $this->priceService->getPriceRange(
            clone $productQuery
        );

        /*
        |--------------------------------------------------------------------------
        | Products
        |--------------------------------------------------------------------------
        */
        $products = $this->scopeFilterSortPaginate(
            $productQuery,
            $request,
            [
                'name',
                'slug',
                'short_description',
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Products retrieved successfully.',

            'data' => [
                'filters' => $filters,

                'price' => $priceRange,

                'products' => $this->responseService
                    ->transformCollection($products->items()),

                'meta' => [
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total(),
                ],
            ],
        ]);
    }

    /**
     * Apply dynamic attribute filters.
     */
    private function applyAttributeFilters(
        $query,
        Request $request
    ): void {
        if (!$request->filled('attributes')) {
            return;
        }

        foreach ($request->input('attributes') as $attributeId => $values) {
            $values = is_array($values)
                ? $values
                : explode(',', $values);

            $values = array_map('trim', $values);

            $query->whereHas(
                'variants.attributeValues',
                function ($valueQuery) use ($attributeId, $values) {
                    $valueQuery
                        ->where('attribute_id', $attributeId)
                        ->whereIn('value', $values);
                }
            );
        }
    }

    /**
     * Apply stock filter.
     */
    private function applyStockFilter(
        $query,
        Request $request
    ): void {
        if (!$request->filled('in_stock')) {
            return;
        }

        if ($request->boolean('in_stock')) {
            $query->whereHas(
                'variants',
                fn ($q) => $q->where('stock', '>', 0)
            );

            return;
        }

        $query->whereDoesntHave(
            'variants',
            fn ($q) => $q->where('stock', '>', 0)
        );
    }

    /**
     * Apply price filter.
     */
    private function applyPriceFilter(
        $query,
        Request $request
    ): void {
        if (
            !$request->filled('price_min') &&
            !$request->filled('price_max')
        ) {
            return;
        }

        $min = $request->input('price_min');
        $max = $request->input('price_max');

        $query->whereHas(
            'variants',
            function ($variantQuery) use ($min, $max) {
                $variantQuery->where(function ($priceQuery) use (
                    $min,
                    $max
                ) {
                    // Sale price
                    $priceQuery->where(function ($q) use ($min, $max) {
                        $q->whereNotNull('sale_price');

                        if ($min !== null) {
                            $q->where('sale_price', '>=', $min);
                        }

                        if ($max !== null) {
                            $q->where('sale_price', '<=', $max);
                        }
                    })

                        // Regular price
                        ->orWhere(function ($q) use ($min, $max) {
                            $q->whereNull('sale_price');

                            if ($min !== null) {
                                $q->where('price', '>=', $min);
                            }

                            if ($max !== null) {
                                $q->where('price', '<=', $max);
                            }
                        });
                });
            }
        );
    }

    /**
     * Show single product.
     *
     * Full product information is returned here.
     */
    public function show(string $slug): JsonResponse
    {
        $product = Product::query()
            ->with([
                'category',
                'subCategory',
                'brand',
                'variants.images',
                'variants.videos',
                'variants.attributeValues.attribute',
                'attributes',
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
            'data' => $this->responseService->transformProduct($product),
        ]);
    }
}
