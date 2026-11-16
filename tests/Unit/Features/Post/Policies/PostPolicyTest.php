<?php

namespace Tests\Unit\Features\Post\Policies;

use App\Features\Post\Models\Post;
use App\Features\Post\Policies\PostPolicy;
use App\Models\User;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class PostPolicyTest extends TestCase
{
    public static function draftViewPermissions(): array
    {
        return [
            'owner' => ['author', 7, true],
            'admin' => ['admin', 8, true],
            'other author' => ['author', 8, false],
        ];
    }

    #[DataProvider('draftViewPermissions')]
    public function test_draft_view_is_limited_to_its_owner_and_admin(string $role, int $userId, bool $allowed): void
    {
        $user = new User;
        $user->id = $userId;
        $user->role = $role;

        $post = new Post;
        $post->user_id = 7;
        $post->isPublished = false;

        $this->assertSame($allowed, (new PostPolicy)->view($user, $post));
    }

    public function test_published_posts_are_visible_to_guests(): void
    {
        $post = new Post;
        $post->isPublished = true;

        $this->assertTrue((new PostPolicy)->view(null, $post));
    }

    public function test_any_authenticated_user_can_create_a_post(): void
    {
        $this->assertTrue((new PostPolicy)->create(new User));
    }

    public function test_only_the_owner_or_admin_can_update_or_delete_a_post(): void
    {
        $post = new Post;
        $post->user_id = 7;

        $owner = new User;
        $owner->id = 7;
        $owner->role = 'author';

        $admin = new User;
        $admin->id = 8;
        $admin->role = 'admin';

        $otherAuthor = new User;
        $otherAuthor->id = 8;
        $otherAuthor->role = 'author';

        $policy = new PostPolicy;

        $this->assertTrue($policy->update($owner, $post));
        $this->assertTrue($policy->delete($owner, $post));
        $this->assertTrue($policy->update($admin, $post));
        $this->assertTrue($policy->delete($admin, $post));
        $this->assertFalse($policy->update($otherAuthor, $post));
        $this->assertFalse($policy->delete($otherAuthor, $post));
    }
}
