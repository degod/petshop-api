<?php

namespace Tests\Feature\MainPage;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogPostsTest extends TestCase
{
    use RefreshDatabase;

    public function testGetBlogPosts()
    {
        Post::factory()->count(10)->create();

        $response = $this->get(route('main.blog'));

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'current_page',
                     'data' => [
                         '*' => [
                             'uuid',
                             'title',
                             'slug',
                             'content',
                             'metadata' => [
                                 'image',
                                 'author',
                             ],
                             'created_at',
                             'updated_at',
                         ]
                     ],
                     'first_page_url',
                     'from',
                     'last_page',
                     'last_page_url',
                     'links' => [
                         '*' => [
                             'url',
                             'label',
                             'active',
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
