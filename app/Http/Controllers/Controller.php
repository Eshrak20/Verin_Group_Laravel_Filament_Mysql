<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

abstract class Controller
{
    public function response(
        bool $success,
        string $message,
        int $code = 200,
        mixed $data = null
    ): JsonResponse {
        return self::apiResponse($success, $message, $code, $data);
    }

    public static function apiResponse(
        bool $success,
        string $message,
        int $code = 200,
        mixed $data = null
    ): JsonResponse {
        return response()->json([
            'success' => $success,
            'message' => $message,
            'code' => $code,
            'data' => $data,
        ], $code);
    }
}
