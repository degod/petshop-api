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

class EditUserTest extends TestCase
{
    use RefreshDatabase;

    public function testEditUser(): void
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

        // Mocking the JwtAuthServiceInterface decodeToken method
        $jwtAuthService->shouldReceive('decodeToken')
            ->with($token->toString())
            ->andReturn($token);

        // Mocking the JwtAuthServiceInterface authenticate method
        $jwtAuthService->shouldReceive('authenticate')
            ->with($token->toString())
            ->andReturn($user);

        $fakeData = [
            'first_name' => $user->first_name.'_new',
            'last_name' => $user->last_name.'_new',
            'email' => $user->email,
            'phone_number' => $user->phone_number,
            'password' => 'userpassword', // Use a hardcoded password as it's hashed
            'password_confirmation' => 'userpassword', // Same as above
            'address' => $user->address,
            'avatar' => $user->uuid,
            'is_marketing' => 1,
        ];

        // Send the PUT request with the Bearer token
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token->toString(),
        ])->putJson(route('user.edit'), $fakeData);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'uuid',
                'first_name',
                'last_name',
                'email',
                'phone_number',
                'address',
                'created_at',
                'updated_at',
            ],
            'error',
            'errors',
            'extra',
        ]);
        $response->assertJson([
            'data' => [
                'first_name' => $user->first_name.'_new',
                'last_name' => $user->last_name.'_new',
            ],
        ]);
    }
}
