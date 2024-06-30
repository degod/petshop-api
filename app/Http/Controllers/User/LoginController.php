<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginUser;
use App\Repositories\User\UserRepositoryInterface;
use App\Services\JwtAuthService;
use App\Services\ResponseService;
use Illuminate\Support\Facades\Hash;

/**
 * @OA\Post(
 *     path="/api/v1/user/login",
 *     summary="Login an User account",
 *     description="Login user and return JWT token",
 *     tags={"User"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/x-www-form-urlencoded",
 *             @OA\Schema(
 *                 required={"email", "password"},
 *                 @OA\Property(property="email", type="string", description="User email", example=""),
 *                 @OA\Property(property="password", type="string", description="User password", example="")
 *             )
 *         )
 *     ),
 *     @OA\Response(response=200,description="OK"),
 *     @OA\Response(response=401,description="Unauthorized"),
 *     @OA\Response(response=404,description="Page not found"),
 *     @OA\Response(response=422,description="Unprocessable Entity"),
 *     @OA\Response(response=500,description="Internal server error")
 * )
 */
class LoginController extends Controller
{
    protected $userRepository;
    protected $jwtAuthService;

    public function __construct(UserRepositoryInterface $userRepository, JwtAuthService $jwtAuthService)
    {
        $this->userRepository = $userRepository;
        $this->jwtAuthService = $jwtAuthService;
    }

    public function __invoke(LoginUser $request)
    {
        $response = new ResponseService();
        $credentials = $request->only('email', 'password');

        $user = $this->userRepository->findByEmail($credentials['email']);

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return $response->error(401, "Invalid credentials");
        }

        $token = $this->jwtAuthService->generateToken($user);

        return $response->success(['token' => $token]);
    }
}
