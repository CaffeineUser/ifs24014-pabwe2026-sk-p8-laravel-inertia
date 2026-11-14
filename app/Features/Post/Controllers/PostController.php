<?php

namespace App\Features\Post\Controllers;

use App\Features\Post\Contracts\PostRepositoryInterface;
use App\Features\Post\Enums\PostCategory;
use App\Features\Post\Models\Post;
use App\Features\Post\Services\PostExcelService;
use App\Features\Post\Services\PostService;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PostController extends Controller
{
    public function __construct(
        protected PostRepositoryInterface $posts,
        protected PostService $postService,
        protected PostExcelService $excelService
    ) {}

    public function index(Request $request): Response
    {
        $userPosts = $this->posts->getByUser($request->user()->id);

        $stats = [
            'total' => $userPosts->count(),
            'published' => $userPosts->where('isPublished', true)->count(),
            'draft' => $userPosts->where('isPublished', false)->count(),
        ];

        return Inertia::render('Post/Index', [
            'posts' => $userPosts,
            'stats' => $stats,
            'categories' => PostCategory::values(),
        ]);
    }

    public function show(Request $request, Post $post): Response
    {
        $this->authorize('view', $post);

        return Inertia::render('Post/Show', [
            'post' => $post->load('user'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string',
            'excerpt' => 'nullable|string',
            'content' => 'required|string',
            'is_published' => 'boolean',
            'cover' => 'nullable|image|max:2048',
        ]);

        $this->postService->createPost($request->user()->id, $validated);

        return redirect()->route('dashboard.posts.index')->with('success', 'Artikel berhasil dibuat.');
    }

    public function update(Request $request, Post $post): RedirectResponse
    {
        $this->authorize('update', $post);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string',
            'excerpt' => 'nullable|string',
            'content' => 'required|string',
            'is_published' => 'boolean',
            'cover' => 'nullable|image|max:2048',
        ]);

        $this->postService->updatePost($post, $validated);

        return redirect()->route('dashboard.posts.index')->with('success', 'Artikel berhasil diperbarui.');
    }

    public function destroy(Request $request, Post $post): RedirectResponse
    {
        $this->authorize('delete', $post);
        $this->posts->delete($post);

        return redirect()->route('dashboard.posts.index')->with('success', 'Artikel berhasil dihapus.');
    }

    public function togglePublish(Request $request, Post $post): RedirectResponse
    {
        $this->authorize('update', $post);
        $this->postService->togglePublish($post);

        return back();
    }

    public function uploadCover(Request $request, Post $post): RedirectResponse
    {
        $this->authorize('update', $post);
        $request->validate(['cover' => 'required|image|max:2048']);
        $this->postService->uploadCover($post, $request->file('cover'));

        return back();
    }

    public function deleteCover(Request $request, Post $post): RedirectResponse
    {
        $this->authorize('update', $post);
        $this->postService->deleteCover($post);

        return back();
    }

    public function exportExcel(Request $request): StreamedResponse
    {
        $userPosts = $this->posts->getByUser($request->user()->id);

        return $this->excelService->export($userPosts);
    }

    public function importExcel(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        $imported = $this->excelService->import($request->file('file'));
        foreach ($imported as $item) {
            $this->postService->createPost($request->user()->id, $item);
        }

        return back()->with('success', count($imported).' artikel berhasil diimpor.');
    }

    public function aiGenerate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'topic' => 'required|string|max:255',
            'count' => 'nullable|integer|min:1|max:5',
        ]);

        $count = $validated['count'] ?? 1;
        $generated = [];
        for ($i = 1; $i <= $count; $i++) {
            $generated[] = [
                'title' => "Artikel: {$validated['topic']} (Bagian {$i})",
                'category' => 'Teknologi',
                'excerpt' => "Ringkasan pembahasan seputar {$validated['topic']}.",
                'content' => "## Eksplorasi: {$validated['topic']}\n\nArtikel ini membahas secara terperinci poin penting tentang {$validated['topic']}.",
                'is_published' => false,
            ];
        }

        return back()->with('ai_posts', $generated)->with('success', 'Draf artikel berhasil dibuat oleh asisten AI.');
    }

    public function aiSaveBatch(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'posts' => 'required|array',
            'posts.*.title' => 'required|string|max:255',
            'posts.*.category' => 'required|string',
            'posts.*.content' => 'required|string',
        ]);

        foreach ($validated['posts'] as $postData) {
            $this->postService->createPost($request->user()->id, $postData);
        }

        return redirect()->route('dashboard.posts.index')->with('success', 'Artikel AI berhasil disimpan.');
    }
}
