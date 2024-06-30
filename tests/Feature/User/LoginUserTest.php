<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use Mockery;
use App\Services\JwtAuthService;
use App\Repositories\User\UserRepositoryInterface;

class LoginUserTest extends TestCase
{
    use RefreshDatabase;

    public function testUserLogin()
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
            'token'
        ]);

        // Assert the token
        $response->assertJson([
            'token' => 'mocked_jwt_token'
        ]);
    }
}