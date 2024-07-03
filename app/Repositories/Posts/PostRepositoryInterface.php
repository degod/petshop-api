<?php

namespace App\Repositories\Posts;

use App\Models\Post;
use Illuminate\Pagination\LengthAwarePaginator;

interface PostRepositoryInterface
{
    public function findById(int $id): ?Post;

    public function findByUuid(string $uuid): ?Post;

    /**
     * Get all post
     *
     * @param  array<string|mixed>  $params
     * @return LengthAwarePaginator<Post>
     */
    public function getPosts(array $params): LengthAwarePaginator;
}
