import React from 'react';
import { Head } from '@inertiajs/react';
import AppLayout from '../../Layouts/AppLayout';

export default function AdminPostList({ posts = [] }) {
  return (
    <AppLayout>
      <Head title="Monitoring Semua Artikel" />
      <div className="flex items-center justify-between mb-6">
        <h1 className="text-2xl font-extrabold text-slate-900">Monitoring Artikel Sistem</h1>
        <a
          href="/admin/posts/export"
          className="cursor-pointer bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-800"
        >
          Ekspor Semua ke Excel
        </a>
      </div>

      <div className="bg-white border border-slate-200 rounded-xl overflow-x-auto shadow-sm">
        <table className="w-full text-left border-collapse">
          <caption className="sr-only">Tabel seluruh artikel pengguna</caption>
          <thead className="bg-slate-50 border-b border-slate-200 text-xs uppercase text-slate-700">
            <tr>
              <th scope="col" className="px-6 py-3.5 font-bold">Judul Artikel</th>
              <th scope="col" className="px-6 py-3.5 font-bold">Penulis</th>
              <th scope="col" className="px-6 py-3.5 font-bold">Kategori</th>
              <th scope="col" className="px-6 py-3.5 font-bold">Status</th>
            </tr>
          </thead>
          <tbody className="divide-y divide-slate-200 text-sm">
            {posts.map((post) => (
              <tr key={post.id} className="hover:bg-slate-50">
                <td className="px-6 py-4 font-semibold text-slate-900">{post.title}</td>
                <td className="px-6 py-4 text-slate-700">{post.user?.name}</td>
                <td className="px-6 py-4 text-slate-700">{post.category}</td>
                <td className="px-6 py-4">
                  <span className={`px-2.5 py-0.5 rounded-full text-xs font-bold ${post.is_published ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'}`}>
                    {post.is_published ? 'Published' : 'Draft'}
                  </span>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </AppLayout>
  );
}