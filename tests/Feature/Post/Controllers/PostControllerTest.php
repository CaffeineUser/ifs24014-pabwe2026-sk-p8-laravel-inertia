<?php

namespace Tests\Feature\Post\Controllers;

use App\Features\Post\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class PostControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_shows_owned_posts_and_publication_statistics(): void
    {
        $user = User::factory()->create();
        Post::factory()->for($user)->create(['is_published' => true]);
        Post::factory()->for($user)->create(['is_published' => false]);
        Post::factory()->create();

        $this->actingAs($user)
            ->get('/dashboard/posts')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Post/Index')
                ->where('stats.total', 2)
                ->where('stats.published', 1)
                ->where('stats.draft', 1));
    }

    public function test_author_can_view_their_post_and_other_authors_are_forbidden(): void
    {
        $author = User::factory()->create();
        $otherAuthor = User::factory()->create();
        $post = Post::factory()->for($author)->create(['is_published' => false]);

        $this->actingAs($author)->get("/dashboard/posts/{$post->id}")->assertOk();
        $this->actingAs($otherAuthor)->get("/dashboard/posts/{$post->id}")->assertForbidden();
    }

    public function test_store_rejects_an_invalid_post_without_persisting_it(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->from('/dashboard/posts')->post('/dashboard/posts', [
            'category' => 'Umum',
            'content' => 'Body',
        ]);

        $response->assertRedirect('/dashboard/posts');
        $response->assertSessionHasErrors('title');
        $this->assertDatabaseCount('posts', 0);
    }

    public function test_author_can_toggle_publication_and_manage_a_cover_image(): void
    {
        Storage::fake('public');
        $author = User::factory()->create();
        $post = Post::factory()->for($author)->create(['is_published' => false]);

        $this->actingAs($author)
            ->patch("/dashboard/posts/{$post->id}/publish")
            ->assertRedirect();
        $post->refresh();
        $this->assertTrue($post->is_published);
        $this->assertNotNull($post->published_at);

        $this->actingAs($author)
            ->post("/dashboard/posts/{$post->id}/cover", [
                'cover' => UploadedFile::fake()->image('cover.jpg'),
            ])
            ->assertRedirect();
        $post->refresh();
        Storage::disk('public')->assertExists($post->cover_image_path);

        $this->actingAs($author)
            ->delete("/dashboard/posts/{$post->id}/cover")
            ->assertRedirect();
        $this->assertNull($post->fresh()->cover_image_path);
    }

    public function test_cover_upload_requires_an_image(): void
    {
        $author = User::factory()->create();
        $post = Post::factory()->for($author)->create();

        $response = $this->actingAs($author)->post("/dashboard/posts/{$post->id}/cover", [
            'cover' => UploadedFile::fake()->create('document.txt'),
        ]);

        $response->assertSessionHasErrors('cover');
    }

    public function test_author_can_export_their_posts_to_excel(): void
    {
        $author = User::factory()->create();
        Post::factory()->for($author)->create(['is_published' => true]);
        Post::factory()->create();

        $response = $this->actingAs($author)->get('/dashboard/posts/export');

        $response->assertOk();
        ob_start();
        $response->baseResponse->sendContent();
        $content = ob_get_clean();
        $this->assertStringStartsWith('PK', $content);
    }

    public function test_author_can_import_posts_from_excel(): void
    {
        $author = User::factory()->create();
        $path = $this->createExcelFile([
            ['ID', 'Judul', 'Kategori', 'Konten'],
            ['', 'Imported Article', 'Pendidikan', 'Imported body'],
        ]);
        $file = UploadedFile::fake()->createWithContent('articles.xlsx', file_get_contents($path));

        try {
            $response = $this->actingAs($author)
                ->from('/dashboard/posts')
                ->post('/dashboard/posts/import', [
                    'file' => $file,
                ]);
        } finally {
            unlink($path);
        }

        $response->assertRedirect('/dashboard/posts');
        $response->assertSessionHas('success', '1 artikel berhasil diimpor.');
        $this->assertDatabaseHas('posts', [
            'user_id' => $author->id,
            'title' => 'Imported Article',
            'category' => 'Pendidikan',
            'isPublished' => false,
        ]);
    }

    public function test_import_rejects_files_that_are_not_excel_documents(): void
    {
        $author = User::factory()->create();

        $response = $this->actingAs($author)->from('/dashboard/posts')->post('/dashboard/posts/import', [
            'file' => UploadedFile::fake()->create('articles.txt'),
        ]);

        $response->assertSessionHasErrors('file');
    }

    public function test_ai_generation_defaults_to_one_draft_and_can_generate_a_batch(): void
    {
        $author = User::factory()->create();

        $defaultCount = $this->actingAs($author)
            ->from('/dashboard/posts')
            ->post('/dashboard/posts/ai-generate', ['topic' => 'Laravel']);
        $defaultCount->assertSessionHas('ai_posts', [[
            'title' => 'Artikel: Laravel (Bagian 1)',
            'category' => 'Teknologi',
            'excerpt' => 'Ringkasan pembahasan seputar Laravel.',
            'content' => "## Eksplorasi: Laravel\n\nArtikel ini membahas secara terperinci poin penting tentang Laravel.",
            'is_published' => false,
        ]]);

        $batch = $this->actingAs($author)
            ->from('/dashboard/posts')
            ->post('/dashboard/posts/ai-generate', ['topic' => 'Testing', 'count' => 2]);
        $batch->assertSessionHas('ai_posts', fn (array $posts): bool => count($posts) === 2);
    }

    public function test_ai_generation_validates_its_topic_and_batch_size(): void
    {
        $author = User::factory()->create();

        $response = $this->actingAs($author)
            ->from('/dashboard/posts')
            ->post('/dashboard/posts/ai-generate', ['topic' => 'Topic', 'count' => 6]);

        $response->assertSessionHasErrors('count');
    }

    public function test_ai_generated_posts_can_be_saved_as_a_batch(): void
    {
        $author = User::factory()->create();

        $response = $this->actingAs($author)->post('/dashboard/posts/ai-save', [
            'posts' => [
                [
                    'title' => 'Generated One',
                    'category' => 'Teknologi',
                    'content' => 'One body',
                ],
                [
                    'title' => 'Generated Two',
                    'category' => 'Opini',
                    'content' => 'Two body',
                ],
            ],
        ]);

        $response->assertRedirect(route('dashboard.posts.index'));
        $response->assertSessionHas('success', 'Artikel AI berhasil disimpan.');
        $this->assertDatabaseHas('posts', ['title' => 'Generated One', 'user_id' => $author->id]);
        $this->assertDatabaseHas('posts', ['title' => 'Generated Two', 'user_id' => $author->id]);
    }

    private function createExcelFile(array $rows): string
    {
        $spreadsheet = new Spreadsheet;
        $spreadsheet->getActiveSheet()->fromArray($rows);
        $path = tempnam(sys_get_temp_dir(), 'post-upload-');
        (new Xlsx($spreadsheet))->save($path);

        return $path;
    }
}
