<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\ForgotPasswordRequest;
use App\Repositories\PasswordResets\PasswordResetRepositoryInterface;
use App\Services\ResponseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

/**
 * @OA\Post(
 *     path="/api/v1/user/forgot-password",
 *     tags={"User"},
 *     summary="Creates a token to reset a user password",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/x-www-form-urlencoded",
 *             @OA\Schema(
 *                 required={"email"},
 *                 @OA\Property(property="email", type="string", description="User email", example=""),
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
class ForgotPasswordController extends Controller
{
    public function __construct(private PasswordResetRepositoryInterface $passwordResetRepository)
    {
    }

    public function __invoke(ForgotPasswordRequest $request): JsonResponse
    {
        $response = new ResponseService();
        $token = Str::random(128);

        // Validate and create a password reset token
        $this->passwordResetRepository->create([
            'email' => $request->email,
            'token' => $token,
        ]);

        return $response->success(['reset_token' => $token]);
    }
}
