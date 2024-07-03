<?php

namespace App\Repositories\Brands;

use App\Models\Brand;

interface BrandRepositoryInterface
{
    public function findById($id): ?Brand;
    public function findByUuid($uuid): ?Brand;
    public function getAllBrands(array $params);
    public function create(array $data): Brand;
    public function delete(Brand $brand): bool;
}