<?php

namespace Tests\Feature\User;

use App\Models\User;
use App\Repositories\User\UserRepositoryInterface;
use App\Services\JwtAuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Mockery;
use Tests\TestCase;

class LoginUserTest extends TestCase
{
    use RefreshDatabase;

    public function testUserLogin(): void
    {
        // Create a test user
        $user = User::factory()->create([
            'email' => 'testuser@example.com',
            'password' => Hash::make('userpassword'), // Ensure the password is hashed
        ]);

        // Mock the UserRepositoryInterface
        $userRepository = Mockery::mock(UserRepositoryInterface::class);
        $this->app->instance(UserRepositoryInterface::class, $userRepository);

        // Mock the JwtAuthService
        $jwtAuthService = Mockery::mock(JwtAuthService::class);
        $this->app->instance(JwtAuthService::class, $jwtAuthService);

        // Mocking the repository findByEmail method
        $userRepository->shouldReceive('findByEmail')
            ->with('testuser@example.com')
            ->andReturn($user);

        // Mocking the JwtAuthService generateToken method
        $jwtAuthService->shouldReceive('generateToken')
            ->with($user)
            ->andReturn('mocked_jwt_token');

        // Test login request
        $response = $this->postJson(route('user.login'), [
            'email' => 'testuser@example.com',
            'password' => 'userpassword', // Use the plain text password
        ]);

        // Assert the response status
        $response->assertStatus(200);

        // Assert the JSON structure
        $response->assertJsonStructure([
            'success',
            'data' => [
                'token',
            ],
            'error',
            'errors',
            'extra',
        ]);

        // Assert the token
        $response->assertJson([
            'data' => [
                'token' => 'mocked_jwt_token',
            ],
        ]);
    }
}
