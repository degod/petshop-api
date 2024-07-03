<?php

namespace App\Http\Controllers\Categories;

use App\Http\Controllers\Controller;
use App\Repositories\Categories\CategoryRepositoryInterface;
use App\Services\ResponseService;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Get(
 *     path="/api/v1/category/{uuid}",
 *     tags={"Categories"},
 *     summary="Fetch a category",
 *
 *     @OA\Parameter(
 *         name="uuid",
 *         in="path",
 *         required=true,
 *
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *
 *     @OA\Response(response=200,description="OK"),
 *     @OA\Response(response=401,description="Unauthorized"),
 *     @OA\Response(response=404,description="Category not found"),
 *     @OA\Response(response=422,description="Unprocessable Entity"),
 *     @OA\Response(response=500,description="Internal server error")
 * )
 */
class ViewCategoryController extends Controller
{
    public function __construct(private CategoryRepositoryInterface $categoryRepository) {}

    public function __invoke(string $uuid): JsonResponse
    {
        $response = new ResponseService();

        try {
            $category = $this->categoryRepository->findByUuid($uuid);

            if (! $category) {
                return $response->error(404, 'Category not found');
            }

            return $response->success($category->makeHidden('id'));
        } catch (\Exception $e) {
            return $response->error(500, 'Failed to fetch category');
        }
    }
}
