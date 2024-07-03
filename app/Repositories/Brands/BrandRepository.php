<?php

namespace App\Repositories\Brands;

use App\Models\Brand;
use Illuminate\Support\Str;

class BrandRepository implements BrandRepositoryInterface
{
    public function __construct(private Brand $brand)
    {
    }

    public function findById($id): ?Brand
    {
        return $this->brand->select('uuid', 'title', 'slug', 'created_at', 'updated_at')->find($id);
    }

    public function findByUuid($uuid): ?Brand
    {
        return $this->brand->select('uuid', 'title', 'slug', 'created_at', 'updated_at')->where('uuid', $uuid)->first();
    }

    public function getAllBrands(array $params)
    {
        $query = $this->brand->newQuery();

        if (isset($params['sortBy']) && isset($params['desc'])) {
            $sortOrder = $params['desc'] ? 'desc' : 'asc';
            $query->orderBy($params['sortBy'], $sortOrder);
        }

        $limit = $params['limit'] ?? 10;
        $page = $params['page'] ?? 1;

        return $query->select('uuid', 'title', 'slug', 'created_at', 'updated_at')
                     ->paginate($limit, ['*'], 'page', $page);
    }

    public function create(array $data): Brand
    {
        $data['uuid'] = Str::uuid();
        $data['slug'] = Str::slug($data['title']);

        return $this->brand->create($data);
    }

    public function delete(Brand $brand): bool
    {
        return $brand->delete();
    }
}