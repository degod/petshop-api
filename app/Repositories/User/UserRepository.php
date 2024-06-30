<?php

namespace App\Repositories\User;

use App\Models\User;
use Illuminate\Support\Str;

class UserRepository implements UserRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct(private User $user)
    {
    }

    /**
     * Used to insert user data
     */
    public function create(array $data): User
    {
        $data['uuid'] = Str::uuid();
        $user = $this->user::create($data);

        return $user;
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function findByUuid(string $uuid): ?User
    {
        return User::where('uuid', $uuid)->first();
    }
}
