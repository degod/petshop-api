<?php

namespace App\Services;

use App\Models\JwtToken;
use App\Models\User;
use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Support\Str;
use Lcobucci\Clock\SystemClock;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\Validation\Constraint\IssuedBy;
use Lcobucci\JWT\Validation\Constraint\PermittedFor;
use Lcobucci\JWT\Validation\Constraint\ValidAt;

class JwtAuthService implements JwtAuthServiceInterface
{
    private string $secretKey;

    private Configuration $config;

    public function __construct(private UserRepositoryInterface $userRepository)
    {
        $this->secretKey = env('JWT_SECRET');
        $this->config = Configuration::forSymmetricSigner(
            new Sha256(),
            InMemory::plainText($this->secretKey)
        );

        // Add validation constraints
        $this->config->setValidationConstraints(
            new IssuedBy(env('APP_NAME')),
            new PermittedFor(env('APP_NAME')),
            new ValidAt(SystemClock::fromUTC())
        );
    }

    public function generateToken(User $user): ?string
    {
        $uniqueId = Str::uuid();
        $now = new \DateTimeImmutable();

        $token = $this->config->builder()
            ->issuedBy(env('APP_NAME'))
            ->permittedFor(env('APP_NAME'))
            ->identifiedBy($uniqueId)
            ->issuedAt($now)
            ->canOnlyBeUsedAfter($now)
            ->expiresAt($now->modify('+1 hour'))
            ->relatedTo((string) $user->id)
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

    public function decodeToken(string $token): ?Token
    {
        try {
            $parsedToken = $this->config->parser()->parse($token);

            // Validate the token
            $constraints = $this->config->validationConstraints();
            if (! $this->config->validator()->validate($parsedToken, ...$constraints)) {
                throw new \Exception('Invalid token');
            }

            return $parsedToken;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function revokeToken(string $token): void
    {
        $decoded = $this->decodeToken($token);

        if ($decoded) {
            JwtToken::where('unique_id', $decoded->claims()->get('jti'))->delete();
        }
    }

    public function authenticate(string $token): ?User
    {
        $decodedToken = $this->decodeToken($token);

        if (! $decodedToken) {
            return null;
        }

        $user_uuid = $decodedToken->claims()->get('user_uuid');
        if (! $user_uuid) {
            return null;
        }

        $user = $this->userRepository->findByUuid($user_uuid);

        return $user;
    }
}
