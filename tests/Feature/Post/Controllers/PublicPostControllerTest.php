<?php

namespace Tests\Feature\Post\Controllers;

use App\Features\Post\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPostControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_index_applies_search_and_category_filters(): void
    {
        $author = User::factory()->create();
        $post = Post::factory()->for($author)->create([
            'title' => 'Laravel Search Basics',
            'category' => 'Teknologi',
            'is_published' => true,
        ]);
        Post::factory()->for($author)->create([
            'title' => 'Other Laravel Guide',
            'category' => 'Pendidikan',
            'is_published' => true,
        ]);

        $this->get('/articles?search=laravel&category=Teknologi')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Public/PostIndex')
                ->where('posts.data.0.id', $post->id)
                ->where('filters.search', 'laravel')
                ->where('filters.category', 'Teknologi'));
    }

    public function test_public_post_page_renders_markdown_without_raw_html(): void
    {
        $post = Post::factory()->create([
            'content' => "# Safe heading\n\n<script>alert('unsafe')</script>",
            'is_published' => true,
        ]);

        $this->get("/articles/{$post->slug}")
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Public/PostShow')
                ->where('post.content_html', fn (string $html): bool => str_contains($html, '<h1>Safe heading</h1>')
                    && ! str_contains($html, '<script>')));
    }
}
