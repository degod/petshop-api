<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\ResponseService;
use App\Services\JwtAuthService;

/**
 * @OA\Delete(
 *     path="/api/v1/user",
 *     tags={"User"},
 *     summary="Delete a User account",
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(response=200,description="OK"),
 *     @OA\Response(response=401,description="Unauthorized"),
 *     @OA\Response(response=404,description="User not found"),
 *     @OA\Response(response=422,description="Unprocessable Entity"),
 *     @OA\Response(response=500,description="Internal server error")
 * )
 */
class DeleteController extends Controller
{
    protected $userRepository;
    protected $jwtAuthService;

    public function __construct(UserRepositoryInterface $userRepository, JwtAuthService $jwtAuthService)
    {
        $this->userRepository = $userRepository;
        $this->jwtAuthService = $jwtAuthService;
    }

    public function __invoke(Request $request): JsonResponse
    {
        $response = new ResponseService();
        $token = $request->bearerToken();

        $user = $this->jwtAuthService->authenticate($token);

        if ($this->userRepository->delete($user->id)) {
            return $response->success([]);
        }

        return $response->error(500, 'Failed to delete user');
    }
}
