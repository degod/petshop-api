<?php

namespace App\Http\Controllers\Categories;

use App\Http\Controllers\Controller;
use App\Http\Requests\EditCategoryRequest;
use App\Repositories\Categories\CategoryRepositoryInterface;
use App\Services\ResponseService;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Put(
 *     path="/api/v1/category/{uuid}",
 *     tags={"Categories"},
 *     summary="Update an existing category",
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="uuid",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/x-www-form-urlencoded",
 *             @OA\Schema(
 *                 required={"title"},
 *                 @OA\Property(property="title", type="string", description="Category title", example="")
 *             )
 *         )
 *     ),
 *     @OA\Response(response=200,description="OK"),
 *     @OA\Response(response=401,description="Unauthorized"),
 *     @OA\Response(response=404,description="Category not found"),
 *     @OA\Response(response=422,description="Unprocessable Entity"),
 *     @OA\Response(response=500,description="Internal server error")
 * )
 */
class EditCategoryController extends Controller
{
    public function __construct(private CategoryRepositoryInterface $categoryRepository)
    {
    }

    public function __invoke(string $uuid, EditCategoryRequest $request): JsonResponse
    {
        $response = new ResponseService();
        $validated = $request->validated();

        // Find the category by UUID
        $category = $this->categoryRepository->findByUuid($uuid);

        if (!$category) {
            return $response->error(404, 'Category not found');
        }

        // Update category data
        $categoryData = [
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']),
        ];

        $updatedCategory = $this->categoryRepository->update($categoryData, $uuid);

        return $response->success($updatedCategory->makeHidden('id'));
    }
}
