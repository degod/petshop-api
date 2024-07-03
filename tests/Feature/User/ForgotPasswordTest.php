<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ForgotPasswordTest extends TestCase
{
    use RefreshDatabase;

    public function testForgotPassword(): void
    {
        // Create a test user
        $user = User::factory()->create([
            'email' => 'user@example.com',
        ]);

        // Call the password reset endpoint
        $response = $this->postJson(route('user.forgot-password'), [
            'email' => $user->email,
        ]);

        // Assert response status
        $response->assertStatus(200);

        // Assert the JSON structure
        $response->assertJsonStructure([
            'success',
            'data' => [
                'reset_token',
            ],
            'error',
            'errors',
            'extra',
        ]);

        // Assert the reset_token key exists in the response data
        $response->assertJson([
            'success' => 1,
            'data' => [
                'reset_token' => true,
            ],
            'error' => null,
            'errors' => [],
            'extra' => [],
        ]);
    }
}
