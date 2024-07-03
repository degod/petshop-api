<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Repositories\Products\ProductRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Get(
 *     path="/api/v1/products",
 *     tags={"Products"},
 *     summary="List all products",
 *
 *     @OA\MediaType(mediaType="application/x-www-form-urlencoded"),
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
 *     @OA\Parameter(
 *         name="category",
 *         in="query",
 *         required=false,
 *
 *         @OA\Schema(type="string")
 *     ),
 *
 *     @OA\Parameter(
 *         name="price",
 *         in="query",
 *         required=false,
 *
 *         @OA\Schema(type="integer")
 *     ),
 *
 *     @OA\Parameter(
 *         name="brand",
 *         in="query",
 *         required=false,
 *
 *         @OA\Schema(type="string")
 *     ),
 *
 *     @OA\Parameter(
 *         name="title",
 *         in="query",
 *         required=false,
 *
 *         @OA\Schema(type="string")
 *     ),
 *
 *     @OA\Response(response=200,description="OK"),
 *     @OA\Response(response=401,description="Unauthorized"),
 *     @OA\Response(response=404,description="Page not found"),
 *     @OA\Response(response=422,description="Unprocessable Entity"),
 *     @OA\Response(response=500,description="Internal server error")
 * )
 */
class ListProductController extends Controller
{
    public function __construct(private ProductRepositoryInterface $productRepository) {}

    public function __invoke(Request $request): JsonResponse
    {
        $params = $request->all();
        $products = $this->productRepository->getAllProducts($params);

        return response()->json($products);
    }
}
