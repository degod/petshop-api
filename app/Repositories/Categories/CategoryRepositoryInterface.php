<?php

namespace App\Repositories\Categories;

use App\Models\Category;

interface CategoryRepositoryInterface
{
    public function findById($id): ?Category;

    public function findByUuid($uuid): ?Category;

    public function getAllCategories(array $params);
    
    public function create(array $data): Category;

    public function update(array $data, string $uuid): Category;

    public function delete(Category $category): bool;
}