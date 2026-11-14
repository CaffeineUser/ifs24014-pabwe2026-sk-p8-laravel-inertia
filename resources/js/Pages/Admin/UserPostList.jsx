// resources/js/Pages/Admin/UserPostList.jsx
import React from 'react';
import { Head, Link } from '@inertiajs/react';
import AppLayout from '../../Layouts/AppLayout';

export default function UserPostList({ user, posts = [] }) {
  return (
    <AppLayout>
      <Head title={`Artikel milik ${user.name}`} />
      <div className="mb-4">
        <Link href="/admin/users" className="text-sm font-semibold text-blue-700 hover:underline">← Kembali ke Pengguna</Link>
      </div>
      <div className="flex items-center justify-between mb-6">
        <h1 className="text-2xl font-extrabold text-slate-900">Artikel oleh {user.name}</h1>
        <a
          href={`/admin/users/${user.id}/posts/export`}
          className="cursor-pointer bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-800"
        >
          Ekspor Artikel User ke Excel
        </a>
      </div>

      <div className="bg-white border border-slate-200 rounded-xl divide-y divide-slate-200 shadow-sm">
        {posts.map((p) => (
          <div key={p.id} className="p-4 sm:px-6 flex justify-between items-center">
            <span className="font-semibold text-slate-900">{p.title}</span>
            <span className="text-xs font-bold text-slate-600">{p.category}</span>
          </div>
        ))}
      </div>
    </AppLayout>
  );
}