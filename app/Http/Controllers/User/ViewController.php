<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\JwtAuthService;
use App\Services\ResponseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Get(
 *     path="/api/v1/user",
 *     summary="View a User account",
 *     tags={"User"},
 *     security={{"bearerAuth":{}}},
 *
 *     @OA\Response(response=200,description="OK"),
 *     @OA\Response(response=401,description="Unauthorized"),
 *     @OA\Response(response=404,description="Page not found"),
 *     @OA\Response(response=422,description="Unprocessable Entity"),
 *     @OA\Response(response=500,description="Internal server error")
 * )
 */
class ViewController extends Controller
{
    public function __construct(private JwtAuthService $jwtAuthService)
    {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $response = new ResponseService();
        $token = $request->bearerToken();

        $user = $token ? $this->jwtAuthService->authenticate($token) : null;

        if (! $user) {
            return $response->error(401, 'Unauthorized');
        }
        unset($user['id']);
        unset($user['is_admin']);

        return $response->success($user);
    }
}
