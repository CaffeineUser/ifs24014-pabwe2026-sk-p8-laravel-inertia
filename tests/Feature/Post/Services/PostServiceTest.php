<?php

namespace Tests\Feature\Post\Services;

use App\Features\Post\Models\Post;
use App\Features\Post\Repositories\PostRepository;
use App\Features\Post\Services\PostService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PostServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_post_sets_publication_defaults_and_stores_an_optional_cover(): void
    {
        Storage::fake('public');
        $this->travelTo('2026-01-02 03:04:05');
        $service = new PostService(new PostRepository);
        $user = User::factory()->create();

        $draft = $service->createPost($user->id, [
            'title' => 'Draft Article',
            'category' => 'Umum',
            'content' => 'Draft body',
        ]);
        $published = $service->createPost($user->id, [
            'title' => 'Published Article',
            'category' => 'Teknologi',
            'content' => 'Published body',
            'is_published' => true,
            'cover' => UploadedFile::fake()->image('cover.jpg'),
        ]);

        $this->assertFalse($draft->is_published);
        $this->assertNull($draft->published_at);
        $this->assertTrue($published->is_published);
        $this->assertSame('2026-01-02 03:04:05', $published->published_at->format('Y-m-d H:i:s'));
        Storage::disk('public')->assertExists($published->cover_image_path);
    }

    public function test_update_post_changes_slug_publication_and_replaces_covers(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $oldCoverPath = 'covers/old-cover.jpg';
        Storage::disk('public')->put($oldCoverPath, 'old');
        $post = Post::factory()->for($user)->create([
            'title' => 'Old Article',
            'is_published' => false,
            'cover_image_path' => $oldCoverPath,
        ]);
        $oldSlug = $post->slug;
        $service = new PostService(new PostRepository);

        $service->updatePost($post, [
            'title' => 'New Article',
            'is_published' => true,
            'cover' => UploadedFile::fake()->image('new-cover.jpg'),
        ]);

        $post->refresh();
        $this->assertNotSame($oldSlug, $post->slug);
        $this->assertTrue($post->is_published);
        $this->assertNotNull($post->published_at);
        Storage::disk('public')->assertMissing($oldCoverPath);
        Storage::disk('public')->assertExists($post->cover_image_path);

        $publishedAt = $post->published_at;
        $service->updatePost($post, [
            'title' => 'New Article',
            'is_published' => true,
            'category' => 'Opini',
        ]);

        $this->assertSame($publishedAt->toDateTimeString(), $post->fresh()->published_at->toDateTimeString());
    }

    public function test_toggle_upload_and_delete_cover_support_both_existing_and_missing_files(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $service = new PostService(new PostRepository);
        $post = Post::factory()->for($user)->create([
            'is_published' => true,
            'cover_image_path' => 'covers/existing.jpg',
        ]);
        Storage::disk('public')->put('covers/existing.jpg', 'old');

        $service->togglePublish($post);
        $this->assertFalse($post->fresh()->is_published);
        $this->assertNull($post->fresh()->published_at);
        $service->togglePublish($post->fresh());
        $this->assertTrue($post->fresh()->is_published);

        $service->uploadCover($post, UploadedFile::fake()->image('replacement.jpg'));
        Storage::disk('public')->assertMissing('covers/existing.jpg');
        Storage::disk('public')->assertExists($post->fresh()->cover_image_path);

        $service->deleteCover($post->fresh());
        $this->assertNull($post->fresh()->cover_image_path);

        $postWithoutCover = Post::factory()->for($user)->create(['cover_image_path' => null]);
        $service->uploadCover($postWithoutCover, UploadedFile::fake()->image('first-cover.jpg'));
        Storage::disk('public')->assertExists($postWithoutCover->fresh()->cover_image_path);
        $service->deleteCover($postWithoutCover->fresh());
        $this->assertNull($postWithoutCover->fresh()->cover_image_path);
    }
}
