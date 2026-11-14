import React from 'react';
import { Head, Link, useForm } from '@inertiajs/react';

export default function Login() {
  const { data, setData, post, processing, errors } = useForm({
    username: '',
    password: '',
  });

  const handleSubmit = (e) => {
    e.preventDefault();
    post('/login');
  };

  return (
    <main className="min-h-screen flex items-center justify-center bg-slate-100 px-4">
      <Head>
        <title>Login Penulis & Admin</title>
        <meta name="description" content="Masuk ke akun BlogVerse Pro untuk menulis dan mengelola artikel." />
      </Head>

      <div className="w-full max-w-md bg-white rounded-2xl shadow-md border border-slate-200 p-8">
        <h1 className="text-2xl font-extrabold text-slate-900 mb-2">Masuk ke Akun</h1>
        <p className="text-sm text-slate-600 mb-6">Silakan masukkan kredensial Anda untuk melanjutkan.</p>

        <form onSubmit={handleSubmit} className="space-y-4">
          <div>
            <label htmlFor="login-username" className="block text-sm font-semibold text-slate-800 mb-1">
              Username
            </label>
            <input
              id="login-username"
              type="text"
              value={data.username}
              onChange={(e) => setData('username', e.target.value)}
              required
              autoComplete="username"
              className="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-600"
            />
            {errors.username && <p role="alert" className="text-sm text-red-700 mt-1">{errors.username}</p>}
          </div>

          <div>
            <label htmlFor="login-password" className="block text-sm font-semibold text-slate-800 mb-1">
              Password
            </label>
            <input
              id="login-password"
              type="password"
              value={data.password}
              onChange={(e) => setData('password', e.target.value)}
              required
              autoComplete="current-password"
              className="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-600"
            />
            {errors.password && <p role="alert" className="text-sm text-red-700 mt-1">{errors.password}</p>}
          </div>

          <button
            id="login-submit"
            type="submit"
            disabled={processing}
            className="cursor-pointer w-full bg-blue-700 text-white py-2.5 rounded-lg font-semibold hover:bg-blue-800 disabled:opacity-60 transition"
          >
            {processing ? 'Memproses...' : 'Masuk'}
          </button>
        </form>

        <div className="mt-6 text-center text-sm text-slate-600 space-y-2">
          <p>
            Belum punya akun?{' '}
            <Link href="/register" className="text-blue-700 font-semibold hover:underline">
              Daftar Sekarang
            </Link>
          </p>
          <p>
            <Link href="/" className="text-slate-600 hover:underline">
              ← Kembali ke Beranda Publik
            </Link>
          </p>
        </div>
      </div>
    </main>
  );
}