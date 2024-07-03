<?php

namespace Tests\Feature\User;

use App\Models\PasswordReset;
use App\Models\User;
use App\Repositories\PasswordResets\PasswordResetRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Mockery;
use Tests\TestCase;

class ResetPasswordTest extends TestCase
{
    use RefreshDatabase;

    public function testResetPassword(): void
    {
        // Create a test user
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('oldpassword'),
        ]);

        // Create a PasswordReset entry
        $passwordReset = PasswordReset::factory()->create([
            'email' => $user->email,
            'token' => Hash::make('reset_token'),
        ]);

        // Mock the PasswordResetRepositoryInterface
        $passwordResetRepository = Mockery::mock(PasswordResetRepositoryInterface::class);
        $this->app->instance(PasswordResetRepositoryInterface::class, $passwordResetRepository);

        // Mocking the findForEmailAndToken method
        $passwordResetRepository->shouldReceive('findForEmailAndToken')
            ->with($user->email, 'reset_token')
            ->andReturn($passwordReset);

        // Mocking the delete method
        $passwordResetRepository->shouldReceive('deleteByEmailAndToken')
            ->with($passwordReset->email, $passwordReset->token)
            ->andReturn(true);

        // Mock the UserRepositoryInterface
        $userRepository = Mockery::mock(UserRepositoryInterface::class);
        $this->app->instance(UserRepositoryInterface::class, $userRepository);

        // Mocking the findByEmail method
        $userRepository->shouldReceive('findByEmail')
            ->with($user->email)
            ->andReturn($user);

        // Mocking the update method
        $userRepository->shouldReceive('update')
            ->with(Mockery::on(function ($arg) use ($user) {
                return $arg['email'] === $user->email && Hash::check('newpassword', $arg['password']);
            }), $user->id)
            ->andReturn(true);

        // Data to reset password
        $data = [
            'token' => 'reset_token', // Use the unhashed token for testing
            'email' => $user->email,
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ];

        // Send the reset password request
        $response = $this->postJson(route('user.reset-password-token'), $data);

        // Assert response status
        $response->assertStatus(200);

        // Assert the JSON structure
        $response->assertJsonStructure([
            'success',
            'data',
            'error',
            'errors',
            'extra',
        ]);

        // Assert the success message
        $response->assertJson([
            'success' => 1,
            'data' => ['message' => 'Password has been successfully updated'],
            'error' => null,
            'errors' => [],
            'extra' => [],
        ]);

        // Assert that the user's password has been updated
        $this->assertTrue(Hash::check('newpassword', $user->fresh()->password));
    }
}
