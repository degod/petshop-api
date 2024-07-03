<?php

namespace Tests\Feature\Categories;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Services\JwtAuthServiceInterface;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use DateTimeImmutable;

class ViewCategoryTest extends TestCase
{
    use RefreshDatabase;

    public function testViewCategory(): void
    {
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
            ->withClaim('user_uuid', $category->uuid)
            ->getToken($config->signer(), $config->signingKey());

        // Mock the JwtAuthServiceInterface
        $jwtAuthService = $this->mock(JwtAuthServiceInterface::class);
        $jwtAuthService->shouldReceive('decodeToken')
            ->with($token->toString())
            ->andReturn($token);

        // Send the GET request with the Bearer token
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token->toString(),
        ])->get(route('categories.view', ['uuid' => $category->uuid]));

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
                'uuid' => $category->uuid,
                'title' => $category->title,
            ]
        ]);
    }
}
