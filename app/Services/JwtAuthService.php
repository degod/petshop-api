<?php

namespace App\Services;

use App\Models\JwtToken;
use Illuminate\Support\Str;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Token\Plain;

class JwtAuthService
{
    private $secretKey;
    private $config;

    public function __construct()
    {
        $this->secretKey = env('JWT_SECRET');
        $this->config = Configuration::forSymmetricSigner(
            new Sha256(),
            InMemory::plainText($this->secretKey)
        );
    }

    public function generateToken($user)
    {
        $uniqueId = Str::uuid();
        $now = new \DateTimeImmutable();

        $token = $this->config->builder()
            ->issuedBy(env('APP_NAME'))
            ->permittedFor(env('APP_NAME'))
            ->identifiedBy($uniqueId, true)
            ->issuedAt($now)
            ->canOnlyBeUsedAfter($now)
            ->expiresAt($now->modify('+1 hour'))
            ->relatedTo($user->id)
            ->withClaim('user_uuid', $user->uuid)
            ->getToken($this->config->signer(), $this->config->signingKey());

        JwtToken::create([
            'user_id' => $user->id,
            'unique_id' => $uniqueId,
            'token_title' => 'user_token',
            'expires_at' => now()->addHour(),
        ]);

        return $token->toString();
    }

    public function decodeToken($token)
    {
        try {
            $parsedToken = $this->config->parser()->parse($token);
            $constraints = $this->config->validationConstraints();
            $this->config->validator()->assert($parsedToken, ...$constraints);

            $storedToken = JwtToken::where('unique_id', $parsedToken->claims()->get('jti'))->first();

            if (!$storedToken) {
                return null;
            }

            return $parsedToken;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function revokeToken($token)
    {
        $decoded = $this->decodeToken($token);

        if ($decoded) {
            JwtToken::where('unique_id', $decoded->claims()->get('jti'))->delete();
        }
    }
}
