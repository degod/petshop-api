<?php

namespace Tests\Feature\Categories;

use App\Models\User;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use Mockery;
use App\Services\JwtAuthServiceInterface;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use DateTimeImmutable;

class EditCategoryTest extends TestCase
{
    use RefreshDatabase;

    public function testEditCategory(): void
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

        $fakeData = [
            'title' => 'Updated Category Title',
        ];

        // Send the PUT request with the Bearer token
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token->toString(),
        ])->putJson(route('categories.edit', ['uuid' => $category->uuid]), $fakeData);

        // Assert the response status
        $response->assertStatus(200);

        // Assert the JSON structure
        $response->assertJsonStructure([
            'success',
            'data' => [
                'uuid',
                'title',
                'slug',
                'created_at',
                'updated_at',
            ],
            'error',
            'errors',
            'extra'
        ]);

        // Assert the JSON content
        $response->assertJson([
            'data' => [
                'title' => 'Updated Category Title'
            ]
        ]);
    }
}
