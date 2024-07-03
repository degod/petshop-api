<?php

namespace Tests\Feature\Products;

use App\Models\Brand;
use App\Models\Category;
use App\Models\File;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListProductTest extends TestCase
{
    use RefreshDatabase;

    public function testListProducts(): void
    {
        $category = Category::factory()->create();
        $brand = Brand::factory()->create();
        $file = File::factory()->create();

        Product::factory()->count(3)->create([
            'category_uuid' => $category->uuid,
            'metadata' => json_encode([
                'brand' => $brand->uuid,
                'image' => $file->uuid,
            ]),
        ]);

        $response = $this->getJson(route('products.list', [
            'page' => 1,
            'limit' => 2,
            'sortBy' => 'title',
            'desc' => false,
            'category' => $category->uuid,
            'price' => null,
            'brand' => $brand->uuid,
            'title' => '',
        ]));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'current_page',
            'data' => [
                '*' => [
                    'uuid',
                    'title',
                    'price',
                    'description',
                    'metadata',
                    'created_at',
                    'updated_at',
                ],
            ],
            'first_page_url',
            'from',
            'last_page',
            'last_page_url',
            'next_page_url',
            'path',
            'per_page',
            'prev_page_url',
            'to',
            'total',
        ]);
    }
}
