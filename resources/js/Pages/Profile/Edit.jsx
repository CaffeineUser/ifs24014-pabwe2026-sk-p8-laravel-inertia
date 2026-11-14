import React from 'react';
import { Head, useForm, usePage } from '@inertiajs/react';
import AppLayout from '../../Layouts/AppLayout';

export default function ProfileEdit() {
  const { auth } = usePage().props;
  const { data, setData, put, processing, errors } = useForm({
    name: auth.user.name || '',
    email: auth.user.email || '',
  });

  const handleSubmit = (e) => {
    e.preventDefault();
    put('/profile');
  };

  return (
    <AppLayout>
      <Head title="Edit Profil Penulis" />
      <h1 className="text-2xl font-extrabold text-slate-900 mb-6">Edit Profil</h1>

      <form onSubmit={handleSubmit} className="max-w-lg bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
        <div>
          <label htmlFor="prof-name" className="block text-sm font-bold text-slate-800 mb-1">Nama Lengkap</label>
          <input
            id="prof-name"
            type="text"
            value={data.name}
            onChange={(e) => setData('name', e.target.value)}
            required
            className="w-full px-3.5 py-2 border border-slate-300 rounded-lg"
          />
          {errors.name && <p role="alert" className="text-sm text-red-700 mt-1">{errors.name}</p>}
        </div>

        <div>
          <label htmlFor="prof-email" className="block text-sm font-bold text-slate-800 mb-1">Email</label>
          <input
            id="prof-email"
            type="email"
            value={data.email}
            onChange={(e) => setData('email', e.target.value)}
            required
            className="w-full px-3.5 py-2 border border-slate-300 rounded-lg"
          />
          {errors.email && <p role="alert" className="text-sm text-red-700 mt-1">{errors.email}</p>}
        </div>

        <button
          type="submit"
          disabled={processing}
          className="cursor-pointer bg-blue-700 text-white px-5 py-2 rounded-lg text-sm font-semibold hover:bg-blue-800"
        >
          Simpan Perubahan
        </button>
      </form>
    </AppLayout>
  );
}