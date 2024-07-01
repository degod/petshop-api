<?php

namespace App\Repositories\Promotions;

use App\Models\Promotion;
use Illuminate\Pagination\LengthAwarePaginator;

interface PromotionRepositoryInterface
{
    public function findById($id): ?Promotion;
    public function findByUuid($uuid): ?Promotion;
    public function getPromotions(array $params): LengthAwarePaginator;
}
