<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;

class ResponseService
{
    public function success(array|object $data): JsonResponse
    {
        return response()->json([
            'success' => 1,
            'data' => $data,
            'error' => null,
            'errors' => [],
            'extra' => [],
        ], 200);
    }

    public function error(int $code = 401, string $errorMessage = 'Unauthorized', array|object $errors = []): JsonResponse
    {
        return response()->json([
            'success' => 0,
            'data' => [],
            'error' => $errorMessage,
            'errors' => $errors,
            'trace' => [],
        ], $code);
    }
}
