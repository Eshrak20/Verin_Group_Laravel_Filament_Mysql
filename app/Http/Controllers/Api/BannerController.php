<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Banner;

class BannerController extends Controller
{
    public function getBanner($pageName)
    {
        $banner = Banner::where('page_name', $pageName)
            ->where('status', true)
            ->orderBy('sorting_number')
            ->first();

        if (!$banner) {
            return response()->json([
                'success' => false,
                'message' => 'Banner not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $banner,
        ]);
    }
}