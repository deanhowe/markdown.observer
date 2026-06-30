import { Head, Link } from '@inertiajs/react'

export default function Welcome() {
  return (
    <>
      <Head title="Markdown Observer - Your Package Documentation Hub for Laravel">
        <meta name="description" content="Transform package documentation into an editable knowledge base. TipTap editor, version control, and your Markdown files as the source of truth. Built for Laravel developers." />
        <meta property="og:title" content="Markdown Observer - Package Documentation for Laravel Developers" />
        <meta property="og:description" content="Transform package docs into an editable knowledge base with TipTap, version control, and Markdown as source of truth." />
        <meta property="og:type" content="website" />
        <meta property="og:url" content="https://markdown.observer" />
        <meta property="og:image" content="https://markdown.observer/og-home.png" />
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" content="Markdown Observer - Package Documentation for Laravel" />
        <meta name="twitter:description" content="Transform package docs with TipTap, version control, and Markdown source of truth." />
        <meta name="twitter:image" content="https://markdown.observer/og-home.png" />
      </Head>
      
      <div className="min-h-screen relative bg-gradient-to-br from-gray-50 to-blue-50 dark:from-gray-900 dark:to-gray-800">
        {/* Header */}
        <header className="container mx-auto px-4 sm:px-6 py-6 flex justify-between items-center">
          <div className="flex items-center gap-3">
            <div className="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
              <svg className="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
            </div>
            <h1 className="text-xl sm:text-2xl font-bold dark:text-white">Markdown Observer</h1>
          </div>
          <div className="flex gap-2 sm:gap-4">
            <Link href={route('login')} className="px-3 sm:px-4 py-2 text-sm sm:text-base text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white transition-colors">
              Login
            </Link>
            <Link href={route('register')} className="px-3 sm:px-4 py-2 text-sm sm:text-base bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow-lg shadow-blue-500/50 transition-all hover:scale-105 whitespace-nowrap">
              Get Started Free
            </Link>
          </div>
        </header>

        {/* Hero */}
        <section className="container mx-auto px-4 sm:px-6 py-12 sm:py-24 text-center">
          <div className="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 text-sm font-medium mb-8">
            <svg className="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
              <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd" />
            </svg>
            Built with Laravel 12, React 19 & TipTap
          </div>
          
          <h2 className="text-4xl sm:text-6xl lg:text-7xl font-extrabold mb-6 dark:text-white leading-tight">
            Your Package Docs,<br />
            <span className="bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 bg-clip-text text-transparent">
              Actually Useful
            </span>
          </h2>
          
          <p className="text-lg sm:text-xl lg:text-2xl text-gray-600 dark:text-gray-300 mb-10 max-w-3xl mx-auto leading-relaxed">
            Stop juggling GitHub READMEs. Transform your <code className="px-2 py-1 bg-gray-200 dark:bg-gray-700 rounded text-sm">composer.json</code> into 
            an editable knowledge base with version control, rich text editing, and your Markdown files as the single source of truth.
          </p>
          
          <div className="flex flex-col sm:flex-row gap-4 justify-center items-center">
            <Link
              href={route('register')}
              className="inline-block px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white text-lg font-semibold rounded-lg shadow-xl shadow-blue-500/50 transition-all hover:scale-105 hover:shadow-2xl"
            >
              Start Free — No Credit Card
            </Link>
            <a
              href="#demo"
              className="inline-block px-8 py-4 border-2 border-gray-300 dark:border-gray-600 hover:border-blue-500 dark:hover:border-blue-500 text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 text-lg font-semibold rounded-lg transition-all"
            >
              Watch Demo
            </a>
          </div>
          
          <p className="text-sm text-gray-500 dark:text-gray-400 mt-6">
            Free tier: 2 uploads, 10 packages • Upgrade anytime
          </p>
        </section>

        {/* Features for Laravel Developers */}
        <section className="container mx-auto px-4 sm:px-6 py-16 sm:py-24">
          <div className="text-center mb-12">
            <h3 className="text-3xl sm:text-5xl font-bold mb-4 bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
              Built for Laravel Developers
            </h3>
            <p className="text-lg text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
              Everything you need to turn package documentation into a searchable, editable knowledge base
            </p>
          </div>
          
          <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            {/* TipTap Editor */}
            <div className="backdrop-blur-sm bg-white dark:bg-gray-800 rounded-2xl p-8 border border-gray-200 dark:border-gray-700 hover:border-blue-500 dark:hover:border-blue-500 transition-all hover:shadow-xl">
              <div className="w-14 h-14 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center mb-6">
                <svg className="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
              </div>
              <h4 className="text-xl font-bold mb-3 dark:text-white">TipTap Rich Text</h4>
              <p className="text-gray-600 dark:text-gray-300 mb-4">
                Edit with a modern WYSIWYG editor. Switch between Markdown and rich text seamlessly.
              </p>
              <div className="text-sm text-blue-600 dark:text-blue-400 font-mono">
                spatie/laravel-markdown
              </div>
            </div>

            {/* Version Control */}
            <div className="backdrop-blur-sm bg-white dark:bg-gray-800 rounded-2xl p-8 border border-gray-200 dark:border-gray-700 hover:border-blue-500 dark:hover:border-blue-500 transition-all hover:shadow-xl">
              <div className="w-14 h-14 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl flex items-center justify-center mb-6">
                <svg className="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <h4 className="text-xl font-bold mb-3 dark:text-white">Version Control</h4>
              <p className="text-gray-600 dark:text-gray-300 mb-4">
                Track every change. Compare versions. Revert when needed. Full history, no Git complexity.
              </p>
              <div className="text-sm text-purple-600 dark:text-purple-400 font-mono">
                Built-in versioning
              </div>
            </div>

            {/* Markdown First */}
            <div className="backdrop-blur-sm bg-white dark:bg-gray-800 rounded-2xl p-8 border border-gray-200 dark:border-gray-700 hover:border-blue-500 dark:hover:border-blue-500 transition-all hover:shadow-xl">
              <div className="w-14 h-14 bg-gradient-to-br from-green-500 to-teal-600 rounded-xl flex items-center justify-center mb-6">
                <svg className="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
              </div>
              <h4 className="text-xl font-bold mb-3 dark:text-white">Markdown Source</h4>
              <p className="text-gray-600 dark:text-gray-300 mb-4">
                Your <code className="text-sm bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">.md</code> files are the source of truth. No vendor lock-in.
              </p>
              <div className="text-sm text-green-600 dark:text-green-400 font-mono">
                league/html-to-markdown
              </div>
            </div>

            {/* Syntax Highlighting */}
            <div className="backdrop-blur-sm bg-white dark:bg-gray-800 rounded-2xl p-8 border border-gray-200 dark:border-gray-700 hover:border-blue-500 dark:hover:border-blue-500 transition-all hover:shadow-xl">
              <div className="w-14 h-14 bg-gradient-to-br from-orange-500 to-red-600 rounded-xl flex items-center justify-center mb-6">
                <svg className="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                </svg>
              </div>
              <h4 className="text-xl font-bold mb-3 dark:text-white">Shiki Highlighting</h4>
              <p className="text-gray-600 dark:text-gray-300 mb-4">
                Beautiful code blocks with VS Code themes. Perfect for technical documentation.
              </p>
              <div className="text-sm text-orange-600 dark:text-orange-400 font-mono">
                spatie/shiki-php
              </div>
            </div>

            {/* Laravel Horizon */}
            <div className="backdrop-blur-sm bg-white dark:bg-gray-800 rounded-2xl p-8 border border-gray-200 dark:border-gray-700 hover:border-blue-500 dark:hover:border-blue-500 transition-all hover:shadow-xl">
              <div className="w-14 h-14 bg-gradient-to-br from-red-500 to-pink-600 rounded-xl flex items-center justify-center mb-6">
                <svg className="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
              </div>
              <h4 className="text-xl font-bold mb-3 dark:text-white">Queue Processing</h4>
              <p className="text-gray-600 dark:text-gray-300 mb-4">
                Fetch and sync package docs in the background with Laravel Horizon.
              </p>
              <div className="text-sm text-red-600 dark:text-red-400 font-mono">
                laravel/horizon
              </div>
            </div>

            {/* API First */}
            <div className="backdrop-blur-sm bg-white dark:bg-gray-800 rounded-2xl p-8 border border-gray-200 dark:border-gray-700 hover:border-blue-500 dark:hover:border-blue-500 transition-all hover:shadow-xl">
              <div className="w-14 h-14 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-xl flex items-center justify-center mb-6">
                <svg className="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
              </div>
              <h4 className="text-xl font-bold mb-3 dark:text-white">RESTful API</h4>
              <p className="text-gray-600 dark:text-gray-300 mb-4">
                Full API access. Integrate with CI/CD, webhooks, or build your own tools.
              </p>
              <div className="text-sm text-cyan-600 dark:text-cyan-400 font-mono">
                laravel/sanctum
              </div>
            </div>
          </div>
        </section>

        {/* How It Works */}
        <section className="container mx-auto px-4 sm:px-6 py-16 sm:py-20 bg-gradient-to-br from-blue-50 to-purple-50 dark:from-gray-800 dark:to-gray-900 rounded-3xl">
          <h3 className="text-3xl sm:text-5xl font-bold text-center mb-12 dark:text-white">
            Simple Workflow
          </h3>
          <div className="grid sm:grid-cols-2 lg:grid-cols-4 gap-8 max-w-6xl mx-auto">
            <div className="text-center">
              <div className="w-20 h-20 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg">
                <span className="text-3xl font-bold text-white">1</span>
              </div>
              <h4 className="text-lg font-bold mb-3 dark:text-white">Upload composer.json</h4>
              <p className="text-sm text-gray-600 dark:text-gray-300">
                Drop your file or paste dependencies
              </p>
            </div>
            <div className="text-center">
              <div className="w-20 h-20 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg">
                <span className="text-3xl font-bold text-white">2</span>
              </div>
              <h4 className="text-lg font-bold mb-3 dark:text-white">We Fetch Docs</h4>
              <p className="text-sm text-gray-600 dark:text-gray-300">
                READMEs from GitHub/Packagist
              </p>
            </div>
            <div className="text-center">
              <div className="w-20 h-20 bg-gradient-to-br from-pink-500 to-pink-600 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg">
                <span className="text-3xl font-bold text-white">3</span>
              </div>
              <h4 className="text-lg font-bold mb-3 dark:text-white">Edit & Annotate</h4>
              <p className="text-sm text-gray-600 dark:text-gray-300">
                Add notes, highlight, organize
              </p>
            </div>
            <div className="text-center">
              <div className="w-20 h-20 bg-gradient-to-br from-orange-500 to-red-600 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg">
                <span className="text-3xl font-bold text-white">4</span>
              </div>
              <h4 className="text-lg font-bold mb-3 dark:text-white">Stay In Sync</h4>
              <p className="text-sm text-gray-600 dark:text-gray-300">
                Track upstream changes automatically
              </p>
            </div>
          </div>
        </section>

        {/* Pricing */}
        <section className="container mx-auto px-4 sm:px-6 py-16 sm:py-24">
          <div className="text-center mb-12">
            <h3 className="text-3xl sm:text-5xl font-bold mb-4 dark:text-white">
              Simple, Honest Pricing
            </h3>
            <p className="text-lg text-gray-600 dark:text-gray-300">
              No hidden fees. Cancel anytime. Or pay once and own it forever.
            </p>
          </div>
          
          <div className="grid sm:grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto">
            {/* Free */}
            <div className="backdrop-blur-sm bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 rounded-2xl p-8 transition-all hover:border-blue-500 hover:shadow-xl">
              <div className="text-center mb-6">
                <h4 className="text-2xl font-bold mb-2 dark:text-white">Free</h4>
                <div className="flex items-baseline justify-center gap-1">
                  <span className="text-5xl font-extrabold dark:text-white">£0</span>
                </div>
                <p className="text-gray-500 dark:text-gray-400 mt-2">Perfect for trying it out</p>
              </div>
              <ul className="space-y-4 mb-8">
                <li className="flex items-start gap-3">
                  <svg className="w-5 h-5 text-green-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd" />
                  </svg>
                  <span className="text-gray-700 dark:text-gray-300">2 composer.json uploads</span>
                </li>
                <li className="flex items-start gap-3">
                  <svg className="w-5 h-5 text-green-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd" />
                  </svg>
                  <span className="text-gray-700 dark:text-gray-300">Up to 10 packages</span>
                </li>
                <li className="flex items-start gap-3">
                  <svg className="w-5 h-5 text-green-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd" />
                  </svg>
                  <span className="text-gray-700 dark:text-gray-300">TipTap editor</span>
                </li>
                <li className="flex items-start gap-3">
                  <svg className="w-5 h-5 text-green-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd" />
                  </svg>
                  <span className="text-gray-700 dark:text-gray-300">Version control</span>
                </li>
              </ul>
              <Link
                href={route('register')}
                className="block w-full py-3 text-center border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:border-blue-500 hover:text-blue-600 dark:hover:text-blue-400 font-semibold transition-all"
              >
                Start Free
              </Link>
            </div>

            {/* Pro - Popular */}
            <div className="backdrop-blur-sm bg-gradient-to-br from-blue-500 to-purple-600 rounded-2xl p-8 shadow-2xl shadow-blue-500/50 relative transform scale-105">
              <div className="absolute -top-4 left-1/2 -translate-x-1/2 bg-orange-500 text-white px-6 py-1.5 rounded-full text-sm font-bold shadow-lg">
                Most Popular
              </div>
              <div className="text-center mb-6 text-white">
                <h4 className="text-2xl font-bold mb-2">Pro</h4>
                <div className="flex items-baseline justify-center gap-1">
                  <span className="text-5xl font-extrabold">£9</span>
                  <span className="text-xl">/month</span>
                </div>
                <p className="mt-2 opacity-90">For serious projects</p>
              </div>
              <ul className="space-y-4 mb-8 text-white">
                <li className="flex items-start gap-3">
                  <svg className="w-5 h-5 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd" />
                  </svg>
                  <span><strong>Unlimited</strong> uploads</span>
                </li>
                <li className="flex items-start gap-3">
                  <svg className="w-5 h-5 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd" />
                  </svg>
                  <span><strong>100</strong> packages per project</span>
                </li>
                <li className="flex items-start gap-3">
                  <svg className="w-5 h-5 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd" />
                  </svg>
                  <span><strong>3</strong> projects</span>
                </li>
                <li className="flex items-start gap-3">
                  <svg className="w-5 h-5 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd" />
                  </svg>
                  <span>API access</span>
                </li>
                <li className="flex items-start gap-3">
                  <svg className="w-5 h-5 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd" />
                  </svg>
                  <span>Priority support</span>
                </li>
              </ul>
              <Link
                href={route('register')}
                className="block w-full py-3 text-center bg-white text-blue-600 rounded-lg hover:bg-gray-100 font-bold transition-all shadow-lg"
              >
                Start Pro Trial
              </Link>
            </div>

            {/* Lifetime */}
            <div className="backdrop-blur-sm bg-white dark:bg-gray-800 border-2 border-orange-400 dark:border-orange-500 rounded-2xl p-8 transition-all hover:shadow-xl">
              <div className="text-center mb-6">
                <h4 className="text-2xl font-bold mb-2 dark:text-white">Lifetime</h4>
                <div className="flex items-baseline justify-center gap-1">
                  <span className="text-5xl font-extrabold text-orange-500">£299</span>
                </div>
                <p className="text-gray-500 dark:text-gray-400 mt-2">Pay once, own forever</p>
              </div>
              <ul className="space-y-4 mb-8">
                <li className="flex items-start gap-3">
                  <svg className="w-5 h-5 text-orange-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd" />
                  </svg>
                  <span className="text-gray-700 dark:text-gray-300"><strong>Everything</strong> in Pro</span>
                </li>
                <li className="flex items-start gap-3">
                  <svg className="w-5 h-5 text-orange-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd" />
                  </svg>
                  <span className="text-gray-700 dark:text-gray-300"><strong>Unlimited</strong> projects</span>
                </li>
                <li className="flex items-start gap-3">
                  <svg className="w-5 h-5 text-orange-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd" />
                  </svg>
                  <span className="text-gray-700 dark:text-gray-300"><strong>500</strong> packages/project</span>
                </li>
                <li className="flex items-start gap-3">
                  <svg className="w-5 h-5 text-orange-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd" />
                  </svg>
                  <span className="text-gray-700 dark:text-gray-300">No monthly fees</span>
                </li>
                <li className="flex items-start gap-3">
                  <svg className="w-5 h-5 text-orange-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd" />
                  </svg>
                  <span className="text-gray-700 dark:text-gray-300">Future updates included</span>
                </li>
              </ul>
              <Link
                href={route('register')}
                className="block w-full py-3 text-center bg-gradient-to-r from-orange-500 to-red-500 text-white rounded-lg hover:from-orange-600 hover:to-red-600 font-bold transition-all shadow-lg"
              >
                Buy Lifetime
              </Link>
            </div>
          </div>
          
          <p className="text-sm text-center text-gray-500 dark:text-gray-400 mt-8 max-w-2xl mx-auto">
            All plans include full version control, TipTap editor, Markdown source of truth, and Shiki syntax highlighting.
            <br />
            *Lifetime access valid while product is commercially available. All features included.
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
