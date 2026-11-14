import React from 'react';
import { Link, usePage, router } from '@inertiajs/react';
import LoadingIndicator from '../Components/LoadingIndicator';

export default function AppLayout({ children }) {
  const { auth } = usePage().props;
  const isAdmin = auth?.user?.role === 'admin';

  const handleLogout = (e) => {
    e.preventDefault();
    router.post('/logout');
  };

  return (
    <div className="min-h-screen flex bg-slate-100 text-slate-900">
      <LoadingIndicator />

      {/* Sidebar Navigasi */}
      <aside className="w-64 bg-slate-900 text-slate-100 flex flex-col shrink-0" aria-label="Sidebar Menu">
        <div className="h-16 flex items-center px-6 border-b border-slate-800">
          <Link href="/" className="text-lg font-extrabold text-white tracking-tight">
            BlogVerse Pro
          </Link>
        </div>

        <nav className="flex-1 p-4 space-y-1.5 text-sm font-medium">
          <Link href="/" className="block px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white">
            ← Beranda Publik
          </Link>

          {!isAdmin && (
            <Link href="/dashboard/posts" className="block px-3 py-2.5 rounded-lg bg-blue-700 text-white">
              Artikel Saya
            </Link>
          )}

          {isAdmin && (
            <>
              <Link href="/admin/users" className="block px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white">
                Kelola Pengguna
              </Link>
              <Link href="/admin/posts" className="block px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white">
                Monitoring Semua Artikel
              </Link>
            </>
          )}

          <Link href="/profile" className="block px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white">
            Profil Penulis
          </Link>
        </nav>

        <div className="p-4 border-t border-slate-800">
          <button
            type="button"
            onClick={handleLogout}
            className="cursor-pointer w-full text-left px-3 py-2 rounded-lg text-sm font-semibold text-red-400 hover:bg-slate-800"
          >
            Keluar (Logout)
          </button>
        </div>
      </aside>

      {/* Main Workspace */}
      <div className="flex-1 flex flex-col min-w-0">
        <header className="h-16 bg-white border-b border-slate-200 px-6 flex items-center justify-between">
          <span className="text-sm font-semibold text-slate-600">
            Halo, <strong className="text-slate-900">{auth?.user?.name}</strong> ({auth?.user?.role})
          </span>
        </header>
        <main id="main-content" className="flex-1 p-6 max-w-7xl w-full mx-auto">
          {children}
        </main>
      </div>
    </div>
  );
}