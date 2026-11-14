<?php

namespace App\Features\Post\Controllers;

use App\Features\Post\Contracts\PostRepositoryInterface;
use App\Features\Post\Services\PostExcelService;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminPostController extends Controller
{
    public function __construct(
        protected PostRepositoryInterface $posts,
        protected PostExcelService $excelService
    ) {}

    public function index(): Response
    {
        $posts = $this->posts->getAllForAdmin();

        return Inertia::render('Admin/PostList', [
            'posts' => $posts,
        ]);
    }

    public function exportAllPosts(): StreamedResponse
    {
        $posts = $this->posts->getAllForAdmin();

        return $this->excelService->export($posts);
    }
}
