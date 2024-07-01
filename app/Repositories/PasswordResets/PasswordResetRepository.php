<?php

namespace App\Repositories\PasswordResets;

use App\Models\PasswordReset;
use Carbon\Carbon;

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

    /**
     * Used to fetch token row by email and token
     */
    public function findForEmailAndToken(string $email, string $token): ?PasswordReset
    {
        return $this->passwordReset::where('email', $email)
            ->where('token', $token)
            ->where('created_at', '>', Carbon::now()->subHours(config('auth.passwords.users.expire')))
            ->first();
    }

    /**
     * Used to delete a password reset token by email and token
     */
    public function deleteByEmailAndToken(string $email, string $token): bool
    {
        return $this->passwordReset::where(['email' => $email, 'token' => $token])->delete();
    }
}
