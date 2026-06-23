<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\ClientReview;

class ClientReviewController extends Controller
{
    // Fetches active client reviews
    public function index()
    {
        $reviews = ClientReview::where('is_active', true)
            ->orderBy('sort_order')
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $reviews
        ]);
    }

    // Renamed this method to avoid the collision
    public function clients()
    {
        $clients = Client::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $clients
        ]);
    }
}