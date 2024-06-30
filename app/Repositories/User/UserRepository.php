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

    /**
     * Used to find user by email
     */
    public function findByEmail(string $email): ?User
    {
        return $this->user::where('email', $email)->first();
    }

    /**
     * Used to find user by uuid
     */
    public function findByUuid(string $uuid): ?User
    {
        return $this->user::where('uuid', $uuid)->first();
    }

    /**
     * Used to edit a user details
     */
    public function edit(array $data): ?User
    {
        $user = $this->user::whereUuid($data['uuid'])->first();
        $user->update($data);

        return $user;
    }

    /**
     * Used to delete a user details
     */
    public function delete(int $userId): ?bool
    {
        $user = $this->user::find($userId);
        if (!$user) return false;

        return $user->delete();
    }
}
