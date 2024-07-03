<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Repositories\User\UserRepositoryInterface;
use App\Services\JwtAuthService;
use App\Services\ResponseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Delete(
 *     path="/api/v1/user",
 *     tags={"User"},
 *     summary="Delete a User account",
 *     security={{"bearerAuth":{}}},
 *
 *     @OA\Response(response=200,description="OK"),
 *     @OA\Response(response=401,description="Unauthorized"),
 *     @OA\Response(response=404,description="User not found"),
 *     @OA\Response(response=422,description="Unprocessable Entity"),
 *     @OA\Response(response=500,description="Internal server error")
 * )
 */
class DeleteController extends Controller
{
    public function __construct(private UserRepositoryInterface $userRepository, private JwtAuthService $jwtAuthService) {}

    public function __invoke(Request $request): JsonResponse
    {
        $response = new ResponseService();
        $token = $request->bearerToken();

        $user = $token ? $this->jwtAuthService->authenticate($token) : null;
        if (! $user) {
            return $response->error(401, 'Unauthorized');
        }

        if ($this->userRepository->delete($user->id)) {
            return $response->success([]);
        }

        return $response->error(500, 'Failed to delete user');
    }
}
