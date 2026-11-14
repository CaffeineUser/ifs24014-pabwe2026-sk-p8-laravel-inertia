<?php

namespace App\Features\Admin\Controllers;

use App\Features\Post\Models\Post;
use App\Features\Post\Services\PostExcelService;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminUserController extends Controller
{
    public function index(): Response
    {
        $users = User::latest()->get();

        return Inertia::render('Admin/UserList', [
            'users' => $users,
        ]);
    }

    public function userPosts(Request $request, User $user): Response
    {
        $posts = Post::where('user_id', $user->id)->latest()->get();

        return Inertia::render('Admin/UserPostList', [
            'user' => $user,
            'posts' => $posts,
        ]);
    }

    public function exportUserPosts(Request $request, User $user, PostExcelService $excelService): StreamedResponse
    {
        $posts = Post::where('user_id', $user->id)->latest()->get();

        return $excelService->export($posts);
    }
}
