<?php

namespace App\Repositories\Brands;

use App\Models\Brand;
use Illuminate\Pagination\LengthAwarePaginator;

interface BrandRepositoryInterface
{
    public function findById(int $id): ?Brand;

    public function findByUuid(string $uuid): ?Brand;

    /**
     * Used to list all brands
     *
     * @param  array<string|mixed>  $params
     * @return LengthAwarePaginator<Brand>
     */
    public function getAllBrands(array $params);

    /**
     * Used to create a brand
     *
     * @param  array<string|mixed>  $data
     */
    public function create(array $data): Brand;

    public function delete(Brand $brand): ?bool;
}
