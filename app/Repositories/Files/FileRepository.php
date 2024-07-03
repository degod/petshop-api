<?php

namespace App\Repositories\Files;

use App\Models\File;

class FileRepository implements FileRepositoryInterface
{
    public function __construct(private File $file) {}

    public function findById(int $id): ?File
    {
        return $this->file->find($id);
    }

    public function findByUuid(string $uuid): ?File
    {
        return $this->file->where('uuid', $uuid)->first();
    }
}
