<?php

namespace App\Features\Post\Controllers;

use App\Features\Post\Contracts\PostRepositoryInterface;
use App\Features\Post\Enums\PostCategory;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class PublicPostController extends Controller
{
    public function __construct(protected PostRepositoryInterface $posts) {}

    public function index(Request $request)
    {
        $search = $request->input('search');
        $category = $request->input('category');

        $posts = $this->posts->getPublicPaginated(9, $search, $category);

        return Inertia::render('Public/PostIndex', [
            'posts' => $posts,
            'categories' => PostCategory::values(),
            'filters' => compact('search', 'category'),
        ]);
    }

    public function show(string $slug)
    {
        $post = $this->posts->findBySlug($slug);

        // Render markdown to safe HTML on the server so the public page
        // does NOT need to ship the heavy @toast-ui editor CSS (~105 KB).
        $postArray = $post->toArray();
        $postArray['content_html'] = Str::markdown($postArray['content'] ?? '', [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);

        return Inertia::render('Public/PostShow', [
            'post' => $postArray,
        ]);
    }
}
