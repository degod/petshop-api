<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUser;
use App\Repositories\User\UserRepositoryInterface;
use App\Services\JwtAuthService;
use App\Services\ResponseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

/**
 * @OA\Post(
 *     path="/api/v1/user/create",
 *     summary="Create a User account",
 *     tags={"User"},
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
class CreateController extends Controller
{
    public function __construct(private UserRepositoryInterface $userRepository, private JwtAuthService $jwtAuthService)
    {
    }

    public function __invoke(StoreUser $request): JsonResponse
    {
        $response = new ResponseService();
        $validated = $request->validated();

        $inputData = [
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone_number' => $validated['phone_number'],
            'address' => $validated['address'],
        ];
        if (! empty($request['avatar'])) {
            $inputData['avatar'] = $request['avatar'];
        }
        if (! empty($request['is_marketing'])) {
            $inputData['is_marketing'] = $request['is_marketing'];
        }

        $user = $this->userRepository->create($inputData);

        $token = $this->jwtAuthService->generateToken($user);
        $user['token'] = $token;

        return $response->success($user);
    }
}
