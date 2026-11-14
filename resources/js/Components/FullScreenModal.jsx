import React, { useEffect, useRef } from 'react';

export default function FullScreenModal({ isOpen, onClose, title, children, footerActions }) {
  const closeBtnRef = useRef(null);
  const onCloseRef = useRef(onClose);
  onCloseRef.current = onClose;
  const wasOpenRef = useRef(false);

  useEffect(() => {
    const handleKeyDown = (e) => {
      if (e.key === 'Escape' && isOpen) {
        onCloseRef.current?.();
      }
    };

    if (isOpen) {
      document.body.style.overflow = 'hidden';
      // Hanya fokuskan ke tombol close satu kali saat modal baru dibuka
      if (!wasOpenRef.current) {
        closeBtnRef.current?.focus();
        wasOpenRef.current = true;
      }
    } else {
      document.body.style.overflow = 'unset';
      wasOpenRef.current = false;
    }

    window.addEventListener('keydown', handleKeyDown);
    return () => {
      document.body.style.overflow = 'unset';
      window.removeEventListener('keydown', handleKeyDown);
    };
  }, [isOpen]);

  if (!isOpen) return null;

  return (
    <div
      role="dialog"
      aria-modal="true"
      aria-labelledby="modal-title"
      className="fixed inset-0 z-50 flex flex-col bg-white"
    >
      {/* Distinct Colored Header */}
      <header className="bg-slate-900 text-white px-6 py-4 flex items-center justify-between border-b border-slate-800 shrink-0">
        <h2 id="modal-title" className="text-lg font-bold tracking-tight">
          {title}
        </h2>
        <button
          ref={closeBtnRef}
          type="button"
          onClick={() => onCloseRef.current?.()}
          aria-label="Tutup dialog"
          className="cursor-pointer p-2 rounded-lg text-slate-300 hover:text-white hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-400"
        >
          <span aria-hidden="true" className="text-xl leading-none">×</span>
        </button>
      </header>

      {/* Scrollable Body */}
      <div className="flex-1 overflow-y-auto p-6 bg-slate-50">
        <div className="max-w-4xl mx-auto bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
          {children}
        </div>
      </div>

      {/* Distinct Colored Footer with Right-Aligned Actions */}
      <footer className="bg-slate-100 border-t border-slate-300 px-6 py-4 flex items-center justify-end gap-3 shrink-0">
        {footerActions}
      </footer>
    </div>
  );
}
