<?php

namespace App\Http\Controllers\Categories;

use App\Http\Controllers\Controller;
use App\Repositories\Categories\CategoryRepositoryInterface;
use App\Services\ResponseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Get(
 *     path="/api/v1/categories",
 *     tags={"Categories"},
 *     summary="List all categories",
 *
 *     @OA\Parameter(
 *         name="page",
 *         in="query",
 *         required=false,
 *
 *         @OA\Schema(type="integer")
 *     ),
 *
 *     @OA\Parameter(
 *         name="limit",
 *         in="query",
 *         required=false,
 *
 *         @OA\Schema(type="integer")
 *     ),
 *
 *     @OA\Parameter(
 *         name="sortBy",
 *         in="query",
 *         required=false,
 *
 *         @OA\Schema(type="string")
 *     ),
 *
 *     @OA\Parameter(
 *         name="desc",
 *         in="query",
 *         required=false,
 *
 *         @OA\Schema(type="boolean")
 *     ),
 *
 *     @OA\Response(response=200,description="OK"),
 *     @OA\Response(response=401,description="Unauthorized"),
 *     @OA\Response(response=404,description="Category not found"),
 *     @OA\Response(response=422,description="Unprocessable Entity"),
 *     @OA\Response(response=500,description="Internal server error")
 * )
 */
class ListCategoryController extends Controller
{
    public function __construct(private CategoryRepositoryInterface $categoryRepository) {}

    public function __invoke(Request $request): JsonResponse
    {
        $response = new ResponseService();

        try {
            $params = $request->only(['page', 'limit', 'sortBy', 'desc']);
            $categories = $this->categoryRepository->getAllCategories($params);

            return response()->json($categories->toArray());
        } catch (\Exception $e) {
            return $response->error(500, 'Failed to fetch categories');
        }
    }
}
