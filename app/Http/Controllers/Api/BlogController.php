<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Blog;

class BlogController extends Controller
{
    public function index()
    {
        return Blog::where('status', 'published')
            ->latest()
            ->paginate(10);
    }

    public function show($slug)
    {
        $blog = Blog::where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        // increase views
        $blog->increment('views');

        return response()->json($blog);
    }
}