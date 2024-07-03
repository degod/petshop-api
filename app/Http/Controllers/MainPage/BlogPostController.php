<?php

namespace App\Http\Controllers\MainPage;

use App\Http\Controllers\Controller;
use App\Repositories\Posts\PostRepositoryInterface;
use App\Services\ResponseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Get(
 *     path="/api/v1/main/blog/{uuid}",
 *     tags={"MainPage"},
 *     summary="Fetch a post",
 *
 *     @OA\Parameter(
 *         name="uuid",
 *         in="path",
 *         required=true,
 *
 *         @OA\Schema(type="string")
 *     ),
 *
 *     @OA\Response(response=200,description="OK"),
 *     @OA\Response(response=401,description="Unauthorized"),
 *     @OA\Response(response=404,description="Post not found"),
 *     @OA\Response(response=422,description="Unprocessable Entity"),
 *     @OA\Response(response=500,description="Internal server error")
 * )
 */
class BlogPostController extends Controller
{
    public function __construct(private PostRepositoryInterface $postRepository) {}

    public function __invoke(Request $request, string $uuid): JsonResponse
    {
        $response = new ResponseService();
        $post = $this->postRepository->findByUuid($uuid);

        if (! $post) {
            return $response->error(404, 'Post not found');
        }

        return $response->success($post->makeHidden('id'));
    }
}
