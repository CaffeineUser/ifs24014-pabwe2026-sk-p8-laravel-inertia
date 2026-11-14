<?php

use App\Features\Admin\Controllers\AdminUserController;
// Import Controllers berdasarkan Feature-First Namespace
use App\Features\Auth\Controllers\AuthController;
use App\Features\Post\Controllers\AdminPostController;
use App\Features\Post\Controllers\PostController;
use App\Features\Post\Controllers\PublicPostController;
use App\Features\Profile\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| TIER 1: Rute Publik / Guest (Tanpa Login)
|--------------------------------------------------------------------------
| Referensi: Bagian 3.2 & 3.5[cite: 1]
*/
Route::get('/', [PublicPostController::class, 'index'])->name('home');
Route::get('/articles', [PublicPostController::class, 'index'])->name('articles.index');
Route::get('/articles/{slug}', [PublicPostController::class, 'show'])->name('articles.show');

// Autentikasi (Guest Only)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'createLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'storeLogin']);
    Route::get('/register', [AuthController::class, 'createRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'storeRegister']);
});

// Logout (Authenticated)
Route::post('/logout', [AuthController::class, 'destroyLogout'])->middleware('auth')->name('logout');

/*
|--------------------------------------------------------------------------
| TIER 2: Rute Anggota / Penulis (Member Dashboard)
|--------------------------------------------------------------------------
| Referensi: Bagian 3.2 & 3.5[cite: 1]
*/
Route::middleware(['auth'])->prefix('dashboard')->name('dashboard.')->group(function () {
    // Fitur Excel (PhpSpreadsheet) & OpenAI AI-Generator (didefinisikan sebelum route berparameter {post})
    Route::get('/posts/export', [PostController::class, 'exportExcel'])->name('posts.export');
    Route::post('/posts/import', [PostController::class, 'importExcel'])->name('posts.import');
    Route::post('/posts/ai-generate', [PostController::class, 'aiGenerate'])->name('posts.ai.generate');
    Route::post('/posts/ai-save', [PostController::class, 'aiSaveBatch'])->name('posts.ai.save');

    // Manajemen Artikel Mandiri (CRUD)
    Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{post}', [PostController::class, 'show'])->whereNumber('post')->name('posts.show');
    Route::put('/posts/{post}', [PostController::class, 'update'])->whereNumber('post')->name('posts.update');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->whereNumber('post')->name('posts.destroy');

    // Fitur Tambahan: Publish Toggle & Cover Image
    Route::patch('/posts/{post}/publish', [PostController::class, 'togglePublish'])->whereNumber('post')->name('posts.publish');
    Route::post('/posts/{post}/cover', [PostController::class, 'uploadCover'])->whereNumber('post')->name('posts.cover.upload');
    Route::delete('/posts/{post}/cover', [PostController::class, 'deleteCover'])->whereNumber('post')->name('posts.cover.delete');
});

// Manajemen Profil Pengguna
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

/*
|--------------------------------------------------------------------------
| TIER 3: Rute Admin (Panel Pengguna & Audit Sistem)
|--------------------------------------------------------------------------
| Referensi: Bagian 3.2 & 3.5[cite: 1]
*/
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Kelola Daftar Pengguna
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');

    // Monitoring Artikel User Tertentu & Ekspor Excel-nya
    Route::get('/users/{user}/posts', [AdminUserController::class, 'userPosts'])->name('users.posts');
    Route::get('/users/{user}/posts/export', [AdminUserController::class, 'exportUserPosts'])->name('users.posts.export');

    // Monitoring Seluruh Artikel Sistem & Ekspor Global
    Route::get('/posts', [AdminPostController::class, 'index'])->name('posts.index');
    Route::get('/posts/export', [AdminPostController::class, 'exportAllPosts'])->name('posts.export');
});
