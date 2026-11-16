<?php

namespace App\Features\Post\Repositories;

use App\Features\Post\Contracts\PostRepositoryInterface;
use App\Features\Post\Models\Post;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class PostRepository implements PostRepositoryInterface
{
    public function getPublicPaginated(int $perPage = 9, ?string $search = null, ?string $category = null): LengthAwarePaginator
    {
        return Post::with('user')
            ->where('isPublished', true)
            ->when($search, fn ($q) => $q->whereRaw('LOWER(title) LIKE ?', ['%'.mb_strtolower($search).'%']))
            ->when($category, fn ($q) => $q->where('category', $category))
            ->latest('published_at')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function findBySlug(string $slug): ?Post
    {
        return Post::with('user')->where('slug', $slug)->firstOrFail();
    }

    public function getByUser(int $userId): Collection
    {
        return Post::where('user_id', $userId)->latest()->get();
    }

    public function getAllForAdmin(): Collection
    {
        return Post::with('user')->latest()->get();
    }

    public function create(array $data): Post
    {
        return Post::create($data);
    }

    public function update(Post $post, array $data): bool
    {
        return $post->update($data);
    }

    public function delete(Post $post): bool
    {
        return $post->delete();
    }
}
