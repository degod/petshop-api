<?php

namespace Tests\Feature\User;

use App\Models\User;
use App\Repositories\User\UserRepositoryInterface;
use App\Services\JwtAuthService;
use Faker\Factory as Faker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class CreateUserTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCreation(): void
    {
        // Instantiate Faker
        $faker = Faker::create();

        // Mock the UserRepositoryInterface
        $userRepository = Mockery::mock(UserRepositoryInterface::class);
        $this->app->instance(UserRepositoryInterface::class, $userRepository);

        // Mock the JwtAuthService
        $jwtAuthService = Mockery::mock(JwtAuthService::class);
        $this->app->instance(JwtAuthService::class, $jwtAuthService);

        // Mocking the repository create method
        $userRepository->shouldReceive('create')->andReturnUsing(function ($inputData) {
            $inputData['uuid'] = Str::uuid();

            return User::create($inputData);
        });

        // Mocking the JwtAuthService generateToken method
        $jwtAuthService->shouldReceive('generateToken')->andReturn('mocked_jwt_token');

        $fakeEmail = strtolower($faker->firstName).'@gmail.com';
        $fakeData = [
            'first_name' => $faker->firstName,
            'last_name' => $faker->lastName,
            'email' => $fakeEmail,
            'password' => 'password', // Use a hardcoded password as it's hashed
            'password_confirmation' => 'password', // Same as above
            'phone_number' => $faker->phoneNumber,
            'address' => $faker->address,
            'avatar' => $faker->optional()->uuid,
            'is_marketing' => null,
        ];

        $response = $this->postJson(route('user.create'), $fakeData);

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
                'token',
            ],
            'error',
            'errors',
            'extra',
        ]);
        $response->assertJson([
            'data' => [
                'token' => 'mocked_jwt_token',
            ],
        ]);

        $this->assertDatabaseHas('users', [
            'email' => $fakeEmail,
        ]);
    }
}
