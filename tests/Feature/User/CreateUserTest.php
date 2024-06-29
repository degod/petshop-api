<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use Mockery;
use Illuminate\Support\Str;
use App\Services\JwtAuthService;
use App\Repositories\User\UserRepositoryInterface;
use Faker\Factory as Faker;

class CreateUserTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCreation()
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
        $userRepository->shouldReceive('create')->andReturnUsing(function($inputData) {
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
            'is_marketing' => null
        ];

        $response = $this->postJson(route('user.create'), $fakeData);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'user' => [
                'id',
                'first_name',
                'last_name',
                'email',
                'phone_number',
                'address',
                'created_at',
                'updated_at'
            ],
            'token'
        ]);
        $response->assertJson([
            'token' => 'mocked_jwt_token'
        ]);

        $this->assertDatabaseHas('users', [
            'email' => $fakeEmail
        ]);
    }
}
