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

class EditUserTest extends TestCase
{
    use RefreshDatabase;

    public function testEditUser()
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

        $fakeData = [
            'first_name' => $user->first_name.'_new',
            'last_name' => $user->last_name.'_new',
            'email' => $user->email,
            'phone_number' => $user->phone_number,
            'password' => 'userpassword', // Use a hardcoded password as it's hashed
            'password_confirmation' => 'userpassword', // Same as above
            'address' => $user->address,
            'avatar' => $user->uuid,
            'is_marketing' => 1
        ];

        // Send the PUT request with the Bearer token
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token->toString(),
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
                'updated_at'
            ],
            'error',
            'errors',
            'extra'
        ]);
        $response->assertJson([
            'data' => [
                'first_name' => $user->first_name.'_new',
                'last_name' => $user->last_name.'_new',
            ]
        ]);
    }
}
