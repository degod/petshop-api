<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\ResetPasswordRequest;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\PasswordResets\PasswordResetRepositoryInterface;
use App\Services\ResponseService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Post(
 *     path="/api/v1/user/reset-password-token",
 *     tags={"User"},
 *     summary="Reset a user password with the a token",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/x-www-form-urlencoded",
 *             @OA\Schema(
 *                 required={"token", "email", "password", "password_confirmation"},
 *                 @OA\Property(property="token", type="string", description="User reset token", example=""),
 *                 @OA\Property(property="email", type="string", description="User email", example=""),
 *                 @OA\Property(property="password", type="string", description="User password", example=""),
 *                 @OA\Property(property="password_confirmation", type="string", description="User password", example="")
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
class ResetPasswordController extends Controller
{
    public function __construct(private UserRepositoryInterface $userRepository, private PasswordResetRepositoryInterface $passwordResetRepository)
    {
    }

    public function __invoke(ResetPasswordRequest $request): JsonResponse
    {
        $response = new ResponseService();
        $validated = $request->validated();

        // Find password reset record by email and token
        $passwordReset = $this->passwordResetRepository->findForEmailAndToken($validated['email'], $validated['token']);

        if (!$passwordReset) {
            return $response->error(401, 'Invalid or expired token');
        }

        // Find user by email
        $user = $this->userRepository->findByEmail($validated['email']);

        if (!$user) {
            return $response->error(404, 'User not found.');
        }

        // Update user's password
        $user->password = Hash::make($validated['password']);
        $user->save();

        // Delete the password reset record
        $deletedToken = $this->passwordResetRepository->deleteByEmailAndToken($passwordReset->email, $passwordReset->token);
        if (!$deletedToken) {
            return $response->error(422, 'Error deleting token');
        }

        return $response->success(["message" => "Password has been successfully updated"]);
    }
}
