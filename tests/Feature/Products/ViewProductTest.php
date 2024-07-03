<?php

namespace Tests\Feature\Products;

use Tests\TestCase;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ViewProductTest extends TestCase
{
    use RefreshDatabase;

    public function testViewProduct(): void
    {
        $product = Product::factory()->create();

        $response = $this->getJson(route('products.view', ['uuid' => $product->uuid]));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'uuid',
                'title',
                'price',
                'description',
                'metadata',
                'created_at',
                'updated_at',
            ],
            'error',
            'errors',
            'extra'
        ]);
    }

    public function testViewProductNotFound(): void
    {
        $response = $this->getJson(route('products.view', ['uuid' => 'non-existent-uuid']));

        $response->assertStatus(404);
        $response->assertJson([
            'success' => false,
            'error' => 'Product not found'
        ]);
    }
}
