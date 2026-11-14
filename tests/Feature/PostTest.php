<?php

namespace Tests\Feature;

use App\Features\Post\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_can_view_articles_index_and_show(): void
    {
        $user = User::factory()->create(['role' => 'member']);
        $post = Post::factory()->create([
            'user_id' => $user->id,
            'is_published' => true,
            'published_at' => now(),
        ]);

        // Uji Halaman Publik (Tier 1)
        $response = $this->get('/articles');
        $response->assertStatus(200);

        $responseDetail = $this->get("/articles/{$post->slug}");
        $responseDetail->assertStatus(200);
    }

    public function test_login_form_contains_required_ids(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        // Memastikan ID elemen pengujian otomatis tersedia sesuai spesifikasi 3.7
        $response->assertSee('id="login-username"', false);
        $response->assertSee('id="login-password"', false);
        $response->assertSee('id="login-submit"', false);
    }

    public function test_member_can_crud_posts(): void
    {
        $user = User::factory()->create(['role' => 'member']);

        // Simulasi Login & Simpan Post
        $response = $this->actingAs($user)->post('/dashboard/posts', [
            'title' => 'Judul Artikel Pengujian',
            'category' => 'Teknologi',
            'content' => 'Konten Markdown pengujian unit.',
            'is_published' => true,
        ]);

        $response->assertRedirect('/dashboard/posts');
        $this->assertDatabaseHas('posts', ['title' => 'Judul Artikel Pengujian']);

        $post = Post::where('title', 'Judul Artikel Pengujian')->first();

        // Uji Update Post
        $updateResponse = $this->actingAs($user)->put("/dashboard/posts/{$post->id}", [
            'title' => 'Judul Artikel Diperbarui',
            'category' => 'Teknologi',
            'content' => 'Konten baru.',
            'is_published' => false,
        ]);
        $updateResponse->assertRedirect('/dashboard/posts');

        // Uji Hapus Post
        $deleteResponse = $this->actingAs($user)->delete("/dashboard/posts/{$post->id}");
        $deleteResponse->assertRedirect('/dashboard/posts');
        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    public function test_admin_can_access_audit_panels(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $member = User::factory()->create(['role' => 'member']);

        $response = $this->actingAs($admin)->get('/admin/users');
        $response->assertStatus(200);

        $responseUserPosts = $this->actingAs($admin)->get("/admin/users/{$member->id}/posts");
        $responseUserPosts->assertStatus(200);

        $responseAllPosts = $this->actingAs($admin)->get('/admin/posts');
        $responseAllPosts->assertStatus(200);
    }
}
