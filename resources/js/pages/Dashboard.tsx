import { Head, Link, router } from '@inertiajs/react'

interface Package {
  id: number
  package_name: string
  version: string
  type: string
  last_synced_at: string | null
  docs_count: number
}

interface DashboardProps {
  packages: Package[]
}

export default function Dashboard({ packages }: DashboardProps) {
  const handleSync = (packageName: string) => {
    router.post(route('packages.sync', packageName))
  }

  return (
    <>
      <Head title="Dashboard" />
      
      <div className="max-w-4xl mx-auto p-6">
        <div className="flex justify-between items-center mb-6">
          <h1 className="text-2xl font-bold">Your Packages</h1>
          <Link
            href={route('packages.upload.form')}
            className="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
          >
            Upload Packages
          </Link>
        </div>

        {packages.length === 0 ? (
          <div className="text-center py-16">
            <svg className="w-24 h-24 mx-auto mb-6 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
            <h2 className="text-xl font-semibold mb-2 dark:text-white">No packages yet</h2>
            <p className="text-gray-600 dark:text-gray-400 mb-6">Upload your composer.json or package.json to get started</p>
            <Link
              href={route('packages.upload.form')}
              className="inline-block px-6 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600"
            >
              Upload Your First Package
            </Link>
          </div>
        ) : (
          <div className="space-y-2">
            {packages.map(pkg => (
              <div key={pkg.id} className="p-4 border rounded hover:bg-gray-50 flex justify-between items-center">
                <div className="flex-1">
                  <Link href={route('packages.docs', pkg.package_name)} className="font-medium hover:text-blue-500">
                    {pkg.package_name}
                  </Link>
                  <p className="text-sm text-gray-500">
                    {pkg.version} • {pkg.type} • {pkg.docs_count || 0} docs
                  </p>
                  {pkg.last_synced_at && (
                    <p className="text-xs text-gray-400">
                      Last synced: {new Date(pkg.last_synced_at).toLocaleDateString()}
                    </p>
                  )}
                </div>
                <button
                  onClick={() => handleSync(pkg.package_name)}
                  className="px-3 py-1 text-sm bg-green-500 text-white rounded hover:bg-green-600"
                >
                  Sync
                </button>
              </div>
            ))}
          </div>
        )}
      </div>
    </>
  )
}
