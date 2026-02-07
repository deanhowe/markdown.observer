import { Head, Link, router } from '@inertiajs/react'
import { useState } from 'react'

interface Doc {
  id: number
  file_path: string
  content: string
  is_edited: boolean
}

interface PackageDocsProps {
  package: {
    package_name: string
    version: string
  }
  docs: Doc[]
}

export default function PackageDocs({ package: pkg, docs }: PackageDocsProps) {
  const [selectedDoc, setSelectedDoc] = useState(docs[0])
  const [isEditing, setIsEditing] = useState(false)
  const [content, setContent] = useState(selectedDoc?.content || '')

  const handleSave = () => {
    router.post(route('docs.update', selectedDoc.id), {
      content,
    }, {
      onSuccess: () => setIsEditing(false),
    })
  }

  return (
    <>
      <Head title={`${pkg.package_name} Docs`} />
      
      <div className="flex h-screen">
        {/* Sidebar */}
        <div className="w-64 border-r bg-gray-50 overflow-y-auto">
          <div className="p-4 border-b">
            <Link href={route('dashboard')} className="text-sm text-blue-500 hover:underline">
              ← Back to Dashboard
            </Link>
            <h2 className="font-bold mt-2">{pkg.package_name}</h2>
            <p className="text-sm text-gray-500">{pkg.version}</p>
          </div>
          
          <div className="p-2">
            {docs.map(doc => (
              <button
                key={doc.id}
                onClick={() => {
                  setSelectedDoc(doc)
                  setContent(doc.content)
                  setIsEditing(false)
                }}
                className={`w-full text-left px-3 py-2 rounded text-sm hover:bg-gray-100 ${
                  selectedDoc?.id === doc.id ? 'bg-gray-200' : ''
                }`}
              >
                {doc.file_path}
                {doc.is_edited && <span className="text-orange-500 ml-1">*</span>}
              </button>
            ))}
          </div>
        </div>

        {/* Content */}
        <div className="flex-1 flex flex-col">
          <div className="border-b p-4 flex justify-between items-center">
            <h3 className="font-medium">{selectedDoc?.file_path}</h3>
            <div className="flex gap-2">
              {isEditing ? (
                <>
                  <button
                    onClick={handleSave}
                    className="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
                  >
                    Save
                  </button>
                  <button
                    onClick={() => {
                      setContent(selectedDoc.content)
                      setIsEditing(false)
                    }}
                    className="px-4 py-2 border rounded hover:bg-gray-50"
                  >
                    Cancel
                  </button>
                </>
              ) : (
                <button
                  onClick={() => setIsEditing(true)}
                  className="px-4 py-2 border rounded hover:bg-gray-50"
                >
                  Edit
                </button>
              )}
            </div>
          </div>

          <div className="flex-1 overflow-y-auto p-6">
            {isEditing ? (
              <textarea
                value={content}
                onChange={e => setContent(e.target.value)}
                className="w-full h-full p-4 border rounded font-mono text-sm"
              />
            ) : (
              <div className="prose max-w-none">
                <pre className="whitespace-pre-wrap">{selectedDoc?.content}</pre>
              </div>
            )}
          </div>
        </div>
      </div>
    </>
  )
}
