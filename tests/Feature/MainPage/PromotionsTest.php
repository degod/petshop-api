<?php

namespace Tests\Feature\MainPage;

use App\Models\File;
use App\Models\Promotion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PromotionsTest extends TestCase
{
    use RefreshDatabase;

    public function testGetPromotions(): void
    {
        // Create files for metadata
        $files = File::factory()->count(3)->create();

        // Create promotions
        Promotion::factory()->count(10)->create()->each(function ($promotion) use ($files) {
            $promotion->metadata = [
                'valid_from' => now()->subDays(10)->format('Y-m-d'),
                'valid_to' => now()->addDays(10)->format('Y-m-d'),
                'image' => $files->random()->uuid,
            ];
            $promotion->save();
        });

        // Test the endpoint with default params
        $response = $this->getJson(route('main.promotions'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'current_page',
            'data' => [
                '*' => [
                    'uuid',
                    'title',
                    'content',
                    'metadata',
                    'created_at',
                    'updated_at',
                ],
            ],
            'first_page_url',
            'from',
            'last_page',
            'last_page_url',
            'links',
            'next_page_url',
            'path',
            'per_page',
            'prev_page_url',
            'to',
            'total',
        ]);

        // Test with query parameters
        $response = $this->getJson(route('main.promotions', [
            'page' => 1,
            'limit' => 5,
            'sortBy' => 'title',
            'desc' => true,
            'valid' => true,
        ]));

        $response->assertStatus(200);
    }
}
