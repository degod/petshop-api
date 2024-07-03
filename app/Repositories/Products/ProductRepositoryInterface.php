<?php

namespace App\Repositories\Products;

use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;

interface ProductRepositoryInterface
{
    public function findById(int $id): ?Product;

    public function findByUuid(string $uuid): ?Product;

    /**
     * Get all products with optional filters and pagination.
     *
     * @param  array<string, mixed>  $params
     * @return LengthAwarePaginator<Product>
     */
    public function getAllProducts(array $params): LengthAwarePaginator;

    /**
     * Create product
     *
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Product;

    public function deleteByUuid(string $uuid): ?bool;

    /**
     * Update a product with the given data.
     *
     * @param  array<string|mixed>  $data
     */
    public function update(Product $product, array $data): Product;
}
