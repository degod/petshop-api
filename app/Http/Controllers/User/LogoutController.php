<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\JwtAuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\ResponseService;

/**
 * @OA\Get(
 *     path="/api/v1/user/logout",
 *     tags={"User"},
 *     summary="Logout an User Account",
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(response=200,description="OK"),
 *     @OA\Response(response=401,description="Unauthorized"),
 *     @OA\Response(response=404,description="Page not found"),
 *     @OA\Response(response=422,description="Unprocessable Entity"),
 *     @OA\Response(response=500,description="Internal server error")
 * )
 */
class LogoutController extends Controller
{
    protected $jwtAuthService;

    public function __construct(JwtAuthService $jwtAuthService)
    {
        $this->jwtAuthService = $jwtAuthService;
    }

    public function __invoke(Request $request): JsonResponse
    {
        $response = new ResponseService();
        $token = $request->bearerToken();

        $this->jwtAuthService->revokeToken($token);
        return $response->success([]);
    }
}
