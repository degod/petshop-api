<?php

namespace Tests\Feature\MainPage;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogPostTest extends TestCase
{
    use RefreshDatabase;

    public function testViewBlogPost()
    {
        $post = Post::factory()->create();

        $response = $this->get(route('main.blog.view', ['uuid'=>$post->uuid]));

        $response->assertStatus(200)
                 ->assertJsonStructure([
                    'success',
                    'data' => [
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
                    ],
                    'error',
                    'errors',
                    'extra'
                 ]);
    }

    public function testViewNonExistingBlogPost()
    {
        $response = $this->get(route('main.blog.view', ['uuid'=>'non-existing-uuid']));

        $response->assertStatus(404)
                 ->assertJson([
                     'error' => 'Post not found',
                 ]);
    }
}
