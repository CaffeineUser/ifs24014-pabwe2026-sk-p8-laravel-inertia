<?php

namespace Tests\Feature\Post\Repositories;

use App\Features\Post\Models\Post;
use App\Features\Post\Repositories\PostRepository;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_posts_are_paginated_and_unpublished_posts_are_excluded(): void
    {
        $author = User::factory()->create();
        $published = Post::factory()->for($author)->create(['is_published' => true]);
        Post::factory()->for($author)->create(['is_published' => false]);

        $posts = (new PostRepository)->getPublicPaginated();

        $this->assertSame([$published->id], $posts->getCollection()->modelKeys());
        $this->assertTrue($posts->first()->relationLoaded('user'));
    }

    public function test_public_post_search_and_category_filters_can_be_combined(): void
    {
        $author = User::factory()->create();
        $matchingPost = Post::factory()->for($author)->create([
            'title' => 'Laravel Search Guide',
            'category' => 'Teknologi',
            'is_published' => true,
        ]);
        Post::factory()->for($author)->create([
            'title' => 'Another Laravel Article',
            'category' => 'Pendidikan',
            'is_published' => true,
        ]);

        $posts = (new PostRepository)->getPublicPaginated(9, 'laravel', 'Teknologi');

        $this->assertSame([$matchingPost->id], $posts->getCollection()->modelKeys());
    }

    public function test_post_can_be_found_by_slug_and_missing_slugs_fail(): void
    {
        $post = Post::factory()->create(['slug' => 'known-article']);
        $repository = new PostRepository;

        $foundPost = $repository->findBySlug('known-article');

        $this->assertSame($post->id, $foundPost->id);
        $this->assertTrue($foundPost->relationLoaded('user'));
        $this->expectException(ModelNotFoundException::class);
        $repository->findBySlug('missing-article');
    }

    public function test_repository_returns_posts_for_a_user_and_for_admin(): void
    {
        $author = User::factory()->create();
        $otherAuthor = User::factory()->create();
        $userPost = Post::factory()->for($author)->create();
        Post::factory()->for($otherAuthor)->create();

        $repository = new PostRepository;

        $this->assertSame([$userPost->id], $repository->getByUser($author->id)->modelKeys());
        $this->assertCount(2, $repository->getAllForAdmin());
        $this->assertTrue($repository->getAllForAdmin()->first()->relationLoaded('user'));
    }

    public function test_repository_creates_updates_and_deletes_posts(): void
    {
        $author = User::factory()->create();
        $repository = new PostRepository;

        $post = $repository->create([
            'user_id' => $author->id,
            'title' => 'Created through repository',
            'slug' => 'created-through-repository',
            'category' => 'Umum',
            'content' => 'Article body',
        ]);

        $this->assertTrue($repository->update($post, ['title' => 'Updated through repository']));
        $this->assertSame('Updated through repository', $post->fresh()->title);
        $this->assertTrue($repository->delete($post));
        $this->assertModelMissing($post);
    }
}
