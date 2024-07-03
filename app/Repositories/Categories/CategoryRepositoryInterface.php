<?php

namespace App\Repositories\Categories;

use App\Models\Category;
use Illuminate\Pagination\LengthAwarePaginator;

interface CategoryRepositoryInterface
{
    public function findById(int $id): ?Category;

    public function findByUuid(string $uuid): ?Category;

    /**
     * Used to list all category
     *
     * @param  array<string|mixed>  $params
     * @return LengthAwarePaginator<Category>
     */
    public function getAllCategories(array $params): LengthAwarePaginator;

    /**
     * Used to create a category
     *
     * @param  array<string|mixed>  $data
     */
    public function create(array $data): Category;

    /**
     * Used to update a category
     *
     * @param  array<string|mixed>  $data
     */
    public function update(array $data, string $uuid): Category;

    public function delete(Category $category): bool;
}
