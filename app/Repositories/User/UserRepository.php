<?php

namespace App\Repositories\User;

use App\Models\User;

class UserRepository implements UserRepositoryInteface
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

        $user = $this->user::create($data);

        return $user;
    }
}
