<?php

namespace App\Repositories\Products;

use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;

interface ProductRepositoryInterface
{
    public function findById($id): ?Product;
    public function findByUuid($uuid): ?Product;
    public function getAllProducts(array $params): LengthAwarePaginator;
    public function create(array $data): Product;
    public function deleteByUuid(string $uuid): bool;
}