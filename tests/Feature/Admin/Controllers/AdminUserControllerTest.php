<?php

namespace Tests\Feature\Admin\Controllers;

use App\Features\Post\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_list_users_and_inspect_a_users_posts(): void
    {
        $admin = User::factory()->admin()->create();
        $author = User::factory()->create();
        $post = Post::factory()->for($author)->create();

        $this->actingAs($admin)->get('/admin/users')->assertOk();
        $this->actingAs($admin)->get("/admin/users/{$author->id}/posts")->assertOk();
        $this->assertSame($author->id, $post->user_id);
    }

    public function test_admin_can_export_a_users_posts(): void
    {
        $admin = User::factory()->admin()->create();
        $author = User::factory()->create();
        Post::factory()->for($author)->create();
        Post::factory()->create();

        $response = $this->actingAs($admin)->get("/admin/users/{$author->id}/posts/export");

        $response->assertOk();
        ob_start();
        $response->baseResponse->sendContent();
        $this->assertStringStartsWith('PK', ob_get_clean());
    }
}
