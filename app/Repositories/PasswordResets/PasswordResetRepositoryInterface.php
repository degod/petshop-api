<?php

namespace App\Repositories\PasswordResets;

use App\Models\PasswordReset;

interface PasswordResetRepositoryInterface
{
    public function create(array $data): ?PasswordReset;

    public function findByEmail(string $email): ?PasswordReset;

    public function deleteByEmail(string $email): bool;

    public function findForEmailAndToken(string $email, string $token): ?PasswordReset;

    public function deleteByEmailAndToken(string $email, string $token): bool;
}
