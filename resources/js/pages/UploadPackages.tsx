import { Head, useForm } from '@inertiajs/react'

export default function UploadPackages() {
  const { data, setData, post, processing } = useForm({
    file: null as File | null,
  })

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault()
    post(route('packages.upload'))
  }

  return (
    <>
      <Head title="Upload Packages" />
      
      <div className="max-w-2xl mx-auto p-6">
        <h1 className="text-2xl font-bold mb-6">Upload Your Package File</h1>
        
        <form onSubmit={handleSubmit} className="space-y-6">
          <div>
            <label className="block text-sm font-medium mb-2">
              composer.json or package.json
            </label>
            <input
              type="file"
              accept=".json"
              onChange={e => setData('file', e.target.files?.[0] || null)}
              className="block w-full border rounded p-2"
            />
          </div>

          <button
            type="submit"
            disabled={processing || !data.file}
            className="px-6 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 disabled:opacity-50"
          >
            {processing ? 'Analyzing...' : 'Upload & Analyze'}
          </button>
        </form>

        <div className="mt-8 p-4 bg-gray-50 rounded">
          <h2 className="font-medium mb-2">What happens next?</h2>
          <ol className="list-decimal list-inside space-y-1 text-sm text-gray-600">
            <li>We analyze your dependencies</li>
            <li>Fetch documentation for each package</li>
            <li>You can edit docs locally</li>
            <li>Sync with upstream anytime</li>
          </ol>
        </div>
      </div>
    </>
  )
}
