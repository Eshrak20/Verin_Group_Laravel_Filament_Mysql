<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Brand;
use Illuminate\Http\JsonResponse;

class CatalogController extends Controller
{
    // 📦 Get all categories
    public function categories(): JsonResponse
    {
        return response()->json([
            'status' => true,
            'data' => Category::select('id', 'name', 'slug', 'icon', 'image', 'status')->get()
        ]);
    }

    // 📂 Get all subcategories
    public function subCategories(): JsonResponse
    {
        return response()->json([
            'status' => true,
            'data' => SubCategory::with('category:id,name')
                ->select('id', 'category_id', 'name', 'slug', 'icon', 'image', 'status')
                ->get()
        ]);
    }

    // 🏷️ Get all brands
    public function brands(): JsonResponse
    {
        return response()->json([
            'status' => true,
            'data' => Brand::with('subCategory.category:id,name')
                ->select('id', 'sub_category_id', 'name', 'slug', 'logo', 'image', 'status')
                ->get()
        ]);
    }
}