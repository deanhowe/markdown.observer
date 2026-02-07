import { Head, useForm } from '@inertiajs/react'
import { useState } from 'react'

export default function UploadPackages() {
  const { data, setData, post, processing, progress } = useForm({
    file: null as File | null,
  })
  
  const [uploadType, setUploadType] = useState<'composer' | 'package' | null>(null)
  const [fileName, setFileName] = useState<string>('')

  const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>, type: 'composer' | 'package') => {
    const file = e.target.files?.[0]
    if (file) {
      setData('file', file)
      setUploadType(type)
      setFileName(file.name)
    }
  }

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault()
    post(route('packages.upload'))
  }

  return (
    <>
      <Head title="Upload Packages" />
      
      <div className="min-h-screen relative z-10 py-12 px-4">
        <div className="max-w-4xl mx-auto">
          <div className="text-center mb-12">
            <h1 className="text-4xl font-bold bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 bg-clip-text text-transparent mb-4">
              Upload Your Dependencies
            </h1>
            <p className="text-gray-600 dark:text-gray-400">
              Choose your package manager and upload your dependency file
            </p>
          </div>
        
          <form onSubmit={handleSubmit} className="space-y-8">
            {/* Two Upload Boxes */}
            <div className="grid md:grid-cols-2 gap-6">
              {/* Composer Upload */}
              <label className="group cursor-pointer">
                <div className={`
                  relative overflow-hidden rounded-2xl p-8 text-center
                  backdrop-blur-sm bg-white/50 dark:bg-gray-800/50
                  border-2 transition-all duration-300
                  ${uploadType === 'composer' 
                    ? 'border-purple-500 shadow-lg shadow-purple-500/20 scale-[1.02]' 
                    : 'border-gray-200 dark:border-gray-700 hover:border-purple-300 hover:scale-[1.01]'
                  }
                `}>
                  <div className="absolute inset-0 bg-gradient-to-br from-purple-500/10 to-pink-500/10 opacity-0 group-hover:opacity-100 transition-opacity" />
                  
                  <div className="relative">
                    <svg className="w-16 h-16 mx-auto mb-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                    
                    <h3 className="text-xl font-semibold mb-2 text-gray-900 dark:text-white">
                      composer.json
                    </h3>
                    <p className="text-sm text-gray-600 dark:text-gray-400 mb-4">
                      PHP / Laravel projects
                    </p>
                    
                    {uploadType === 'composer' && fileName && (
                      <div className="mt-4 p-3 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                        <p className="text-sm font-medium text-purple-900 dark:text-purple-300 truncate">
                          {fileName}
                        </p>
                      </div>
                    )}
                  </div>
                </div>
                <input
                  type="file"
                  accept=".json"
                  onChange={e => handleFileChange(e, 'composer')}
                  className="hidden"
                />
              </label>

              {/* Package.json Upload */}
              <label className="group cursor-pointer">
                <div className={`
                  relative overflow-hidden rounded-2xl p-8 text-center
                  backdrop-blur-sm bg-white/50 dark:bg-gray-800/50
                  border-2 transition-all duration-300
                  ${uploadType === 'package' 
                    ? 'border-blue-500 shadow-lg shadow-blue-500/20 scale-[1.02]' 
                    : 'border-gray-200 dark:border-gray-700 hover:border-blue-300 hover:scale-[1.01]'
                  }
                `}>
                  <div className="absolute inset-0 bg-gradient-to-br from-blue-500/10 to-purple-500/10 opacity-0 group-hover:opacity-100 transition-opacity" />
                  
                  <div className="relative">
                    <svg className="w-16 h-16 mx-auto mb-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                    
                    <h3 className="text-xl font-semibold mb-2 text-gray-900 dark:text-white">
                      package.json
                    </h3>
                    <p className="text-sm text-gray-600 dark:text-gray-400 mb-4">
                      Node / JavaScript projects
                    </p>
                    
                    {uploadType === 'package' && fileName && (
                      <div className="mt-4 p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                        <p className="text-sm font-medium text-blue-900 dark:text-blue-300 truncate">
                          {fileName}
                        </p>
                      </div>
                    )}
                  </div>
                </div>
                <input
                  type="file"
                  accept=".json"
                  onChange={e => handleFileChange(e, 'package')}
                  className="hidden"
                />
              </label>
            </div>

            {/* Progress Bar */}
            {processing && progress && (
              <div className="backdrop-blur-sm bg-white/50 dark:bg-gray-800/50 rounded-2xl p-6 border border-gray-200 dark:border-gray-700">
                <div className="flex items-center justify-between mb-2">
                  <span className="text-sm font-medium text-gray-700 dark:text-gray-300">
                    Uploading...
                  </span>
                  <span className="text-sm font-medium text-gray-700 dark:text-gray-300">
                    {progress.percentage}%
                  </span>
                </div>
                <div className="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3 overflow-hidden">
                  <div 
                    className="h-full bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500 transition-all duration-300 rounded-full"
                    style={{ width: `${progress.percentage}%` }}
                  />
                </div>
              </div>
            )}

            {/* Submit Button */}
            <button
              type="submit"
              disabled={processing || !data.file}
              className="
                w-full py-4 px-6 rounded-2xl font-semibold text-white
                bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600
                hover:shadow-lg hover:shadow-purple-500/50 hover:scale-[1.02]
                disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100
                transition-all duration-300
              "
            >
              {processing ? 'Analyzing Dependencies...' : 'Upload & Analyze'}
            </button>
          </form>

          {/* Info Box */}
          <div className="mt-8 backdrop-blur-sm bg-white/50 dark:bg-gray-800/50 rounded-2xl p-6 border border-gray-200 dark:border-gray-700">
            <h2 className="font-semibold mb-4 text-gray-900 dark:text-white flex items-center gap-2">
              <svg className="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              What happens next?
            </h2>
            <ol className="space-y-3 text-sm text-gray-600 dark:text-gray-400">
              <li className="flex items-start gap-3">
                <span className="flex-shrink-0 w-6 h-6 rounded-full bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 flex items-center justify-center text-xs font-semibold">1</span>
                <span>We analyze your dependencies and extract package information</span>
              </li>
              <li className="flex items-start gap-3">
                <span className="flex-shrink-0 w-6 h-6 rounded-full bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 flex items-center justify-center text-xs font-semibold">2</span>
                <span>Fetch documentation from GitHub for each package</span>
              </li>
              <li className="flex items-start gap-3">
                <span className="flex-shrink-0 w-6 h-6 rounded-full bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 flex items-center justify-center text-xs font-semibold">3</span>
                <span>Edit and customize documentation locally</span>
              </li>
              <li className="flex items-start gap-3">
                <span className="flex-shrink-0 w-6 h-6 rounded-full bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 flex items-center justify-center text-xs font-semibold">4</span>
                <span>Sync with upstream repositories anytime</span>
              </li>
            </ol>
          </div>
        </div>
      </div>
    </>
  )
}
