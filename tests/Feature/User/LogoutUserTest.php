<?php

namespace Tests\Feature\User;

use App\Models\User;
use App\Services\JwtAuthServiceInterface;
use DateTimeImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Mockery;
use Tests\TestCase;

class LogoutUserTest extends TestCase
{
    use RefreshDatabase;

    public function testLogoutUser(): void
    {
        // Create a test user
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('userpassword'),
        ]);

        // Create a real token
        $config = Configuration::forSymmetricSigner(
            new Sha256(),
            InMemory::plainText(env('JWT_SECRET'))
        );

        $now = new DateTimeImmutable();
        $token = $config->builder()
            ->issuedBy(env('APP_NAME'))
            ->permittedFor(env('APP_NAME'))
            ->identifiedBy('mocked_jwt_token_jti')
            ->issuedAt($now)
            ->canOnlyBeUsedAfter($now)
            ->expiresAt($now->modify('+1 hour'))
            ->relatedTo((string) $user->id)
            ->withClaim('user_uuid', $user->uuid)
            ->getToken($config->signer(), $config->signingKey());

        // Mock the JwtAuthServiceInterface
        $jwtAuthService = Mockery::mock(JwtAuthServiceInterface::class);
        $this->app->instance(JwtAuthServiceInterface::class, $jwtAuthService);

        // Mocking the JwtAuthServiceInterface revokeToken method
        $jwtAuthService->shouldReceive('revokeToken')
            ->with($token->toString())
            ->andReturn(true);

        // Mocking the JwtAuthServiceInterface decodeToken method
        $jwtAuthService->shouldReceive('decodeToken')
            ->with($token->toString())
            ->andReturn($token);

        // Mocking the JwtAuthServiceInterface authenticate method
        $jwtAuthService->shouldReceive('authenticate')
            ->with($token->toString())
            ->andReturn($user);

        // Send the GET request with the Bearer token
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token->toString(),
        ])->getJson(route('user.logout'));

        // Assert the response status
        $response->assertStatus(200);

        // Assert the JSON structure
        $response->assertJsonStructure([
            'success',
            'data',
            'error',
            'errors',
            'extra',
        ]);

        // Assert the success message
        $response->assertJson([
            'success' => 1,
            'data' => [],
            'error' => null,
            'errors' => [],
            'extra' => [],
        ]);
    }
}
