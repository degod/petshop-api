<?php

namespace App\Repositories\User;

use App\Models\User;

interface UserRepositoryInterface
{
    public function create(array $data): User;

    public function findByEmail(string $email): ?User;

    public function findByUuid(string $uuid): ?User;

    public function edit(array $data): ?User;
}
