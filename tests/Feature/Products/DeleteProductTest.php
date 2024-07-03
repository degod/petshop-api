<?php

namespace Tests\Feature\Products;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Services\JwtAuthServiceInterface;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use DateTimeImmutable;

class DeleteProductTest extends TestCase
{
    use RefreshDatabase;

    public function testDeleteProduct(): void
    {
        // Create a test product
        $product = Product::factory()->create();

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
            ->relatedTo((string) $product->id)
            ->withClaim('product_uuid', $product->uuid)
            ->getToken($config->signer(), $config->signingKey());

        // Mock the JwtAuthServiceInterface
        $jwtAuthService = $this->mock(JwtAuthServiceInterface::class);
        $this->app->instance(JwtAuthServiceInterface::class, $jwtAuthService);

        // Mocking the JwtAuthServiceInterface decodeToken method
        $jwtAuthService->shouldReceive('decodeToken')
            ->with($token->toString())
            ->andReturn($token);

        // Send the DELETE request with the Bearer token
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token->toString(),
        ])->deleteJson(route('products.delete', ['uuid' => $product->uuid]));

        // Assert response
        $response->assertStatus(200);
    }
}
