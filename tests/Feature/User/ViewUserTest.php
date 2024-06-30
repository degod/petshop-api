<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use Mockery;
use App\Services\JwtAuthService;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Token\Plain;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use DateTimeImmutable;

class ViewUserTest extends TestCase
{
    use RefreshDatabase;

    public function testViewUser()
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
        ])->get(route('user.view'));

        // Assert the response status
        $response->assertStatus(200);

        // Assert the JSON structure
        $response->assertJsonStructure([
            'id',
            'first_name',
            'last_name',
            'email',
            'phone_number',
            'address',
            'avatar',
            'is_marketing',
            'created_at',
            'updated_at'
        ]);

        // Assert the JSON content
        $response->assertJson([
            'email' => 'user@example.com'
        ]);
    }
}
