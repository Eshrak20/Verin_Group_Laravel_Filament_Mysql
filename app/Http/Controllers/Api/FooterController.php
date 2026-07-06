<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FooterSetting;
use Illuminate\Http\JsonResponse;

class FooterController extends Controller
{
    // All footer data (all companies)
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

    // Pages list by company
    public function pages(string $company): JsonResponse
    {
        $footer = FooterSetting::where('company_key', $company)
            ->firstOrFail();
            

        $pages = $footer->pages()
            ->where('is_published', true)
            ->get();

        return response()->json([
            'success' => true,
            'company' => $footer->company_name,
            'data' => $pages
        ]);
    }

    // Single page by company + page type
    public function showPage(string $company, string $page_type): JsonResponse
    {
        $footer = FooterSetting::where('company_key', $company)
            ->firstOrFail();

        $page = $footer->pages()
            ->where('page_type', $page_type)
            ->where('is_published', true)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'company' => $footer->company_name,
            'data' => $page
        ]);
    }
}