<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\EditUser;
use App\Repositories\User\UserRepositoryInterface;
use App\Services\JwtAuthService;
use App\Services\ResponseService;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Put(
 *     path="/api/v1/user/edit",
 *     tags={"User"},
 *     summary="Update a User account",
 *     security={{"bearerAuth":{}}},
 *
 *     @OA\RequestBody(
 *         required=true,
 *
 *         @OA\MediaType(
 *             mediaType="application/x-www-form-urlencoded",
 *
 *             @OA\Schema(
 *                 required={"first_name", "last_name", "email", "password", "password_confirmation", "phone_number", "address"},
 *
 *                 @OA\Property(property="first_name", type="string", description="User firstname", example=""),
 *                 @OA\Property(property="last_name", type="string", description="User lastname", example=""),
 *                 @OA\Property(property="email", type="string", description="User email", example=""),
 *                 @OA\Property(property="password", type="string", description="User password", example=""),
 *                 @OA\Property(property="password_confirmation", type="string", description="User password", example=""),
 *                 @OA\Property(property="avatar", type="string", description="Avatar image UUID", example=""),
 *                 @OA\Property(property="address", type="string",description="User main address", example=""),
 *                 @OA\Property(property="phone_number", type="string", description="User main phone number", example=""),
 *                 @OA\Property(property="is_marketing", type="string", description="User marketing preferences", example="")
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(response=200,description="OK"),
 *     @OA\Response(response=401,description="Unauthorized"),
 *     @OA\Response(response=404,description="Page not found"),
 *     @OA\Response(response=422,description="Unprocessable Entity"),
 *     @OA\Response(response=500,description="Internal server error")
 * )
 */
class EditController extends Controller
{
    public function __construct(private UserRepositoryInterface $userRepository, private JwtAuthService $jwtAuthService) {}

    public function __invoke(EditUser $request): JsonResponse
    {
        $validated = $request->validated();

        $response = new ResponseService();
        $token = $request->bearerToken();

        if ($token) {
            $userData = $this->jwtAuthService->authenticate($token);
            $validated['uuid'] = $userData ? $userData->uuid : null;

            $user = $this->userRepository->edit($validated);
            unset($user['id']);
            unset($user['is_admin']);

            if ($user) {
                return $response->success($user);
            } else {
                return $response->error(401, 'Unauthorized');
            }
        } else {
            return $response->error(401, 'Unauthorized');
        }
    }
}
