import '../css/app.css';
import { createInertiaApp } from '@inertiajs/react';
import { createRoot } from 'react-dom/client';

const el = typeof document !== 'undefined' ? document.getElementById('app') : null;
const scriptEl = typeof document !== 'undefined'
  ? document.querySelector('script[data-page="app"][type="application/json"]')
  : null;

const initialPage = scriptEl?.textContent
  ? JSON.parse(scriptEl.textContent)
  : (el?.dataset?.page ? JSON.parse(el.dataset.page) : undefined);

createInertiaApp({
  page: initialPage,
  title: (title) => (title ? `${title} - BlogVerse Pro` : 'BlogVerse Pro'),
  resolve: (name) => {
    const pages = import.meta.glob('./Pages/**/*.jsx', { eager: true });
    return pages[`./Pages/${name}.jsx`];
  },
  setup({ el, App, props }) {
    createRoot(el).render(<App {...props} />);
  },
  progress: {
    color: '#2563eb',
    showSpinner: false,
  },
});
