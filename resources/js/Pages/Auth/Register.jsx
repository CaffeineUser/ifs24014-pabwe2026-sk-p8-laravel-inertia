import React from 'react';
import { Head, Link, useForm } from '@inertiajs/react';

export default function Register() {
  const { data, setData, post, processing, errors } = useForm({
    name: '',
    username: '',
    email: '',
    password: '',
    password_confirmation: '',
  });

  const handleSubmit = (e) => {
    e.preventDefault();
    post('/register');
  };

  return (
    <main className="min-h-screen flex items-center justify-center bg-slate-100 px-4 py-8">
      <Head>
        <title>Daftar Penulis Baru</title>
        <meta name="description" content="Buat akun penulis di BlogVerse Pro." />
      </Head>

      <div className="w-full max-w-md bg-white rounded-2xl shadow-md border border-slate-200 p-8">
        <h1 className="text-2xl font-extrabold text-slate-900 mb-2">Daftar Akun Penulis</h1>
        <p className="text-sm text-slate-600 mb-6">Bergabung dan mulai bagikan artikel Anda.</p>

        <form onSubmit={handleSubmit} className="space-y-4">
          <div>
            <label htmlFor="reg-name" className="block text-sm font-semibold text-slate-800 mb-1">Nama Lengkap</label>
            <input
              id="reg-name"
              type="text"
              value={data.name}
              onChange={(e) => setData('name', e.target.value)}
              required
              className="w-full px-3.5 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-600"
            />
            {errors.name && <p role="alert" className="text-sm text-red-700 mt-1">{errors.name}</p>}
          </div>

          <div>
            <label htmlFor="reg-username" className="block text-sm font-semibold text-slate-800 mb-1">Username</label>
            <input
              id="reg-username"
              type="text"
              value={data.username}
              onChange={(e) => setData('username', e.target.value)}
              required
              className="w-full px-3.5 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-600"
            />
            {errors.username && <p role="alert" className="text-sm text-red-700 mt-1">{errors.username}</p>}
          </div>

          <div>
            <label htmlFor="reg-email" className="block text-sm font-semibold text-slate-800 mb-1">Email</label>
            <input
              id="reg-email"
              type="email"
              value={data.email}
              onChange={(e) => setData('email', e.target.value)}
              required
              className="w-full px-3.5 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-600"
            />
            {errors.email && <p role="alert" className="text-sm text-red-700 mt-1">{errors.email}</p>}
          </div>

          <div>
            <label htmlFor="reg-password" className="block text-sm font-semibold text-slate-800 mb-1">Password</label>
            <input
              id="reg-password"
              type="password"
              value={data.password}
              onChange={(e) => setData('password', e.target.value)}
              required
              className="w-full px-3.5 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-600"
            />
            {errors.password && <p role="alert" className="text-sm text-red-700 mt-1">{errors.password}</p>}
          </div>

          <div>
            <label htmlFor="reg-password-conf" className="block text-sm font-semibold text-slate-800 mb-1">Konfirmasi Password</label>
            <input
              id="reg-password-conf"
              type="password"
              value={data.password_confirmation}
              onChange={(e) => setData('password_confirmation', e.target.value)}
              required
              className="w-full px-3.5 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-600"
            />
          </div>

          <button
            type="submit"
            disabled={processing}
            className="cursor-pointer w-full bg-blue-700 text-white py-2.5 rounded-lg font-semibold hover:bg-blue-800 transition"
          >
            {processing ? 'Mendaftarkan...' : 'Daftar'}
          </button>
        </form>

        <p className="mt-6 text-center text-sm text-slate-600">
          Sudah punya akun?{' '}
          <Link href="/login" className="text-blue-700 font-semibold hover:underline">
            Masuk di sini
          </Link>
        </p>
      </div>
    </main>
  );
}