// resources/js/Pages/Admin/UserList.jsx
import React from 'react';
import { Head, Link } from '@inertiajs/react';
import AppLayout from '../../Layouts/AppLayout';

export default function UserList({ users = [] }) {
  return (
    <AppLayout>
      <Head title="Kelola Pengguna" />
      <h1 className="text-2xl font-extrabold text-slate-900 mb-6">Daftar Pengguna</h1>

      <div className="bg-white border border-slate-200 rounded-xl overflow-x-auto shadow-sm">
        <table className="w-full text-left border-collapse">
          <caption className="sr-only">Daftar pengguna terdaftar</caption>
          <thead className="bg-slate-50 border-b border-slate-200 text-xs uppercase text-slate-700">
            <tr>
              <th scope="col" className="px-6 py-3.5 font-bold">Nama</th>
              <th scope="col" className="px-6 py-3.5 font-bold">Username</th>
              <th scope="col" className="px-6 py-3.5 font-bold">Role</th>
              <th scope="col" className="px-6 py-3.5 font-bold">Aksi</th>
            </tr>
          </thead>
          <tbody className="divide-y divide-slate-200 text-sm">
            {users.map((u) => (
              <tr key={u.id}>
                <td className="px-6 py-4 font-semibold text-slate-900">{u.name}</td>
                <td className="px-6 py-4 text-slate-700">{u.username}</td>
                <td className="px-6 py-4 text-slate-700">{u.role}</td>
                <td className="px-6 py-4">
                  <Link
                    href={`/admin/users/${u.id}/posts`}
                    className="text-blue-700 font-semibold hover:underline"
                  >
                    Lihat Artikel
                  </Link>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </AppLayout>
  );
}