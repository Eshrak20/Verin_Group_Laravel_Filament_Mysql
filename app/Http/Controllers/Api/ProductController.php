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

    public function index(Request $request): JsonResponse
    {
        // dd($request->all());
        $query = Product::query()
            ->with([
                'category',
                'subCategory',
                'brand',
                'variants.images',
                'variants.videos',
                'attributes',
                'attributes' => function ($query) {
                    $query->with('values');
                },
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
        // Dynamic Attribute Filters
        if ($request->filled('attributes')) {

            foreach ($request->attributes as $attributeName => $values) {

                $values = is_array($values)
                    ? $values
                    : explode(',', $values);

                $values = array_map('trim', $values);

                $query->whereHas('variants', function ($variantQuery) use ($attributeName, $values) {

                    $variantQuery->whereHas('attributeValues', function ($valueQuery) use ($attributeName, $values) {

                        $valueQuery->whereIn('value', $values)
                            ->whereHas('attribute', function ($attributeQuery) use ($attributeName) {

                                $attributeQuery->where('name', $attributeName);
                            });
                    });
                });
            }
        }

        // Random Order
        $query->inRandomOrder();
        if ($request->filled('attributes')) {

            foreach ($request->input('attributes') as $attributeName => $values) {

                $values = is_array($values)
                    ? $values
                    : explode(',', $values);

                $query->whereHas('variants.attributeValues', function ($q) use ($attributeName, $values) {

                    $q->whereIn('value', $values)
                        ->whereHas('attribute', function ($q) use ($attributeName) {
                            $q->where('name', $attributeName);
                        });
                });
            }
        }

        // dd($query->toSql(), $query->getBindings());
        $products = $this->scopeFilterSortPaginate(
            $query,
            $request,
            ['name','slug','short_description']
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
