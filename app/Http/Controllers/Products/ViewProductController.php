<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Repositories\Products\ProductRepositoryInterface;
use App\Services\ResponseService;
use Illuminate\Http\Request;

/**
 * @OA\Get(
 *     path="/api/v1/product/{uuid}",
 *     tags={"Products"},
 *     summary="Fetch a product",
 *     @OA\Parameter(
 *         name="uuid",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(response=200,description="OK"),
 *     @OA\Response(response=401,description="Unauthorized"),
 *     @OA\Response(response=404,description="Page not found"),
 *     @OA\Response(response=422,description="Unprocessable Entity"),
 *     @OA\Response(response=500,description="Internal server error"),
 *     @OA\MediaType(mediaType="application/x-www-form-urlencoded")
 * )
 */
class ViewProductController extends Controller
{
    private $productRepository;
    private $responseService;

    public function __construct(ProductRepositoryInterface $productRepository, ResponseService $responseService)
    {
        $this->productRepository = $productRepository;
        $this->responseService = $responseService;
    }

    public function __invoke(string $uuid)
    {
        $product = $this->productRepository->findByUuid($uuid);

        if (!$product) {
            return $this->responseService->error(404, 'Product not found');
        }

        return $this->responseService->success($product->makeHidden('id'));
    }
}
