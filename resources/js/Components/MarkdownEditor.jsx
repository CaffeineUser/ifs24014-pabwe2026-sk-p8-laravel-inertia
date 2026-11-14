import React, { useEffect, useRef } from 'react';
import Editor from '@toast-ui/editor';
import '@toast-ui/editor/dist/toastui-editor.css';

export default function MarkdownEditor({ initialValue = '', onChange }) {
  const editorRef = useRef(null);
  const instanceRef = useRef(null);

  useEffect(() => {
    instanceRef.current = new Editor({
      el: editorRef.current,
      height: '380px',
      initialEditType: 'markdown',
      previewStyle: 'vertical',
      initialValue: initialValue,
      events: {
        change: () => {
          onChange(instanceRef.current.getMarkdown());
        },
      },
    });

    return () => {
      if (instanceRef.current) {
        instanceRef.current.destroy();
      }
    };
  }, []);

  return <div ref={editorRef} aria-label="Editor konten Markdown" />;
}