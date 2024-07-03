<?php

namespace App\Repositories\Promotions;

use App\Models\Promotion;
use Illuminate\Pagination\LengthAwarePaginator;

interface PromotionRepositoryInterface
{
    public function findById(int $id): ?Promotion;

    public function findByUuid(string $uuid): ?Promotion;

    /**
     * Get all promotions
     *
     * @param  array<string, mixed>  $params
     * @return LengthAwarePaginator<Promotion>
     */
    public function getPromotions(array $params): LengthAwarePaginator;
}
