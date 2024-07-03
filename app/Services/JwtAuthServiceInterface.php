<?php

namespace App\Services;

use App\Models\User;
use Lcobucci\JWT\Token;

interface JwtAuthServiceInterface
{
    public function generateToken(User $user): ?string;

    public function decodeToken(string $token): ?Token;

    public function revokeToken(string $token): void;

    public function authenticate(string $token): ?User;
}
