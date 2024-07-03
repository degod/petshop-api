<?php

namespace App\Repositories\Brands;

use App\Models\Brand;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class BrandRepository implements BrandRepositoryInterface
{
    public function __construct(private Brand $brand)
    {
    }

    public function findById(int $id): ?Brand
    {
        return $this->brand->select('uuid', 'title', 'slug', 'created_at', 'updated_at')->find($id);
    }

    public function findByUuid(string $uuid): ?Brand
    {
        return $this->brand->select('uuid', 'title', 'slug', 'created_at', 'updated_at')->where('uuid', $uuid)->first();
    }

    /**
     * Used to list all brands
     *
     * @param  array<string|mixed>  $params
     * @return LengthAwarePaginator<Brand>
     */
    public function getAllBrands(array $params): LengthAwarePaginator
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

    public function delete(Brand $brand): ?bool
    {
        return $brand->delete();
    }
}
