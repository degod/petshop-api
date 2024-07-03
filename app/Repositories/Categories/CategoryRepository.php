<?php

namespace App\Repositories\Categories;

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Pagination\LengthAwarePaginator;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function __construct(private Category $category)
    {
    }

    public function findById(int $id): ?Category
    {
        return $this->category->select('uuid', 'title', 'slug', 'created_at', 'updated_at')->find($id);
    }

    public function findByUuid(string $uuid): ?Category
    {
        return $this->category->select('uuid', 'title', 'slug', 'created_at', 'updated_at')->where('uuid', $uuid)->first();
    }

    /**
     * Used to list all category
     * 
     * @param  array<string|mixed> $params
     * @return LengthAwarePaginator<Category>
     */
    public function getAllCategories(array $params): LengthAwarePaginator
    {
        $query = $this->category->newQuery();

        if (isset($params['sortBy']) && isset($params['desc'])) {
            $sortOrder = $params['desc'] ? 'desc' : 'asc';
            $query->orderBy($params['sortBy'], $sortOrder);
        }

        $limit = $params['limit'] ?? 10;
        $page = $params['page'] ?? 1;

        return $query->select('uuid', 'title', 'slug', 'created_at', 'updated_at')
                     ->paginate($limit, ['*'], 'page', $page);
    }

    /**
     * Used to create a category
     * 
     * @param  array<string|mixed> $data
     * @return Category
     */
    public function create(array $data): Category
    {
        $data['uuid'] = Str::uuid();
        $data['slug'] = Str::slug($data['title']);

        return $this->category->create($data);
    }

    /**
     * Used to update a category
     * 
     * @param  array<string|mixed> $data
     * @param  string $uuid
     * @return Category
     */
    public function update(array $data, string $uuid): Category
    {
        $category = $this->findByUuid($uuid);

        if (!$category) {
            throw new \Exception("Category not found", 404);
        }

        $data['slug'] = Str::slug($data['title']);
        $category->update($data);

        return $category;
    }

    public function delete(Category $category): bool
    {
        return $this->category->whereId($category->id)->delete();
    }
}