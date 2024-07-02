<?php

namespace App\Http\Controllers\MainPage;

use App\Http\Controllers\Controller;
use App\Repositories\Posts\PostRepositoryInterface;
use Illuminate\Http\Request;

/**
 * @OA\Get(
 *     path="/api/v1/main/blog",
 *     tags={"MainPage"},
 *     summary="List all posts",
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
 *     @OA\Response(response=200,description="OK"),
 *     @OA\Response(response=401,description="Unauthorized"),
 *     @OA\Response(response=404,description="Page not found"),
 *     @OA\Response(response=422,description="Unprocessable Entity"),
 *     @OA\Response(response=500,description="Internal server error")
 * )
 */
class BlogPostsController extends Controller
{
    private PostRepositoryInterface $postRepository;

    public function __construct(PostRepositoryInterface $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    public function __invoke(Request $request)
    {
        $params = $request->only(['page', 'limit', 'sortBy', 'desc']);
        $posts = $this->postRepository->getPosts($params);

        return response()->json($posts);
    }
}
