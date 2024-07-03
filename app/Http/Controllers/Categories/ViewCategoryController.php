<?php

namespace App\Http\Controllers\Categories;

use App\Http\Controllers\Controller;
use App\Repositories\Categories\CategoryRepositoryInterface;
use App\Services\ResponseService;

/**
 * @OA\Get(
 *     path="/api/v1/category/{uuid}",
 *     tags={"Categories"},
 *     summary="Fetch a category",
 *     @OA\Parameter(
 *         name="uuid",
 *         in="path",
 *         required=true,
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *     @OA\Response(response=200,description="OK"),
 *     @OA\Response(response=401,description="Unauthorized"),
 *     @OA\Response(response=404,description="Category not found"),
 *     @OA\Response(response=422,description="Unprocessable Entity"),
 *     @OA\Response(response=500,description="Internal server error")
 * )
 */
class ViewCategoryController extends Controller
{
    protected $categoryRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function __invoke($uuid)
    {
        $response = new ResponseService();

        try {
            $category = $this->categoryRepository->findByUuid($uuid);

            if (!$category) {
                return $response->error(404, 'Category not found');
            }

            return $response->success($category);
        } catch (\Exception $e) {
            return $response->error(500, 'Failed to fetch category');
        }
    }
}
