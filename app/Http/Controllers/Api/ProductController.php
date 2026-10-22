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
    ) {}

    /**
     * Product listing.
     *
     * Returns lightweight product-card data.
     */
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category_id'    => ['sometimes', 'nullable', 'integer', 'exists:categories,id'],
            'sub_category_id' => ['sometimes', 'nullable', 'integer'],
            'brand_id'       => ['sometimes', 'nullable', 'integer'],

            'search'         => ['sometimes', 'nullable', 'string', 'max:255'],

            'is_featured'    => ['sometimes', 'nullable', 'boolean'],
            'in_stock'       => ['sometimes', 'nullable', 'boolean'],

            'price_min'      => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'price_max'      => ['sometimes', 'nullable', 'numeric', 'min:0', 'gte:price_min'],

            'attributes'     => ['sometimes', 'nullable', 'array'],
            'attributes.*'   => ['array'],
            'attributes.*.*' => ['string', 'max:255'],

            'page'           => ['sometimes', 'nullable', 'integer', 'min:1'],
            'per_page'       => ['sometimes', 'nullable', 'integer', 'min:1', 'max:100'],

            'sort_by'        => ['sometimes', 'nullable', 'string', 'in:created_at,name,price'],
            'sort_order'     => ['sometimes', 'nullable', 'string', 'in:asc,desc'],
        ]);

        /*
    |--------------------------------------------------------------------------
    | Base Query
    |--------------------------------------------------------------------------
    */

        $baseQuery = Product::query()
            ->with([
                'category:id,name',
                'subCategory:id,name',
                'brand:id,name',
                'variants.attributeValues.attribute',
                'variants.images',
                'variants.inventories',
            ]);

        // Category is optional.
        if ($request->filled('category_id')) {
            $baseQuery->where(
                'category_id',
                $request->integer('category_id')
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

        if ($request->filled('is_featured')) {
            $baseQuery->where(
                'is_featured',
                $request->boolean('is_featured')
            );
        }

        /*
    |--------------------------------------------------------------------------
    | Search
    |--------------------------------------------------------------------------
    */

        $search = trim(
            (string) $request->input('search', $request->input('q', ''))
        );

        if ($search !== '') {
            $term = '%' . $search . '%';

            $baseQuery->where(function ($query) use ($term) {
                $query
                    ->where('name', 'like', $term)
                    ->orWhere('slug', 'like', $term)
                    ->orWhere('short_description', 'like', $term)

                    ->orWhereHas('variants', function ($variantQuery) use ($term) {
                        $variantQuery->where('sku', 'like', $term);
                    })

                    ->orWhereHas('brand', function ($brandQuery) use ($term) {
                        $brandQuery->where('name', 'like', $term);
                    })

                    ->orWhereHas('category', function ($categoryQuery) use ($term) {
                        $categoryQuery->where('name', 'like', $term);
                    })

                    ->orWhereHas('subCategory', function ($subCategoryQuery) use ($term) {
                        $subCategoryQuery->where('name', 'like', $term);
                    });
            });
        }

        /*
    |--------------------------------------------------------------------------
    | Product Query
    |--------------------------------------------------------------------------
    */

        $productQuery = clone $baseQuery;

        $this->applyAttributeFilters($productQuery, $request);
        $this->applyStockFilter($productQuery, $request);
        $this->applyPriceFilter($productQuery, $request);

        /*
    |--------------------------------------------------------------------------
    | Dynamic Filters
    |--------------------------------------------------------------------------
    |
    | ProductFilterService::getDynamicFilters() currently accepts
    | exactly one argument, so pass only the query.
    |
    */

        $filters = $this->filterService->getDynamicFilters(
            clone $baseQuery
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
    | Paginated Products
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
    /**
     * Apply stock filter using inventory availability.
     */
    private function applyStockFilter(
        $query,
        Request $request
    ): void {
        if (!$request->filled('in_stock')) {
            return;
        }

        $availableInventory = function ($inventoryQuery) {
            $inventoryQuery->whereRaw('stock > COALESCE(reserved, 0)');
        };

        if ($request->boolean('in_stock')) {
            $query->whereHas(
                'variants.inventories',
                $availableInventory
            );

            return;
        }

        $query->whereDoesntHave(
            'variants.inventories',
            $availableInventory
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
                'deliveryCharge',
                'variants.images',
                'variants.videos',
                'variants.inventories',
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

        $productData = $this->responseService->transformProduct($product);

        $productData['delivery_charge'] = $product->deliveryCharge
            ? [
                'id' => $product->deliveryCharge->id,
                'name' => $product->deliveryCharge->name,
                'inside_dhaka_charge' => (float) $product->deliveryCharge->inside_dhaka_charge,
                'outside_dhaka_charge' => (float) $product->deliveryCharge->outside_dhaka_charge,
                'description' => $product->deliveryCharge->description,
            ]
            : null;

        return response()->json([
            'success' => true,
            'message' => 'Product retrieved successfully.',
            'data' => $productData,
        ]);
    }
}
