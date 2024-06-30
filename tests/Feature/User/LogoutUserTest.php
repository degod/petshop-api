<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use Mockery;
use App\Services\JwtAuthService;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use DateTimeImmutable;

class LogoutUserTest extends TestCase
{
    use RefreshDatabase;

    public function testLogoutUser()
    {
        // Create a test user
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('userpassword')
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
            ->identifiedBy('mocked_jwt_token_jti', true)
            ->issuedAt($now)
            ->canOnlyBeUsedAfter($now)
            ->expiresAt($now->modify('+1 hour'))
            ->relatedTo($user->id)
            ->withClaim('user_uuid', $user->uuid)
            ->getToken($config->signer(), $config->signingKey());

        // Mock the JwtAuthService
        $jwtAuthService = Mockery::mock(JwtAuthService::class);
        $this->app->instance(JwtAuthService::class, $jwtAuthService);

        // Mocking the JwtAuthService revokeToken method
        $jwtAuthService->shouldReceive('revokeToken')
            ->with($token->toString())
            ->andReturn(true);

        // Mocking the JwtAuthService decodeToken method
        $jwtAuthService->shouldReceive('decodeToken')
            ->with($token->toString())
            ->andReturn($token);

        // Mocking the JwtAuthService authenticate method
        $jwtAuthService->shouldReceive('authenticate')
            ->with($token->toString())
            ->andReturn($user);

        // Send the GET request with the Bearer token
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token->toString(),
        ])->getJson(route('user.logout'));

        // Assert the response status
        $response->assertStatus(200);

        // Assert the JSON structure
        $response->assertJsonStructure([
            'success',
            'data',
            'error',
            'errors',
            'extra'
        ]);

        // Assert the success message
        $response->assertJson([
            'success' => 1,
            'data' => [],
            'error' => null,
            'errors' => [],
            'extra' => []
        ]);
    }
}
