<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Repositories\User\UserRepositoryInterface;
use App\Services\JwtAuthService;
use App\Services\ResponseService;
use Illuminate\Http\Request;

/**
 * @OA\Get(
 *     path="/api/v1/user",
 *     summary="View a User account",
 *     tags={"User"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(response=200,description="OK"),
 *     @OA\Response(response=401,description="Unauthorized"),
 *     @OA\Response(response=404,description="Page not found"),
 *     @OA\Response(response=422,description="Unprocessable Entity"),
 *     @OA\Response(response=500,description="Internal server error")
 * )
 */
class ViewController extends Controller
{
    protected $userRepository;
    protected $jwtAuthService;

    public function __construct(UserRepositoryInterface $userRepository, JwtAuthService $jwtAuthService)
    {
        $this->userRepository = $userRepository;
        $this->jwtAuthService = $jwtAuthService;
    }

    public function __invoke(Request $request)
    {
        $response = new ResponseService();
        $token = $request->bearerToken();

        $user = $this->jwtAuthService->authenticate($token);

        if (!$user) {
            return $response->error(401, "Unauthorized");
        }
        unset($user['id']);
        unset($user['is_admin']);

        return $response->success($user);
    }
}
