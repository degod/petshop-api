<?php

namespace App\Http\Controllers\Categories;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCategoryRequest;
use App\Repositories\Categories\CategoryRepositoryInterface;
use App\Services\ResponseService;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Post(
 *     path="/api/v1/category/create",
 *     tags={"Categories"},
 *     summary="Create a new category",
 *     security={{"bearerAuth":{}}},
 *
 *     @OA\RequestBody(
 *         required=true,
 *
 *         @OA\MediaType(
 *             mediaType="application/x-www-form-urlencoded",
 *
 *             @OA\Schema(
 *                 required={"title"},
 *
 *                 @OA\Property(property="title", type="string", description="Category title", example="")
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(response=200, description="OK"),
 *     @OA\Response(response=401, description="Unauthorized"),
 *     @OA\Response(response=404, description="Page not found"),
 *     @OA\Response(response=422, description="Unprocessable Entity"),
 *     @OA\Response(response=500, description="Internal server error")
 * )
 */
class CreateCategoryController extends Controller
{
    public function __construct(private CategoryRepositoryInterface $categoryRepository, private ResponseService $responseService) {}

    public function __invoke(CreateCategoryRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $category = $this->categoryRepository->create($validated);

        return $this->responseService->success($category->makeHidden('id')->toArray());
    }
}
