import React from 'react';
import { Head, Link } from '@inertiajs/react';
import PublicLayout from '../../Layouts/PublicLayout';

export default function PostShow({ post }) {
  return (
    <PublicLayout>
      <Head>
        <title>{post.title}</title>
        <meta name="description" content={post.excerpt || post.title} />
      </Head>

      <article className="max-w-3xl mx-auto bg-white border border-slate-200 rounded-2xl p-6 sm:p-10 shadow-sm">
        <header className="mb-8 border-b border-slate-200 pb-6">
          <span className="inline-block text-xs font-bold uppercase tracking-wider bg-blue-50 text-blue-700 px-3 py-1 rounded-md mb-4">
            {post.category}
          </span>
          <h1 className="text-3xl sm:text-4xl font-extrabold text-slate-900 leading-tight mb-4">
            {post.title}
          </h1>
          <div className="flex items-center gap-4 text-sm text-slate-600">
            <span>Penulis: <strong className="text-slate-900">{post.user?.name}</strong></span>
            <span>•</span>
            <time dateTime={post.published_at}>{new Date(post.published_at).toLocaleDateString('id-ID')}</time>
          </div>
        </header>

        {post.cover_image_path && (
          <figure className="mb-8">
            <img
              src={`/storage/${post.cover_image_path}`}
              alt={`Ilustrasi sampul ${post.title}`}
              className="w-full rounded-xl max-h-96 object-cover"
            />
          </figure>
        )}

        {/* Server-rendered Markdown HTML — no heavy editor JS/CSS needed */}
        <div
          className="prose-content"
          dangerouslySetInnerHTML={{ __html: post.content_html || post.content }}
        />
      </article>

      {/* Kartu Ajakan Komunitas */}
      <aside aria-label="Ajakan Komunitas" className="max-w-3xl mx-auto mt-8 bg-slate-900 text-white rounded-2xl p-6 sm:p-8 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div>
          <h2 className="text-lg font-bold">Ingin menulis artikel seperti ini?</h2>
          <p className="text-sm text-slate-300">Bergabunglah dengan komunitas penulis BlogVerse Pro sekarang.</p>
        </div>
        <div className="flex gap-3 shrink-0">
          <Link href="/login" className="cursor-pointer bg-white text-slate-900 px-4 py-2 rounded-lg text-sm font-bold hover:bg-slate-100">
            Halaman Login
          </Link>
          <Link href="/register" className="cursor-pointer bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-blue-700">
            Halaman Register
          </Link>
        </div>
      </aside>
    </PublicLayout>
  );
}