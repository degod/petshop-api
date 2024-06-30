<?php

namespace App\Services;

use App\Models\JwtToken;
use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Support\Str;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Validation\Constraint\IssuedBy;
use Lcobucci\JWT\Validation\Constraint\PermittedFor;
use Lcobucci\JWT\Validation\Constraint\ValidAt;
use Lcobucci\Clock\SystemClock;

class JwtAuthService
{
    private $secretKey;
    private $config;
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
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

        $this->userRepository = $userRepository;
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

            // Validate the token
            $constraints = $this->config->validationConstraints();
            if (!$this->config->validator()->validate($parsedToken, ...$constraints)) {
                throw new \Exception('Invalid token');
            }

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

    public function authenticate($token)
    {
        $decodedToken = $this->decodeToken($token);

        if (!$decodedToken) {
            return null;
        }

        $user_uuid = $decodedToken->claims()->get('user_uuid');

        if (!$user_uuid) {
            return null;
        }

        $user = $this->userRepository->findByUuid($user_uuid);

        return $user;
    }
}
