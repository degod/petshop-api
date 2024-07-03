<?php

namespace App\Repositories\User;

use App\Models\User;

interface UserRepositoryInterface
{
    /**
     * Used to insert user data
     *
     * @param  array<string|mixed>  $data
     */
    public function create(array $data): User;

    public function findByEmail(string $email): ?User;

    public function findByUuid(string $uuid): ?User;

    /**
     * Edit user
     *
     * @param  array<string|mixed>  $data
     */
    public function edit(array $data): ?User;

    public function delete(int $userId): ?bool;
}
