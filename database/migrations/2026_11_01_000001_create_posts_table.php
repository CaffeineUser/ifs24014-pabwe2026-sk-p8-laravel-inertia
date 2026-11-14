<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id(); // bigint (PK)
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // relasi ke user.id
            $table->string('title'); // judul Postingan[cite:1]
            $table->string('slug')->unique();
            $table->string('category'); // Kategori artikel[cite: 1]
            $table->text('excerpt')->nullable(); // Ringkasan pemikat pembaca[cite: 1]
            $table->longText('content'); // Isi artikel lengkap (Markdown)[cite: 1]
            $table->string('cover_image_path')->nullable(); // Berkas gambar sampul[cite: 1]
            $table->boolean('isPublished')->default(false); // Status publikasi[cite: 1]
            $table->timestamp('published_at')->nullable(); // Waktu publikasi[cite: 1]
            $table->timestamps(); // created_at, update_at[cite: 1]
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
