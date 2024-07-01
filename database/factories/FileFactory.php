<?php

namespace Database\Factories;

use App\Models\File;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\File>
 */
class FileFactory extends Factory
{
    protected $model = File::class;
    
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => Str::uuid(),
            'name' => $this->faker->word . '.' . $this->faker->fileExtension,
            'path' => 'files/' . $this->faker->word . '.' . $this->faker->fileExtension,
            'size' => $this->faker->numberBetween(100, 10000),
            'type' => $this->faker->mimeType,
        ];
    }
}
