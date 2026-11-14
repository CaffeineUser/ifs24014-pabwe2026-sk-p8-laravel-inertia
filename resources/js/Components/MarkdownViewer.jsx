import React, { useEffect, useRef } from 'react';
import Viewer from '@toast-ui/editor/dist/toastui-editor-viewer';
import '@toast-ui/editor/dist/toastui-editor-viewer.css';

export default function MarkdownViewer({ content = '' }) {
  const viewerRef = useRef(null);

  useEffect(() => {
    const viewer = new Viewer({
      el: viewerRef.current,
      initialValue: content,
    });
    return () => viewer.destroy();
  }, [content]);

  return <div ref={viewerRef} className="prose prose-slate max-w-none" />;
}