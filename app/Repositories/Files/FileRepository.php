<?php

namespace App\Repositories\Files;

use App\Models\File;

class FileRepository implements FileRepositoryInterface
{
    public function findById($id): ?File
    {
        return File::find($id);
    }

    public function findByUuid($uuid): ?File
    {
        return File::where('uuid', $uuid)->first();
    }
}