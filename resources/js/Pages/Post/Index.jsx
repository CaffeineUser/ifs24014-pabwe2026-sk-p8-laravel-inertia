import React, { useState } from 'react';
import { Head, router, useForm } from '@inertiajs/react';
import AppLayout from '../../Layouts/AppLayout';
import KebabMenu from '../../Components/KebabMenu';
import FullScreenModal from '../../Components/FullScreenModal';
import MarkdownEditor from '../../Components/MarkdownEditor';

export default function PostIndex({ posts = [], stats = {}, categories = [] }) {
  const [modalOpen, setModalOpen] = useState(false);
  const [editingPost, setEditingPost] = useState(null);
  const [aiCount, setAiCount] = useState(1);
  const [aiTopic, setAiTopic] = useState('');

  const { data, setData, post, put, processing, reset, errors } = useForm({
    title: '',
    category: categories[0] || 'Umum',
    excerpt: '',
    content: '',
    is_published: false,
  });

  const openCreateModal = () => {
    setEditingPost(null);
    reset();
    setModalOpen(true);
  };

  const openEditModal = (item) => {
    setEditingPost(item);
    setData({
      title: item.title,
      category: item.category,
      excerpt: item.excerpt || '',
      content: item.content,
      is_published: item.is_published,
    });
    setModalOpen(true);
  };

  const closeModal = () => setModalOpen(false);

  const handleSave = (e) => {
    e.preventDefault();
    if (editingPost) {
      put(`/dashboard/posts/${editingPost.id}`, {
        onSuccess: () => setModalOpen(false),
      });
    } else {
      post('/dashboard/posts', {
        onSuccess: () => setModalOpen(false),
      });
    }
  };

  const handleTogglePublish = (postId) => {
    router.patch(`/dashboard/posts/${postId}/publish`);
  };

  const handleDelete = (postId) => {
    if (confirm('Yakin ingin menghapus artikel ini?')) {
      router.delete(`/dashboard/posts/${postId}`);
    }
  };

  const handleAiGenerate = (e) => {
    e.preventDefault();
    router.post('/dashboard/posts/ai-generate', { topic: aiTopic, count: aiCount });
  };

  return (
    <AppLayout>
      <Head title="Dashboard Artikel Penulis" />

      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <h1 className="text-2xl font-extrabold text-slate-900">Artikel Saya</h1>
        <div className="flex flex-wrap gap-2">
          <a
            href="/dashboard/posts/export"
            className="cursor-pointer bg-white border border-slate-300 text-slate-800 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-slate-50"
          >
            Ekspor Excel
          </a>
          <button
            type="button"
            onClick={openCreateModal}
            className="cursor-pointer bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-800"
          >
            + Tambah Artikel
          </button>
        </div>
      </div>

      {/* Tiga Kartu Ringkasan Statistik */}
      <section aria-label="Ringkasan Statistik Artikel" className="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div className="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
          <p className="text-sm font-semibold text-slate-600">Total Artikel</p>
          <p className="text-3xl font-extrabold text-slate-900 mt-1">{stats.total ?? posts.length}</p>
        </div>
        <div className="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
          <p className="text-sm font-semibold text-emerald-700">Total Selesai (Publik)</p>
          <p className="text-3xl font-extrabold text-emerald-800 mt-1">{stats.published ?? 0}</p>
        </div>
        <div className="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
          <p className="text-sm font-semibold text-amber-700">Belum Selesai (Draf)</p>
          <p className="text-3xl font-extrabold text-amber-800 mt-1">{stats.draft ?? 0}</p>
        </div>
      </section>

      {/* AI Generator Interaktif (input type="number") */}
      <section aria-label="AI Generator Artikel" className="bg-white p-5 rounded-xl border border-slate-200 shadow-sm mb-6">
        <h2 className="text-sm font-bold text-slate-900 mb-3">AI Draft Generator (OpenAI)</h2>
        <form onSubmit={handleAiGenerate} className="flex flex-col sm:flex-row gap-3">
          <div className="flex-1">
            <label htmlFor="ai-topic" className="sr-only">Topik Artikel AI</label>
            <input
              id="ai-topic"
              type="text"
              placeholder="Masukkan topik artikel yang diinginkan..."
              value={aiTopic}
              onChange={(e) => setAiTopic(e.target.value)}
              required
              className="w-full px-3.5 py-2 border border-slate-300 rounded-lg text-sm"
            />
          </div>
          <div className="w-full sm:w-32">
            <label htmlFor="ai-count" className="sr-only">Jumlah Artikel</label>
            <input
              id="ai-count"
              type="number"
              min="1"
              max="5"
              value={aiCount}
              onChange={(e) => setAiCount(Number(e.target.value))}
              required
              className="w-full px-3.5 py-2 border border-slate-300 rounded-lg text-sm"
            />
          </div>
          <button
            type="submit"
            className="cursor-pointer bg-slate-900 text-white px-5 py-2 rounded-lg text-sm font-semibold hover:bg-slate-800"
          >
            Generate AI
          </button>
        </form>
      </section>

      {/* Daftar Artikel Penulis (Format List View, bukan Card) */}
      <div className="bg-white border border-slate-200 rounded-xl shadow-sm overflow-visible">
        <ul className="divide-y divide-slate-200" aria-label="Daftar Artikel Milik Penulis">
          {posts.map((item) => (
            <li key={item.id} className="p-4 sm:px-6 flex items-center justify-between gap-4 hover:bg-slate-50">
              <div className="min-w-0 flex-1">
                <div className="flex items-center gap-2.5 mb-1">
                  <span
                    className={`inline-flex px-2.5 py-0.5 rounded-full text-xs font-bold ${
                      item.is_published ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'
                    }`}
                  >
                    {item.is_published ? 'Dipublikasikan' : 'Draf'}
                  </span>
                  <span className="text-xs font-semibold text-slate-600">{item.category}</span>
                </div>
                <p className="text-base font-bold text-slate-900 truncate">{item.title}</p>
              </div>

              <div className="flex items-center gap-2 shrink-0">
                <button
                  type="button"
                  onClick={() => handleTogglePublish(item.id)}
                  className="cursor-pointer px-3 py-1.5 text-xs font-semibold rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-100"
                >
                  {item.is_published ? 'Jadikan Draf' : 'Publikasikan'}
                </button>

                <KebabMenu
                  itemTitle={item.title}
                  onDetail={() => router.get(`/dashboard/posts/${item.id}`)}
                  onEdit={() => openEditModal(item)}
                  onDelete={() => handleDelete(item.id)}
                />
              </div>
            </li>
          ))}
        </ul>
      </div>

      {/* Modal Full Screen Tambah / Ubah */}
      <FullScreenModal
        isOpen={modalOpen}
        onClose={closeModal}
        title={editingPost ? 'Ubah Artikel' : 'Tambah Artikel Baru'}
        footerActions={
          <>
            <button
              type="button"
              onClick={closeModal}
              className="cursor-pointer px-4 py-2 rounded-lg border border-slate-400 text-slate-800 text-sm font-semibold hover:bg-slate-200"
            >
              Batal
            </button>
            <button
              type="submit"
              form="post-form"
              disabled={processing}
              className="cursor-pointer px-5 py-2 rounded-lg bg-blue-700 text-white text-sm font-semibold hover:bg-blue-800 flex items-center gap-2"
            >
              {processing && <span className="animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-full" />}
              Simpan Artikel
            </button>
          </>
        }
      >
        <form id="post-form" onSubmit={handleSave} className="space-y-5">
          <div>
            <label htmlFor="post-title" className="block text-sm font-bold text-slate-800 mb-1">Judul Artikel</label>
            <input
              id="post-title"
              type="text"
              value={data.title}
              onChange={(e) => setData('title', e.target.value)}
              required
              className="w-full px-3.5 py-2 border border-slate-300 rounded-lg"
            />
            {errors.title && <p role="alert" className="text-sm text-red-700 mt-1">{errors.title}</p>}
          </div>

          <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label htmlFor="post-category" className="block text-sm font-bold text-slate-800 mb-1">Kategori</label>
              <select
                id="post-category"
                value={data.category}
                onChange={(e) => setData('category', e.target.value)}
                className="w-full px-3.5 py-2 border border-slate-300 rounded-lg"
              >
                {categories.map((cat) => (
                  <option key={cat} value={cat}>{cat}</option>
                ))}
              </select>
            </div>

            <div className="flex items-center pt-6">
              <input
                id="post-published"
                type="checkbox"
                checked={data.is_published}
                onChange={(e) => setData('is_published', e.target.checked)}
                className="h-4 w-4 text-blue-700 border-slate-300 rounded"
              />
              <label htmlFor="post-published" className="ml-2 text-sm font-semibold text-slate-800">
                Langsung Publikasikan ke Umum
              </label>
            </div>
          </div>

          <div>
            <label htmlFor="post-excerpt" className="block text-sm font-bold text-slate-800 mb-1">Ringkasan (Excerpt)</label>
            <textarea
              id="post-excerpt"
              rows="2"
              value={data.excerpt}
              onChange={(e) => setData('excerpt', e.target.value)}
              className="w-full px-3.5 py-2 border border-slate-300 rounded-lg"
            />
          </div>

          <div>
            <span className="block text-sm font-bold text-slate-800 mb-1">Konten Artikel (Markdown)</span>
            <MarkdownEditor
              initialValue={data.content}
              onChange={(markdown) => setData('content', markdown)}
            />
            {errors.content && <p role="alert" className="text-sm text-red-700 mt-1">{errors.content}</p>}
          </div>
        </form>
      </FullScreenModal>
    </AppLayout>
  );
}