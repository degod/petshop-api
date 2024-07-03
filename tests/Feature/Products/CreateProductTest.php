<?php

namespace Tests\Feature\Products;

use App\Models\User;
use App\Models\Category;
use App\Models\Brand;
use App\Models\File;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use Mockery;
use App\Services\JwtAuthServiceInterface;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use DateTimeImmutable;

class CreateProductTest extends TestCase
{
    use RefreshDatabase;

    public function testCreateProduct(): void
    {
        // Create a test user
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('userpassword')
        ]);

        // Create necessary related models
        $category = Category::factory()->create();
        $brand = Brand::factory()->create();
        $file = File::factory()->create();

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
            'category_uuid' => $category->uuid,
            'title' => 'New Product',
            'price' => 199.99,
            'description' => 'This is a new product',
            'metadata' => json_encode([
                'brand' => $brand->uuid,
                'image' => $file->uuid
            ])
        ];

        // Send the POST request with the Bearer token
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token->toString(),
        ])->postJson(route('products.create'), $fakeData);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'uuid',
                'category_uuid',
                'title',
                'price',
                'description',
                'metadata',
                'created_at',
                'updated_at'
            ],
            'error',
            'errors',
            'extra'
        ]);
    }
}
