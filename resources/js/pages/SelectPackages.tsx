import { Head, useForm } from '@inertiajs/react'
import { useState } from 'react'

interface Package {
  name: string
  version: string
  type: string
}

interface SelectPackagesProps {
  packages: Package[]
  limits: {
    can_add_more: boolean
    current_count: number
    limit: number
  }
}

export default function SelectPackages({ packages, limits }: SelectPackagesProps) {
  const [selected, setSelected] = useState<string[]>([])
  const { post, processing } = useForm()

  const togglePackage = (packageData: Package) => {
    const name = packageData.name
    if (selected.includes(name)) {
      setSelected(selected.filter(n => n !== name))
    } else {
      if (selected.length >= limits.limit - limits.current_count) {
        alert(`Free tier limited to ${limits.limit} packages total`)
        return
      }
      setSelected([...selected, name])
    }
  }

  const handleSubmit = () => {
    const selectedPackages = packages
      .filter(pkg => selected.includes(pkg.name))
      .map(pkg => ({ name: pkg.name, version: pkg.version, type: pkg.type }))
    
    post(route('packages.confirm'), {
      data: { packages: selectedPackages },
    })
  }

  return (
    <>
      <Head title="Select Packages" />
      
      <div className="max-w-4xl mx-auto p-6">
        <div className="mb-8">
          <h1 className="text-3xl font-bold dark:text-white mb-3 bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
            Select Packages to Track
          </h1>
          <p className="text-gray-600 dark:text-gray-400">
            Found {packages.length} packages. 
            You can select {limits.limit - limits.current_count} more 
            ({limits.current_count}/{limits.limit} used).
          </p>
          {!limits.can_add_more && (
            <div className="mt-4 p-4 bg-gradient-to-r from-red-50 to-pink-50 dark:from-red-900/20 dark:to-pink-900/20 border border-red-200 dark:border-red-800 rounded-xl backdrop-blur-sm">
              <p className="text-red-600 dark:text-red-400 font-medium">
                Package limit reached. Upgrade to add more!
              </p>
            </div>
          )}
        </div>

        <div className="space-y-3 mb-8">
          {packages.map(pkg => (
            <label
              key={pkg.name}
              className="flex items-center p-5 bg-white/50 dark:bg-gray-800/50 backdrop-blur-sm border border-gray-200 dark:border-gray-700 rounded-xl hover:shadow-lg hover:scale-[1.02] cursor-pointer transition-all duration-200"
            >
              <input
                type="checkbox"
                checked={selected.includes(pkg.name)}
                onChange={() => togglePackage(pkg)}
                className="mr-4 w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
              />
              <div className="flex-1">
                <div className="font-semibold dark:text-white">{pkg.name}</div>
                <div className="text-sm text-gray-500 dark:text-gray-400 mt-1">
                  {pkg.version} • {pkg.type}
                </div>
              </div>
            </label>
          ))}
        </div>

        <div className="flex gap-4">
          <button
            onClick={handleSubmit}
            disabled={processing || selected.length === 0}
            className="px-8 py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-xl hover:shadow-lg hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100 transition-all duration-200 font-medium"
          >
            {processing ? 'Adding...' : `Add ${selected.length} Package${selected.length !== 1 ? 's' : ''}`}
          </button>
          
          <a
            href={route('dashboard')}
            className="px-8 py-3 bg-white/50 dark:bg-gray-800/50 backdrop-blur-sm border border-gray-200 dark:border-gray-700 rounded-xl hover:shadow-lg hover:scale-105 transition-all duration-200 dark:text-white font-medium"
          >
            Cancel
          </a>
        </div>

        <div className="mt-8 p-6 bg-gradient-to-br from-blue-50 via-purple-50 to-pink-50 dark:from-blue-900/20 dark:via-purple-900/20 dark:to-pink-900/20 border border-blue-200 dark:border-blue-800 rounded-2xl backdrop-blur-sm shadow-lg">
          <h3 className="text-lg font-bold mb-4 dark:text-white bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
            Upgrade for More Packages
          </h3>
          <div className="space-y-3 text-sm">
            <div className="flex items-start gap-3">
              <svg className="w-5 h-5 text-gray-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
              </svg>
              <div>
                <span className="font-medium dark:text-white">Free:</span>
                <span className="text-gray-600 dark:text-gray-400"> 2 uploads, 10 packages</span>
              </div>
            </div>
            <div className="flex items-start gap-3">
              <svg className="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
              </svg>
              <div>
                <span className="font-medium dark:text-white">Pro (£9/mo):</span>
                <span className="text-gray-600 dark:text-gray-400"> 100 packages</span>
              </div>
            </div>
            <div className="flex items-start gap-3">
              <svg className="w-5 h-5 text-purple-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
              </svg>
              <div>
                <span className="font-medium dark:text-white">Lifetime (£299):</span>
                <span className="text-gray-600 dark:text-gray-400"> Unlimited packages</span>
              </div>
            </div>
          </div>
          <a href={route('pricing')} className="inline-block mt-4 text-blue-600 dark:text-blue-400 hover:underline font-medium">
            View Pricing →
          </a>
        </div>
      </div>
    </>
  )
}
