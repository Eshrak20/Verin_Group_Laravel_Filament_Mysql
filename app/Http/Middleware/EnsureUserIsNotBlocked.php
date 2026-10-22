<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsNotBlocked
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->is_blocked) {
            $user->tokens()->delete();

            return response()->json([
                'success' => false,
                'message' => 'Your account has been blocked.',
            ], 403);
        }

        return $next($request);
    }
}