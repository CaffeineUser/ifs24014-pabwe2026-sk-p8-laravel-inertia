<?php

namespace Tests\Feature\Models;

use App\Features\Post\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_publication_alias_defaults_to_false_and_casts_values_to_booleans(): void
    {
        $post = new Post;

        $this->assertFalse($post->isPublished);

        $post->is_published = 1;
        $this->assertTrue($post->isPublished);

        $post->isPublished = 0;
        $this->assertFalse($post->is_published);
    }

    public function test_post_factory_and_user_relationships_create_linked_models(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->for($user)->create();

        $this->assertSame($user->id, $post->user->id);
        $this->assertSame([$post->id], $user->posts->modelKeys());
        $this->assertTrue($post->published_at->isToday());
    }
}
