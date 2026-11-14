import React from 'react';
import { Link, usePage } from '@inertiajs/react';
import LoadingIndicator from '../Components/LoadingIndicator';

export default function PublicLayout({ children }) {
  const { auth } = usePage().props;

  return (
    <div className="min-h-screen flex flex-col bg-slate-50 text-slate-900">
      <a
        href="#main-content"
        className="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-50 focus:bg-blue-700 focus:text-white focus:px-4 focus:py-2 focus:rounded-lg"
      >
        Langsung ke konten utama
      </a>

      <LoadingIndicator />

      {/* Sticky Top Navbar */}
      <header className="sticky top-0 z-40 bg-white/95 backdrop-blur border-b border-slate-200">
        <nav className="max-w-6xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between" aria-label="Navigasi Publik">
          <div className="flex items-center gap-6">
            <Link href="/" className="text-xl font-extrabold text-blue-700 tracking-tight">
              BlogVerse Pro
            </Link>
            <Link href="/articles" className="text-sm font-semibold text-slate-700 hover:text-blue-700">
              Jelajahi Artikel
            </Link>
          </div>

          <div className="flex items-center gap-3">
            {auth?.user ? (
              <Link
                href={auth.user.role === 'admin' ? '/admin/posts' : '/dashboard/posts'}
                className="cursor-pointer bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-800 transition"
              >
                {auth.user.role === 'admin' ? 'Panel Admin' : 'Dashboard Penulis'}
              </Link>
            ) : (
              <>
                <Link
                  href="/login"
                  className="cursor-pointer text-sm font-semibold text-slate-700 hover:text-blue-700 px-3 py-2 rounded-lg"
                >
                  Masuk (Login)
                </Link>
                <Link
                  href="/register"
                  className="cursor-pointer bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-800 transition"
                >
                  Daftar (Register)
                </Link>
              </>
            )}
          </div>
        </nav>
      </header>

      <main id="main-content" className="flex-1 w-full max-w-6xl mx-auto px-4 sm:px-6 py-8">
        {children}
      </main>

      {/* Footer Editorial */}
      <footer className="bg-white border-t border-slate-200 mt-12">
        <div className="max-w-6xl mx-auto px-4 sm:px-6 py-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-slate-600">
          <p className="font-medium">© 2026 BlogVerse Pro. Platform Publikasi Artikel Multi-Tier.</p>
          <nav aria-label="Navigasi Footer" className="flex items-center gap-6 font-semibold">
            <Link href="/" className="hover:text-blue-700">Beranda Publik</Link>
            {!auth?.user && (
              <>
                <Link href="/login" className="hover:text-blue-700">Halaman Login</Link>
                <Link href="/register" className="hover:text-blue-700">Halaman Register</Link>
              </>
            )}
            {auth?.user && (
              <Link
                href={auth.user.role === 'admin' ? '/admin/posts' : '/dashboard/posts'}
                className="hover:text-blue-700"
              >
                {auth.user.role === 'admin' ? 'Panel Admin' : 'Dashboard Penulis'}
              </Link>
            )}
          </nav>
        </div>
      </footer>
    </div>
  );
}