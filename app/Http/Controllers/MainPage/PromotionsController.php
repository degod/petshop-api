<?php

namespace App\Http\Controllers\MainPage;

use App\Http\Controllers\Controller;
use App\Repositories\Promotions\PromotionRepositoryInterface;
use Illuminate\Http\Request;

/**
 * @OA\Get(
 *     path="/api/v1/main/promotions",
 *     tags={"MainPage"},
 *     summary="List all promotions",
 *     @OA\Parameter(
 *         name="page",
 *         in="query",
 *         required=false,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Parameter(
 *         name="limit",
 *         in="query",
 *         required=false,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Parameter(
 *         name="sortBy",
 *         in="query",
 *         required=false,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *         name="desc",
 *         in="query",
 *         required=false,
 *         @OA\Schema(type="boolean")
 *     ),
 *     @OA\Parameter(
 *         name="valid",
 *         in="query",
 *         required=false,
 *         @OA\Schema(type="boolean")
 *     ),
 *     @OA\Response(response=200,description="OK"),
 *     @OA\Response(response=401,description="Unauthorized"),
 *     @OA\Response(response=404,description="Page not found"),
 *     @OA\Response(response=422,description="Unprocessable Entity"),
 *     @OA\Response(response=500,description="Internal server error")
 * )
 */
class PromotionsController extends Controller
{
    private PromotionRepositoryInterface $promotionRepository;

    public function __construct(PromotionRepositoryInterface $promotionRepository)
    {
        $this->promotionRepository = $promotionRepository;
    }

    public function __invoke(Request $request)
    {
        $params = $request->only(['page', 'limit', 'sortBy', 'desc', 'valid']);
        $promotions = $this->promotionRepository->getPromotions($params);

        return response()->json($promotions);
    }
}
