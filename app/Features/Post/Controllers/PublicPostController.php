<?php

namespace App\Features\Post\Controllers;

use App\Features\Post\Contracts\PostRepositoryInterface;
use App\Features\Post\Enums\PostCategory;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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

        return Inertia::render('Public/PostShow', [
            'post' => $post,
        ]);
    }
}
