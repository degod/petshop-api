<?php

namespace App\Repositories\PasswordResets;

use App\Models\PasswordReset;

interface PasswordResetRepositoryInterface
{
    public function create(array $data): ?PasswordReset;

    public function findByEmail(string $email): ?PasswordReset;

    public function deleteByEmail(string $email): bool;
}