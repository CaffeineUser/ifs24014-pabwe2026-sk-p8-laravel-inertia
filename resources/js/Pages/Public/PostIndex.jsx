import React, { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import PublicLayout from '../../Layouts/PublicLayout';

export default function PostIndex({ posts, categories = [], filters = {} }) {
  const [search, setSearch] = useState(filters.search || '');
  const [category, setCategory] = useState(filters.category || '');

  const handleFilter = (e) => {
    e.preventDefault();
    router.get('/articles', { search, category }, { preserveState: true });
  };

  return (
    <PublicLayout>
      <Head>
        <title>Beranda Publik & Galeri Artikel</title>
        <meta name="description" content="Baca berbagai artikel teknologi, pendidikan, bisnis, dan opini terbaru di BlogVerse Pro." />
      </Head>

      {/* Hero Banner Beranda */}
      <section aria-labelledby="hero-heading" className="bg-white border border-slate-200 rounded-2xl p-8 sm:p-10 mb-8 shadow-sm">
        <h1 id="hero-heading" className="text-3xl sm:text-4xl font-extrabold text-slate-900 mb-3">
          Wawasan & Gagasan Tanpa Batas
        </h1>
        <p className="text-slate-600 max-w-2xl text-base sm:text-lg mb-6">
          Temukan tulisan berkualitas dari berbagai penulis, atau mulai publikasikan pemikiran Anda sendiri hari ini.
        </p>
        <div className="flex flex-wrap gap-3">
          <Link
            href="/login"
            className="cursor-pointer bg-blue-700 text-white px-5 py-2.5 rounded-lg font-semibold hover:bg-blue-800 transition"
          >
            Halaman Login (Masuk)
          </Link>
          <Link
            href="/register"
            className="cursor-pointer bg-slate-100 text-slate-900 border border-slate-300 px-5 py-2.5 rounded-lg font-semibold hover:bg-slate-200 transition"
          >
            Halaman Register (Daftar Penulis)
          </Link>
        </div>
      </section>

      {/* Search & Category Filter */}
      <form onSubmit={handleFilter} className="flex flex-col sm:flex-row gap-3 mb-8" role="search" aria-label="Pencarian artikel">
        <div className="flex-1">
          <label htmlFor="search-input" className="sr-only">Cari judul atau konten artikel</label>
          <input
            id="search-input"
            type="search"
            placeholder="Cari judul atau konten artikel..."
            value={search}
            onChange={(e) => setSearch(e.target.value)}
            className="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg text-slate-900 focus:ring-2 focus:ring-blue-600"
          />
        </div>
        <div>
          <label htmlFor="category-select" className="sr-only">Filter Kategori</label>
          <select
            id="category-select"
            value={category}
            onChange={(e) => setCategory(e.target.value)}
            className="w-full sm:w-48 px-4 py-2.5 bg-white border border-slate-300 rounded-lg text-slate-900 focus:ring-2 focus:ring-blue-600"
          >
            <option value="">Semua Kategori</option>
            {categories.map((cat) => (
              <option key={cat} value={cat}>{cat}</option>
            ))}
          </select>
        </div>
        <button
          type="submit"
          className="cursor-pointer bg-slate-900 text-white px-6 py-2.5 rounded-lg font-semibold hover:bg-slate-800 transition"
        >
          Cari
        </button>
      </form>

      {/* Article Grid */}
      <section aria-label="Daftar Artikel Publik" className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {posts?.data?.length > 0 ? (
          posts.data.map((post) => (
            <article key={post.id} className="bg-white border border-slate-200 rounded-xl overflow-hidden flex flex-col shadow-sm hover:shadow-md transition">
              {post.cover_image_path && (
                <img
                  src={`/storage/${post.cover_image_path}`}
                  alt={`Gambar sampul untuk artikel ${post.title}`}
                  loading="lazy"
                  className="w-full h-48 object-cover"
                />
              )}
              <div className="p-5 flex-1 flex flex-col">
                <span className="inline-block self-start text-xs font-bold uppercase tracking-wider bg-blue-50 text-blue-700 px-2.5 py-1 rounded-md mb-3">
                  {post.category}
                </span>
                <h2 className="text-lg font-bold text-slate-900 mb-2 line-clamp-2">
                  <Link href={`/articles/${post.slug}`} className="hover:text-blue-700 focus:underline">
                    {post.title}
                  </Link>
                </h2>
                <p className="text-sm text-slate-600 line-clamp-3 mb-4 flex-1">
                  {post.excerpt}
                </p>
                <div className="text-xs text-slate-500 pt-3 border-t border-slate-100 flex justify-between">
                  <span>Oleh <strong className="text-slate-700">{post.user?.name}</strong></span>
                  <time dateTime={post.published_at}>{new Date(post.published_at).toLocaleDateString('id-ID')}</time>
                </div>
              </div>
            </article>
          ))
        ) : (
          <p className="col-span-full text-center text-slate-600 py-12">Belum ada artikel yang dipublikasikan.</p>
        )}
      </section>
    </PublicLayout>
  );
}