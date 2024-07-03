<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Http\Requests\EditProductRequest;
use App\Repositories\Products\ProductRepositoryInterface;
use App\Services\ResponseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * @OA\Put(
 *     path="/api/v1/product/{uuid}",
 *     tags={"Products"},
 *     summary="Edit an existing product",
 *     security={{"bearerAuth": {}}},
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
class EditProductController extends Controller
{
    public function __construct(private ProductRepositoryInterface $productRepository, private ResponseService $responseService)
    {
    }

    public function __invoke(EditProductRequest $request, string $uuid): JsonResponse
    {
        $data = $request->validated();

        try {
            DB::beginTransaction();

            $product = $this->productRepository->findByUuid($uuid);

            if (!$product) {
                throw new ModelNotFoundException('Product not found');
            }

            $updatedProduct = $this->productRepository->update($product, $data);

            DB::commit();

            return $this->responseService->success($updatedProduct->makeHidden('id'));
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return $this->responseService->error(404, 'Product not found');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseService->error(500, 'Failed to update product');
        }
    }
}
