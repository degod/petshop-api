<?php

namespace App\Repositories\PasswordResets;

use App\Models\PasswordReset;

class PasswordResetRepository implements PasswordResetRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct(private PasswordReset $passwordReset)
    {
    }

    /**
     * Used to create a password reset token
     */
    public function create(array $data): ?PasswordReset
    {
        return $this->passwordReset::updateOrCreate(
            ['email' => $data['email']],
            ['token' => $data['token'], 'created_at' => now()]
        );
    }

    /**
     * Used to find a password reset token by email
     */
    public function findByEmail(string $email): ?PasswordReset
    {
        return $this->passwordReset::where('email', $email)->first();
    }

    /**
     * Used to delete a password reset token by email
     */
    public function deleteByEmail(string $email): bool
    {
        return $this->passwordReset::where('email', $email)->delete();
    }
}