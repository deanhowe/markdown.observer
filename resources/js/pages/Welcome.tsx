import { Head, Link } from '@inertiajs/react'

export default function Welcome() {
  return (
    <>
      <Head title="Your Package Documentation Hub">
        <meta name="description" content="Upload your composer.json or package.json. We fetch docs for all your dependencies. Edit them locally. Keep them in sync." />
        <meta property="og:title" content="Markdown Observer - Your Package Documentation Hub" />
        <meta property="og:description" content="Upload your composer.json or package.json. We fetch docs for all your dependencies. Edit them locally. Keep them in sync." />
        <meta property="og:type" content="website" />
        <meta property="og:url" content="https://markdown.observer" />
        <meta property="og:image" content="https://markdown.observer/og-home.png" />
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" content="Markdown Observer - Your Package Documentation Hub" />
        <meta name="twitter:description" content="Upload your composer.json or package.json. We fetch docs for all your dependencies." />
        <meta name="twitter:image" content="https://markdown.observer/og-home.png" />
      </Head>
      
      <div className="min-h-screen relative">
        {/* Header */}
        <header className="container mx-auto px-4 sm:px-6 py-6 flex justify-between items-center">
          <h1 className="text-xl sm:text-2xl font-bold dark:text-white">Markdown Observer</h1>
          <div className="flex gap-2 sm:gap-4">
            <Link href={route('login')} className="px-3 sm:px-4 py-2 text-sm sm:text-base text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white">
              Login
            </Link>
            <Link href={route('register')} className="px-3 sm:px-4 py-2 text-sm sm:text-base bg-blue-500/90 hover:bg-blue-500 text-white rounded backdrop-blur-sm whitespace-nowrap">
              Get Started
            </Link>
          </div>
        </header>

        {/* Hero */}
        <section className="container mx-auto px-4 sm:px-6 py-12 sm:py-20 text-center">
          <h2 className="text-4xl sm:text-6xl font-bold mb-6 dark:text-white">
            Your Package Documentation,<br />
            <span className="bg-gradient-to-r from-blue-500 to-purple-600 bg-clip-text text-transparent">Organized & Editable</span>
          </h2>
          <p className="text-base sm:text-xl text-gray-600 dark:text-gray-300 mb-8 max-w-2xl mx-auto">
            Upload your composer.json or package.json. We fetch docs for all your dependencies. 
            Edit them locally. Keep them in sync.
          </p>
          <Link
            href={route('register')}
            className="inline-block px-6 sm:px-8 py-3 sm:py-4 bg-blue-500/90 hover:bg-blue-500 text-white text-base sm:text-lg rounded-lg backdrop-blur-sm"
          >
            Start Free
          </Link>
          <p className="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-4">
            Free: 2 uploads, 10 packages • No credit card required
          </p>
        </section>

        {/* How It Works */}
        <section className="container mx-auto px-4 sm:px-6 py-12 sm:py-20">
          <h3 className="text-4xl sm:text-5xl font-bold text-center mb-8 sm:mb-12 bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent leading-tight">
            How It Works
          </h3>
          <div className="grid sm:grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-8">
            <div className="text-center backdrop-blur-sm bg-white/90 dark:bg-gray-800/90 rounded-2xl p-6 border border-gray-200 dark:border-gray-700">
              <div className="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-500 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg className="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" /></svg>
              </div>
              <h4 className="font-bold mb-2 dark:text-white">1. Upload</h4>
              <p className="text-sm text-gray-600 dark:text-gray-300">Upload your composer.json or package.json</p>
            </div>
            <div className="text-center backdrop-blur-sm bg-white/90 dark:bg-gray-800/90 rounded-2xl p-6 border border-gray-200 dark:border-gray-700">
              <div className="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-500 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg className="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
              </div>
              <h4 className="font-bold mb-2 dark:text-white">2. Select</h4>
              <p className="text-sm text-gray-600 dark:text-gray-300">Choose which packages to track</p>
            </div>
            <div className="text-center backdrop-blur-sm bg-white/90 dark:bg-gray-800/90 rounded-2xl p-6 border border-gray-200 dark:border-gray-700">
              <div className="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-500 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg className="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
              </div>
              <h4 className="font-bold mb-2 dark:text-white">3. Edit</h4>
              <p className="text-sm text-gray-600 dark:text-gray-300">Edit docs with our rich text editor</p>
            </div>
            <div className="text-center backdrop-blur-sm bg-white/90 dark:bg-gray-800/90 rounded-2xl p-6 border border-gray-200 dark:border-gray-700">
              <div className="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-500 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg className="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
              </div>
              <h4 className="font-bold mb-2 dark:text-white">4. Sync</h4>
              <p className="text-sm text-gray-600 dark:text-gray-300">Keep docs updated with upstream</p>
            </div>
          </div>
        </section>

        {/* Pricing */}
        <section className="container mx-auto px-4 sm:px-6 py-12 sm:py-20">
          <h3 className="text-4xl sm:text-5xl font-bold text-center mb-8 sm:mb-12 bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent leading-tight pb-2">Simple Pricing</h3>
          <div className="grid sm:grid-cols-1 md:grid-cols-3 gap-6 sm:gap-8 max-w-5xl mx-auto">
            <div className="backdrop-blur-sm bg-white/90 dark:bg-gray-800/90 border border-gray-200 dark:border-gray-700 rounded-lg p-6 text-center">
              <h4 className="text-xl font-bold mb-2 dark:text-white">Free</h4>
              <p className="text-4xl font-bold mb-4 dark:text-white">£0</p>
              <ul className="text-left space-y-2 mb-6 text-gray-700 dark:text-gray-300">
                <li>✓ 2 file uploads</li>
                <li>✓ 10 packages</li>
                <li>✓ Edit & sync</li>
              </ul>
              <Link href={route('register')} className="block w-full py-2 border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-50 dark:hover:bg-gray-700 dark:text-white">
                Start Free
              </Link>
            </div>
            <div className="backdrop-blur-sm bg-white/90 dark:bg-gray-800/90 border-2 border-blue-500 rounded-lg p-6 text-center relative">
              <div className="absolute -top-3 left-1/2 -translate-x-1/2 bg-blue-500 text-white px-3 py-1 rounded text-sm">
                Popular
              </div>
              <h4 className="text-xl font-bold mb-2 dark:text-white">Pro</h4>
              <p className="text-4xl font-bold mb-4 dark:text-white">£9<span className="text-lg">/mo</span></p>
              <ul className="text-left space-y-2 mb-6 text-gray-700 dark:text-gray-300">
                <li>✓ Unlimited uploads</li>
                <li>✓ 100 packages</li>
                <li>✓ 3 projects</li>
              </ul>
              <Link href={route('register')} className="block w-full py-2 bg-blue-500/90 hover:bg-blue-500 text-white rounded backdrop-blur-sm">
                Start Pro
              </Link>
            </div>
            <div className="backdrop-blur-sm bg-white/90 dark:bg-gray-800/90 border border-gray-200 dark:border-gray-700 rounded-lg p-6 text-center">
              <h4 className="text-xl font-bold mb-2 dark:text-white">Lifetime</h4>
              <p className="text-4xl font-bold mb-4 dark:text-white">£299</p>
              <ul className="text-left space-y-2 mb-6 text-gray-700 dark:text-gray-300">
                <li>✓ Unlimited everything</li>
                <li>✓ Multiple projects</li>
                <li>✓ Pay once, forever*</li>
              </ul>
              <Link href={route('register')} className="block w-full py-2 bg-orange-400/80 hover:bg-orange-400 text-white rounded backdrop-blur-sm">
                Get Lifetime
              </Link>
            </div>
          </div>
          <p className="text-xs text-gray-500 dark:text-gray-400 text-center mt-8 max-w-2xl mx-auto">
            *Lifetime access valid while product is commercially available
          </p>
        </section>

        {/* Footer */}
        <footer className="border-t border-gray-200 dark:border-gray-700 py-8 text-center text-gray-600 dark:text-gray-400">
          <div className="container mx-auto px-4 sm:px-6">
            <div className="flex flex-wrap justify-center gap-4 mb-4">
              <Link href="/health" className="hover:text-gray-900 dark:hover:text-white">Health Dashboard</Link>
              <Link href="/terms" className="hover:text-gray-900 dark:hover:text-white">Terms & Conditions</Link>
              <Link href="/privacy" className="hover:text-gray-900 dark:hover:text-white">Privacy Policy</Link>
              <Link href="/faq" className="hover:text-gray-900 dark:hover:text-white">FAQ</Link>
            </div>
            <p>&copy; 2026 Markdown Observer. Built with Laravel.</p>
          </div>
        </footer>
      </div>
    </>
  )
}
