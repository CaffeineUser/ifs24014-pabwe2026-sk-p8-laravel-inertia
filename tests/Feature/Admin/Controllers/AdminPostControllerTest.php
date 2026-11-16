<?php

namespace Tests\Feature\Admin\Controllers;

use App\Features\Post\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPostControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_export_all_posts_to_excel(): void
    {
        $admin = User::factory()->admin()->create();
        Post::factory()->create(['title' => 'First Report Row']);
        Post::factory()->create(['title' => 'Second Report Row']);

        $response = $this->actingAs($admin)->get('/admin/posts/export');

        $response->assertOk();
        ob_start();
        $response->baseResponse->sendContent();
        $this->assertStringStartsWith('PK', ob_get_clean());
    }

    public function test_non_admin_cannot_access_admin_routes(): void
    {
        $author = User::factory()->create();

        $this->actingAs($author)->get('/admin/posts')->assertForbidden();
    }
}
