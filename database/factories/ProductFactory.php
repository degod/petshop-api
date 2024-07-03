<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Brand;
use App\Models\File;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_uuid' => (string) Category::factory()->create()->uuid,
            'uuid' => (string) Str::uuid(),
            'title' => $this->faker->sentence,
            'price' => $this->faker->randomFloat(2, 1, 1000),
            'description' => $this->faker->paragraph,
            'metadata' => [
                'brand' => (string) Brand::factory()->create()->uuid,
                'image' => (string) File::factory()->create()->uuid,
            ],
        ];
    }
}
