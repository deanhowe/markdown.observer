import { Head, Link } from '@inertiajs/react'

interface AIWelcomeProps {
  stats: {
    collections: number;
    documents: number;
    repos: number;
  }
}

export default function AIWelcome({ stats }: AIWelcomeProps) {
  return (
    <>
      <Head title="AI Steering Docs Search" />

      <div className="min-h-screen relative z-10">
        {/* Header */}
        <header className="container mx-auto px-6 py-6 flex justify-between items-center">
          <div>
            <h1 className="text-2xl font-bold dark:text-white">Markdown Observer</h1>
            <p className="text-sm text-gray-500">AI Steering Docs</p>
          </div>
          <div className="flex gap-4">
            <a href="https://markdown.observer" className="px-4 py-2 text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white">
              Package Docs
            </a>
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
            World's First<br />
            <span className="text-blue-500">AI Steering Docs Search Engine</span>
          </h2>
          <p className="text-xl text-gray-600 dark:text-gray-400 mb-8 max-w-2xl mx-auto">
            Learn how React, Next.js, Livewire structure their AI instructions.
            Search 500+ top projects for <code className="bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded">.claude/</code>,
            <code className="bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded">.cursor/</code>,
            <code className="bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded">.ai/</code> folders.
          </p>

          {/* Search Preview */}
          <div className="max-w-2xl mx-auto mb-8">
            <div className="backdrop-blur-sm bg-white/70 dark:bg-gray-800/70 rounded-2xl p-6 border border-gray-200 dark:border-gray-700">
              <input
                type="text"
                placeholder="Search: What do React's steering docs contain?"
                className="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white"
                disabled
              />
              <p className="text-sm text-gray-500 mt-4">Coming soon - Crawler running now!</p>
            </div>
          </div>

          <Link
            href={route('register')}
            className="inline-block px-8 py-4 bg-blue-500 text-white text-lg rounded-lg hover:bg-blue-600"
          >
            Join Waitlist
          </Link>
        </section>

        {/* Features */}
        <section className="container mx-auto px-6 py-20">
          <h3 className="text-3xl font-bold text-center mb-12 bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
            What You'll Find
          </h3>
          <div className="grid md:grid-cols-3 gap-8">
            <div className="backdrop-blur-sm bg-white/70 dark:bg-gray-800/70 rounded-2xl p-6 border border-gray-200 dark:border-gray-700">
              <h4 className="text-xl font-bold mb-3 dark:text-white">AI Instructions</h4>
              <p className="text-gray-600 dark:text-gray-400">
                See how top projects structure their Claude, Cursor, and Kiro instructions
              </p>
            </div>
            <div className="backdrop-blur-sm bg-white/70 dark:bg-gray-800/70 rounded-2xl p-6 border border-gray-200 dark:border-gray-700">
              <h4 className="text-xl font-bold mb-3 dark:text-white">Custom Rules</h4>
              <p className="text-gray-600 dark:text-gray-400">
                Learn coding patterns, testing strategies, and architecture decisions
              </p>
            </div>
            <div className="backdrop-blur-sm bg-white/70 dark:bg-gray-800/70 rounded-2xl p-6 border border-gray-200 dark:border-gray-700">
              <h4 className="text-xl font-bold mb-3 dark:text-white">Upload Yours</h4>
              <p className="text-gray-600 dark:text-gray-400">
                Share your steering docs with the community (optional)
              </p>
            </div>
          </div>
        </section>

        {/* Stats */}
        <section className="container mx-auto px-6 py-20 text-center">
          <div className="grid md:grid-cols-3 gap-8 max-w-3xl mx-auto">
            <div>
              <p className="text-4xl font-bold text-blue-500">{stats.repos.toLocaleString()}+</p>
              <p className="text-gray-600 dark:text-gray-400">Repos Crawled</p>
            </div>
            <div>
              <p className="text-4xl font-bold text-blue-500">~{stats.collections.toLocaleString()}</p>
              <p className="text-gray-600 dark:text-gray-400">Collections Found</p>
            </div>
            <div>
              <p className="text-4xl font-bold text-blue-500">{stats.documents.toLocaleString()}+</p>
              <p className="text-gray-600 dark:text-gray-400">Steering Docs</p>
            </div>
          </div>
        </section>

        {/* CTA */}
        <section className="container mx-auto px-6 py-20 text-center">
          <div className="backdrop-blur-sm bg-white/70 dark:bg-gray-800/70 rounded-2xl p-12 border border-gray-200 dark:border-gray-700 max-w-2xl mx-auto">
            <h3 className="text-3xl font-bold mb-4 dark:text-white">
              No One Else Has This Data
            </h3>
            <p className="text-xl text-gray-600 dark:text-gray-400 mb-8">
              This is StackOverflow for AI steering docs. Be first.
            </p>
            <Link
              href={route('register')}
              className="inline-block px-8 py-4 bg-blue-500 text-white text-lg rounded-lg hover:bg-blue-600"
            >
              Join Waitlist - Free
            </Link>
          </div>
        </section>
      </div>
    </>
  )
}
