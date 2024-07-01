<?php

namespace Database\Factories;

use App\Models\Promotion;
use App\Models\File;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Promotion>
 */
class PromotionFactory extends Factory
{
    protected $model = Promotion::class;
    
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $file = File::inRandomOrder()->first();

        return [
            'uuid' => Str::uuid(),
            'title' => $this->faker->sentence,
            'content' => $this->faker->paragraph,
            'metadata' => $file ? [
                'valid_from' => $this->faker->date('Y-m-d'),
                'valid_to' => $this->faker->date('Y-m-d'),
                'image' => $file->uuid,
            ] : null,
        ];
    }
}
