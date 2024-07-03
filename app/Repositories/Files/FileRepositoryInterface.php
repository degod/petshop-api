<?php

namespace App\Repositories\Files;

use App\Models\File;

interface FileRepositoryInterface
{
    public function findById(int $id): ?File;
    public function findByUuid(string $uuid): ?File;
}