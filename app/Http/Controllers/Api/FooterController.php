<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FooterSetting;
use Illuminate\Http\JsonResponse;

class FooterController extends Controller
{
    public function index(): JsonResponse
    {
        $footer = FooterSetting::with([
            'socialLinks',
            'links',
            'contactInfo',
        ])->get();

        return response()->json([
            'success' => true,
            'data' => $footer,
        ]);
    }
}
