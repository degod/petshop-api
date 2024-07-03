<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateProductRequest;
use App\Repositories\Products\ProductRepositoryInterface;
use App\Services\ResponseService;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Post(
 *     path="/api/v1/product/create",
 *     tags={"Products"},
 *     summary="Create a new product",
 *     security={{"bearerAuth": {}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/x-www-form-urlencoded",
 *             @OA\Schema(
 *                 required={"category_uuid", "title", "price", "description", "metadata"},
 *                 @OA\Property(property="category_uuid", type="string", description="Category UUID", example=""),
 *                 @OA\Property(property="title", type="string", description="Product title", example=""),
 *                 @OA\Property(property="price", type="number", description="Product price", example=""),
 *                 @OA\Property(property="description", type="string", description="Product description", example=""),
 *                 @OA\Property(property="metadata", type="object", description="Product metadata",
 *                     @OA\Property(property="brand", type="string"),
 *                     @OA\Property(property="image", type="string")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(response=200, description="OK"),
 *     @OA\Response(response=401, description="Unauthorized"),
 *     @OA\Response(response=404, description="Page not found"),
 *     @OA\Response(response=422, description="Unprocessable Entity"),
 *     @OA\Response(response=500, description="Internal server error")
 * )
 */
class CreateProductController extends Controller
{
    public function __construct(private ProductRepositoryInterface $productRepository, private ResponseService $responseService)
    {
    }

    public function __invoke(CreateProductRequest $request): JsonResponse
    {
        $data = $request->validated();

        try {
            $product = $this->productRepository->create($data);

            return $this->responseService->success($product->makeHidden('id'));
        } catch (\Exception $e) {
            \Log::error($e);
            return $this->responseService->error(500, 'Failed to create product');
        }
    }
}
