<?php

namespace Tests\Feature\Categories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use Mockery;
use App\Services\JwtAuthServiceInterface;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use DateTimeImmutable;

class DeleteCategoryTest extends TestCase
{
    use RefreshDatabase;

    public function testDeleteCategory(): void
    {
        // Create a test user
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('userpassword')
        ]);

        // Create a test category
        $category = Category::factory()->create();

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

        // Mocking the JwtAuthServiceInterface decodeToken method
        $jwtAuthService->shouldReceive('decodeToken')
            ->with($token->toString())
            ->andReturn($token);

        // Mocking the JwtAuthServiceInterface authenticate method
        $jwtAuthService->shouldReceive('authenticate')
            ->with($token->toString())
            ->andReturn($user);

        // Send the DELETE request with the Bearer token
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token->toString(),
        ])->delete(route('categories.delete', ['uuid' => $category->uuid]));

        // Assert the response status
        $response->assertStatus(200);

        // Assert the JSON structure
        $response->assertJsonStructure([
            'success',
            'data' => [
                'message'
            ],
            'error',
            'errors',
            'extra'
        ]);

        // Assert the JSON content
        $response->assertJson([
            'success' => 1,
            'error' => null,
            'errors' => [],
            'extra' => [],
            'data' => [
                'message' => 'Category deleted'
            ]
        ]);
    }
}
