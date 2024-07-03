<?php

namespace App\Repositories\Posts;

use App\Models\Post;
use Illuminate\Pagination\LengthAwarePaginator;

class PostRepository implements PostRepositoryInterface
{
    public function __construct(private Post $post)
    {
    }

    public function findById(int $id): ?Post
    {
        return $this->post
            ->select('uuid', 'title', 'slug', 'content', 'metadata', 'created_at', 'updated_at')
            ->find($id);
    }

    public function findByUuid(string $uuid): ?Post
    {
        return $this->post->where('uuid', $uuid)
            ->select('uuid', 'title', 'slug', 'content', 'metadata', 'created_at', 'updated_at')
            ->first();
    }

    /**
     * Get all post
     * 
     * @param  array<string|mixed> $params
     * @return LengthAwarePaginator<Post>
     */
    public function getPosts(array $params): LengthAwarePaginator
    {
        $query = $this->post->newQuery();

        // Sorting
        if (isset($params['sortBy'])) {
            $sortOrder = $params['desc'] ? 'desc' : 'asc';
            $query->orderBy($params['sortBy'], $sortOrder);
        } else {
            $query->latest(); // Default sorting by latest
        }

        // Pagination
        $perPage = $params['limit'] ?? 10;
        $page = $params['page'] ?? 1;
        $offset = ($page - 1) * $perPage;

        $posts = $query->select('uuid', 'title', 'slug', 'content', 'metadata', 'created_at', 'updated_at')
                ->offset($offset)->limit($perPage)->get();

        return new LengthAwarePaginator(
            $posts,
            $query->count(),
            $perPage,
            $page,
            ['path' => url()->current()]
        );
    }
}
