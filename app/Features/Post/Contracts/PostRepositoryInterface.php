<?php

namespace App\Features\Post\Contracts;

use App\Features\Post\Models\Post;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface PostRepositoryInterface
{
    public function getPublicPaginated(int $perPage = 9, ?string $search = null, ?string $category = null): LengthAwarePaginator;

    public function findBySlug(string $slug): ?Post;

    public function getByUser(int $userId): Collection;

    public function getAllForAdmin(): Collection;

    public function create(array $data): Post;

    public function update(Post $post, array $data): bool;

    public function delete(Post $post): bool;
}
