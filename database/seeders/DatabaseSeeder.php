<?php

namespace Database\Seeders;

use App\Features\Post\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Administrator',
                'email' => 'admin@blogverse.test',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        $author = User::firstOrCreate(
            ['username' => 'penulis'],
            [
                'name' => 'Budi Penulis',
                'email' => 'penulis@blogverse.test',
                'password' => Hash::make('password'),
                'role' => 'author',
            ]
        );

        if (Post::count() === 0) {
            Post::create([
                'user_id' => $author->id,
                'title' => 'Panduan Memulai dengan Laravel 12 dan Inertia React',
                'slug' => 'panduan-memulai-laravel-12-inertia-react-'.Str::random(5),
                'category' => 'Teknologi',
                'excerpt' => 'Artikel pengantar untuk memahami arsitektur modern Laravel bersama Inertia.js dan React.',
                'content' => "# Memulai Laravel & Inertia\n\nInertia.js memungkinkan pengembang membangun aplikasi monolith modern dengan fleksibilitas SPA menggunakan React.\n\n### Kelebihan Utama:\n- Tidak perlu membuat REST API terpisah untuk data frontend.\n- Keamanan bawaan Laravel (CSRF, sesi, autentikasi).\n- Produktivitas tinggi.",
                'isPublished' => true,
                'published_at' => now(),
            ]);

            Post::create([
                'user_id' => $author->id,
                'title' => 'Draf Artikel: Tips Optimasi Query Database',
                'slug' => 'tips-optimasi-query-database-'.Str::random(5),
                'category' => 'Edukasi',
                'excerpt' => 'Draf tips penting indexing dan eager loading di PostgreSQL.',
                'content' => "# Optimasi Query\n\nSelalu gunakan eager loading `with()` untuk menghindari masalah N+1.",
                'isPublished' => false,
                'published_at' => null,
            ]);
        }
    }
}
