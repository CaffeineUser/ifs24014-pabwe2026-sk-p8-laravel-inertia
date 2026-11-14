import React from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AppLayout from '../../Layouts/AppLayout';
import MarkdownViewer from '../../Components/MarkdownViewer';

export default function PostShow({ post }) {
  const handleCoverUpload = (e) => {
    const file = e.target.files[0];
    if (!file) return;
    router.post(`/dashboard/posts/${post.id}/cover`, { cover: file }, { forceFormData: true });
  };

  const handleCoverDelete = () => {
    router.delete(`/dashboard/posts/${post.id}/cover`);
  };

  return (
    <AppLayout>
      <Head title={`Detail: ${post.title}`} />

      <div className="mb-4">
        <Link href="/dashboard/posts" className="text-sm font-semibold text-blue-700 hover:underline">
          ← Kembali ke Daftar Artikel
        </Link>
      </div>

      <div className="bg-white border border-slate-200 rounded-xl p-6 sm:p-8 shadow-sm">
        <h1 className="text-2xl font-extrabold text-slate-900 mb-2">{post.title}</h1>
        <p className="text-sm text-slate-600 mb-6">Kategori: <strong>{post.category}</strong></p>

        {/* Manajemen Foto Sampul */}
        <div className="mb-6 p-4 bg-slate-50 border border-slate-200 rounded-lg">
          <label htmlFor="cover-upload" className="block text-sm font-bold text-slate-800 mb-2">
            Foto Sampul Artikel
          </label>
          {post.cover_image_path && (
            <div className="mb-3">
              <img
                src={`/storage/${post.cover_image_path}`}
                alt={`Sampul ${post.title}`}
                className="max-h-60 rounded-lg object-cover mb-2"
              />
              <button
                type="button"
                onClick={handleCoverDelete}
                className="cursor-pointer text-xs font-semibold text-red-700 hover:underline"
              >
                Hapus Foto Sampul
              </button>
            </div>
          )}
          <input
            id="cover-upload"
            type="file"
            accept="image/*"
            onChange={handleCoverUpload}
            className="text-sm text-slate-700"
          />
        </div>

        <MarkdownViewer content={post.content} />
      </div>
    </AppLayout>
  );
}