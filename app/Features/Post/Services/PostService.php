<?php

namespace App\Features\Post\Services;

use App\Features\Post\Contracts\PostRepositoryInterface;
use App\Features\Post\Models\Post;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostService
{
    public function __construct(protected PostRepositoryInterface $posts) {}

    public function createPost(int $userId, array $data): Post
    {
        $data['user_id'] = $userId;
        $data['slug'] = Str::slug($data['title']).'-'.uniqid();
        if (! isset($data['is_published'])) {
            $data['is_published'] = false;
        }
        $data['published_at'] = $data['is_published'] ? now() : null;

        if (isset($data['cover'])) {
            $data['cover_image_path'] = $data['cover']->store('covers', 'public');
        }

        return $this->posts->create($data);
    }

    public function updatePost(Post $post, array $data): bool
    {
        if (isset($data['title']) && $data['title'] !== $post->title) {
            $data['slug'] = Str::slug($data['title']).'-'.uniqid();
        }

        if (isset($data['is_published']) && $data['is_published'] !== $post->is_published) {
            $data['published_at'] = $data['is_published'] ? now() : null;
        }

        if (isset($data['cover'])) {
            if ($post->cover_image_path) {
                Storage::disk('public')->delete($post->cover_image_path);
            }
            $data['cover_image_path'] = $data['cover']->store('covers', 'public');
        }

        return $this->posts->update($post, $data);
    }

    public function togglePublish(Post $post): bool
    {
        $newStatus = ! $post->is_published;

        return $this->posts->update($post, [
            'is_published' => $newStatus,
            'published_at' => $newStatus ? now() : null,
        ]);
    }

    public function uploadCover(Post $post, $file): bool
    {
        if ($post->cover_image_path) {
            Storage::disk('public')->delete($post->cover_image_path);
        }
        $path = $file->store('covers', 'public');

        return $this->posts->update($post, ['cover_image_path' => $path]);
    }

    public function deleteCover(Post $post): bool
    {
        if ($post->cover_image_path) {
            Storage::disk('public')->delete($post->cover_image_path);
        }

        return $this->posts->update($post, ['cover_image_path' => null]);
    }
}
