<?php

namespace App\Repositories\Promotions;

use App\Models\Promotion;
use Illuminate\Pagination\LengthAwarePaginator;

class PromotionRepository implements PromotionRepositoryInterface
{
    public function __construct(private Promotion $promotion)
    {
    }

    public function findById($id): ?Promotion
    {
        return $this->promotion
            ->select('uuid', 'title', 'content', 'metadata', 'created_at', 'updated_at')
            ->find($id);
    }

    public function findByUuid($uuid): ?Promotion
    {
        return $this->promotion->where('uuid', $uuid)
            ->select('uuid', 'title', 'content', 'metadata', 'created_at', 'updated_at')
            ->first();
    }

    public function getPromotions(array $params): LengthAwarePaginator
    {
        $now = now();
        
        $query = $this->promotion->newQuery();

        $query->when(
            isset($params['valid']) && is_bool($params['valid']),
            function ($query) use ($params, $now) {
                if ($params['valid']) {
                    $query->whereJsonContains('metadata->valid_from', '<=', $now)
                          ->whereJsonContains('metadata->valid_to', '>=', $now);
                } else {
                    $query->where(function ($query) use ($now) {
                        $query->whereJsonContains('metadata->valid_from', '>', $now)
                              ->orWhereJsonContains('metadata->valid_to', '<', $now);
                    });
                }
            }
        );

        $query->when(
            isset($params['sortBy']) && isset($params['desc']),
            function ($query) use ($params) {
                $sortOrder = $params['desc'] ? 'desc' : 'asc';
                $query->orderBy($params['sortBy'], $sortOrder);
            }
        );

        $limit = $params['limit'] ?? 10;

        return $query->select('uuid', 'title', 'content', 'metadata', 'created_at', 'updated_at')->paginate($limit);
    }
}
