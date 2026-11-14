import React, { useEffect, useState } from 'react';
import { router } from '@inertiajs/react';

export default function LoadingIndicator() {
  const [isProcessing, setIsProcessing] = useState(false);

  useEffect(() => {
    const removeStart = router.on('start', () => setIsProcessing(true));
    const removeFinish = router.on('finish', () => setIsProcessing(false));
    return () => {
      removeStart();
      removeFinish();
    };
  }, []);

  if (!isProcessing) return null;

  return (
    <div
      role="status"
      aria-live="polite"
      className="fixed bottom-5 right-5 z-50 flex items-center gap-3 bg-slate-900 text-white px-4 py-2.5 rounded-full shadow-xl border border-slate-700 text-sm font-medium animate-fade-in"
    >
      <svg className="animate-spin h-4 w-4 text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
        <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" />
        <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z" />
      </svg>
      <span>Memproses tindakan...</span>
    </div>
  );
}