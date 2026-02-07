import { useState } from 'react'
import { Head, useForm } from '@inertiajs/react'
import MarkdownEditor from '@/components/MarkdownEditor'

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

        {mode === 'markdown' ? (
          <textarea
            value={data.content}
            onChange={e => setData('content', e.target.value)}
            className="w-full h-96 p-4 border rounded font-mono"
          />
        ) : (
          <MarkdownEditor
            content={data.content}
            onChange={(html, markdown) => setData('content', markdown)}
          />
        )}
      </div>
    </>
  )
}
