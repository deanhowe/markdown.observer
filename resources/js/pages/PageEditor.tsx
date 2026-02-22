import { useState, useEffect } from 'react'
import { Head, useForm } from '@inertiajs/react'
import MarkdownEditor from '@/components/MarkdownEditor'
import axios from 'axios'

interface PageEditorProps {
  page?: {
    slug: string
    content: string
  }
}

export default function PageEditor({ page }: PageEditorProps) {
  const [mode, setMode] = useState<'markdown' | 'rich'>('rich')
  const { data, setData, post, processing } = useForm({
    slug: page?.slug || '',
    content: page?.content || '',
  })

  const handleSave = () => {
    post(route('pages.store'))
  }

  const exportForAi = async () => {
    try {
      const response = await axios.post(route('api.markdown.export-for-ai'), { markdown: data.content });
      await navigator.clipboard.writeText(response.data.markdown);
      alert('Optimized markdown copied to clipboard!');
    } catch (error) {
      console.error('Export error:', error);
      alert('Failed to export for AI');
    }
  }

  return (
    <>
      <Head title={page ? `Edit ${page.slug}` : 'New Page'} />

      <div className="max-w-4xl mx-auto p-6">
        <div className="flex justify-between items-center mb-6">
          <h1 className="text-2xl font-bold">
            {page ? `Edit ${page.slug}` : 'New Page'}
          </h1>

          <div className="flex gap-2">
            <button
              onClick={exportForAi}
              className="px-4 py-2 bg-purple-500 text-white rounded hover:bg-purple-600"
              title="Copy optimized markdown for AI"
            >
              Export for AI
            </button>
            <button
              onClick={() => setMode('markdown')}
              className={`px-4 py-2 rounded ${mode === 'markdown' ? 'bg-blue-500 text-white' : 'bg-gray-200'}`}
            >
              Markdown
            </button>
            <button
              onClick={() => setMode('rich')}
              className={`px-4 py-2 rounded ${mode === 'rich' ? 'bg-blue-500 text-white' : 'bg-gray-200'}`}
            >
              Rich Text
            </button>
            <button
              onClick={handleSave}
              disabled={processing}
              className="px-6 py-2 bg-green-500 text-white rounded hover:bg-green-600 disabled:opacity-50"
            >
              Save
            </button>
          </div>
        </div>

        <input
          type="text"
          value={data.slug}
          onChange={e => setData('slug', e.target.value)}
          placeholder="page-slug"
          className="w-full mb-4 px-4 py-2 border rounded"
        />

        <div className="flex gap-6 h-[600px]">
          <div className="flex-1 flex flex-col">
            {mode === 'markdown' ? (
              <textarea
                value={data.content}
                onChange={e => setData('content', e.target.value)}
                className="w-full h-full p-4 border rounded font-mono"
                placeholder="Write your markdown here..."
              />
            ) : (
              <div className="h-full overflow-y-auto">
                <MarkdownEditor
                  content={data.content}
                  onChange={(html, markdown) => setData('content', markdown)}
                />
              </div>
            )}
          </div>

          {mode === 'markdown' && (
            <div className="flex-1 border rounded bg-gray-50 dark:bg-gray-800 p-6 overflow-y-auto">
              <h2 className="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4 border-b pb-2">Preview</h2>
              <div className="prose prose-blue dark:prose-invert max-w-none">
                <PreviewContent markdown={data.content} />
              </div>
            </div>
          )}
        </div>
      </div>
    </>
  )
}

function PreviewContent({ markdown }: { markdown: string }) {
  const [html, setHtml] = useState('');
  const [isPending, setIsPending] = useState(false);

  useEffect(() => {
    if (!markdown) {
      setHtml('');
      return;
    }

    const timer = setTimeout(async () => {
      setIsPending(true);
      try {
        const response = await axios.post('/api/markdown/to-html', { markdown });
        setHtml(response.data.html);
      } catch (error) {
        console.error('Markdown preview error:', error);
      } finally {
        setIsPending(false);
      }
    }, 300); // 300ms debounce

    return () => clearTimeout(timer);
  }, [markdown]);

  return (
    <div className={isPending ? 'opacity-50 transition-opacity' : 'transition-opacity'}>
      <div dangerouslySetInnerHTML={{ __html: html }} />
      {!html && !isPending && <p className="text-gray-400 italic">No content to preview.</p>}
    </div>
  );
}
