<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => Str::uuid(),
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'is_admin' => $this->faker->boolean,
            'email' => $this->faker->unique()->safeEmail,
            'email_verified_at' => $this->faker->optional()->dateTimeThisYear,
            'password' => Hash::make('userpassword'),
            'avatar' => $this->faker->optional()->uuid,
            'address' => $this->faker->address,
            'phone_number' => $this->faker->phoneNumber,
            'is_marketing' => $this->faker->boolean,
            'created_at' => now(),
            'updated_at' => now(),
            'last_login_at' => $this->faker->optional()->dateTimeThisYear,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
