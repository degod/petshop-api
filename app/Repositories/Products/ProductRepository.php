<?php

namespace App\Repositories\Products;

use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class ProductRepository implements ProductRepositoryInterface
{
    public function __construct(private Product $product) {}

    public function findById(int $id): ?Product
    {
        return $this->product->find($id);
    }

    public function findByUuid(string $uuid): ?Product
    {
        return $this->product->where('uuid', $uuid)->first();
    }

    /**
     * Get all products with optional filters and pagination.
     *
     * @param  array<string, mixed>  $params
     * @return LengthAwarePaginator<Product>
     */
    public function getAllProducts(array $params): LengthAwarePaginator
    {
        $query = $this->product->newQuery();

        // Exclude the 'id' column from the selection
        $selectColumns = array_diff($this->product->getFillable(), ['id']);

        $query->select($selectColumns);

        if (isset($params['category'])) {
            $query->whereHas('category', function ($q) use ($params) {
                $q->where('uuid', $params['category']);
            });
        }

        if (isset($params['brand'])) {
            $query->where('metadata->brand', $params['brand']);
        }

        if (isset($params['title'])) {
            $query->where('title', 'like', '%'.$params['title'].'%');
        }

        if (isset($params['price'])) {
            $query->where('price', $params['price']);
        }

        if (isset($params['sortBy']) && isset($params['desc'])) {
            $sortOrder = $params['desc'] ? 'desc' : 'asc';
            $query->orderBy($params['sortBy'], $sortOrder);
        }

        $limit = $params['limit'] ?? 10;
        $page = $params['page'] ?? 1;

        return $query->paginate($limit, ['*'], 'page', $page);
    }

    /**
     * Create product
     *
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Product
    {
        $data['uuid'] = (string) Str::uuid();

        return $this->product->create($data);
    }

    public function deleteByUuid(string $uuid): ?bool
    {
        $product = $this->findByUuid($uuid);

        if (! $product) {
            return false;
        }

        return $product->delete();
    }

    /**
     * Update a product with the given data.
     *
     * @param  array<string|mixed>  $data
     */
    public function update(Product $product, array $data): Product
    {
        $product->update($data);

        return $product;
    }
}
