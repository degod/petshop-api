<?php

namespace Tests\Feature\Categories;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Category;

class ListCategoryTest extends TestCase
{
    use RefreshDatabase;

    public function testListCategories(): void
    {
        // Create a few test categories
        Category::factory()->count(3)->create();

        // Send the GET request with query parameters
        $response = $this->get(route('categories.list', [
            'page' => 1,
            'limit' => 10,
            'sortBy' => 'title',
            'desc' => true
        ]));

        // Assert the response status
        $response->assertStatus(200);

        // Assert the JSON structure
        $response->assertJsonStructure([
            'current_page',
            'data' => [
                '*' => [
                    'uuid',
                    'title',
                    'slug',
                    'created_at',
                    'updated_at',
                ]
            ],
            'next_page_url',
            'path',
            'per_page',
            'prev_page_url',
            'to',
            'total',
        ]);
    }
}
