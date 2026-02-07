import { Head, Link } from '@inertiajs/react'

export default function Welcome() {
  return (
    <>
      <Head title="Your Package Documentation Hub" />
      
      <div className="min-h-screen bg-gradient-to-b from-blue-50 to-white">
        {/* Header */}
        <header className="container mx-auto px-6 py-6 flex justify-between items-center">
          <h1 className="text-2xl font-bold dark:text-white">Markdown Observer</h1>
          <div className="flex gap-4">
            <Link href={route('login')} className="px-4 py-2 text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white">
              Login
            </Link>
            <Link href={route('register')} className="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
              Get Started
            </Link>
          </div>
        </header>

        {/* Hero */}
        <section className="container mx-auto px-6 py-20 text-center">
          <h2 className="text-5xl font-bold mb-6">
            Your Package Documentation,<br />
            <span className="text-blue-500">Organized & Editable</span>
          </h2>
          <p className="text-xl text-gray-600 mb-8 max-w-2xl mx-auto">
            Upload your composer.json or package.json. We fetch docs for all your dependencies. 
            Edit them locally. Keep them in sync.
          </p>
          <Link
            href={route('register')}
            className="inline-block px-8 py-4 bg-blue-500 text-white text-lg rounded-lg hover:bg-blue-600"
          >
            Start Free
          </Link>
          <p className="text-sm text-gray-500 mt-4">
            Free: 2 uploads, 10 packages • No credit card required
          </p>
        </section>

        {/* How It Works */}
        <section className="container mx-auto px-6 py-20">
          <h3 className="text-3xl font-bold text-center mb-12">How It Works</h3>
          <div className="grid md:grid-cols-4 gap-8">
            <div className="text-center">
              <div className="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg className="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" /></svg>
              </div>
              <h4 className="font-bold mb-2">1. Upload</h4>
              <p className="text-gray-600">Upload your composer.json or package.json</p>
            </div>
            <div className="text-center">
              <div className="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg className="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
              </div>
              <h4 className="font-bold mb-2">2. Select</h4>
              <p className="text-gray-600">Choose which packages to track</p>
            </div>
            <div className="text-center">
              <div className="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg className="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
              </div>
              <h4 className="font-bold mb-2">3. Edit</h4>
              <p className="text-gray-600">Edit docs with our rich text editor</p>
            </div>
            <div className="text-center">
              <div className="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg className="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
              </div>
              <h4 className="font-bold mb-2">4. Sync</h4>
              <p className="text-gray-600">Keep docs updated with upstream</p>
            </div>
          </div>
        </section>

        {/* Pricing */}
        <section className="container mx-auto px-6 py-20">
          <h3 className="text-3xl font-bold text-center mb-12">Simple Pricing</h3>
          <div className="grid md:grid-cols-3 gap-8 max-w-5xl mx-auto">
            <div className="border rounded-lg p-6 text-center">
              <h4 className="text-xl font-bold mb-2">Free</h4>
              <p className="text-4xl font-bold mb-4">£0</p>
              <ul className="text-left space-y-2 mb-6">
                <li>✓ 2 file uploads</li>
                <li>✓ 10 packages</li>
                <li>✓ Edit & sync</li>
              </ul>
              <Link href={route('register')} className="block w-full py-2 border rounded hover:bg-gray-50">
                Start Free
              </Link>
            </div>
            <div className="border-2 border-blue-500 rounded-lg p-6 text-center relative">
              <div className="absolute -top-3 left-1/2 -translate-x-1/2 bg-blue-500 text-white px-3 py-1 rounded text-sm">
                Popular
              </div>
              <h4 className="text-xl font-bold mb-2">Pro</h4>
              <p className="text-4xl font-bold mb-4">£9<span className="text-lg">/mo</span></p>
              <ul className="text-left space-y-2 mb-6">
                <li>✓ Unlimited uploads</li>
                <li>✓ 100 packages</li>
                <li>✓ 3 projects</li>
              </ul>
              <Link href={route('register')} className="block w-full py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                Start Pro
              </Link>
            </div>
            <div className="border rounded-lg p-6 text-center">
              <h4 className="text-xl font-bold mb-2">Lifetime</h4>
              <p className="text-4xl font-bold mb-4">£299</p>
              <ul className="text-left space-y-2 mb-6">
                <li>✓ Unlimited everything</li>
                <li>✓ Multiple projects</li>
                <li>✓ Pay once, forever</li>
              </ul>
              <Link href={route('register')} className="block w-full py-2 bg-green-500 text-white rounded hover:bg-green-600">
                Get Lifetime
              </Link>
            </div>
          </div>
        </section>

        {/* Footer */}
        <footer className="border-t py-8 text-center text-gray-600">
          <p>&copy; 2026 Markdown Observer. Built with Laravel.</p>
        </footer>
      </div>
    </>
  )
}
