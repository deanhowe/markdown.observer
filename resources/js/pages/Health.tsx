import { Head } from '@inertiajs/react'

interface Stats {
  users: {
    total: number
    free: number
    pro: number
    lifetime: number
    today: number
    this_week: number
  }
  steering_docs: {
    collections: number
    documents: number
    public: number
    by_type: Record<string, number>
  }
  packages: {
    total: number
    unique: number
  }
  queue: {
    horizon_status: string
    failed_jobs: number
  }
  system: {
    laravel_version: string
    php_version: string
    environment: string
    debug: boolean
  }
}

export default function Health({ stats }: { stats: Stats }) {
  const statusColor = (status: string) => {
    if (status === 'running') return 'text-green-500'
    if (status === 'stopped') return 'text-red-500'
    return 'text-yellow-500'
  }

  return (
    <>
      <Head title="Health Dashboard" />
      
      <div className="min-h-screen bg-gray-50 dark:bg-gray-900 py-12">
        <div className="container mx-auto px-4 sm:px-6 max-w-6xl">
          <h1 className="text-4xl font-bold mb-8 dark:text-white">Health Dashboard</h1>
          
          <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            {/* Users */}
            <div className="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
              <h2 className="text-xl font-bold mb-4 dark:text-white">Users</h2>
              <div className="space-y-2 text-gray-700 dark:text-gray-300">
                <div className="flex justify-between">
                  <span>Total:</span>
                  <span className="font-bold">{stats.users.total}</span>
                </div>
                <div className="flex justify-between">
                  <span>Free:</span>
                  <span>{stats.users.free}</span>
                </div>
                <div className="flex justify-between">
                  <span>Pro:</span>
                  <span className="text-blue-500 font-bold">{stats.users.pro}</span>
                </div>
                <div className="flex justify-between">
                  <span>Lifetime:</span>
                  <span className="text-orange-500 font-bold">{stats.users.lifetime}</span>
                </div>
                <div className="border-t border-gray-200 dark:border-gray-700 pt-2 mt-2">
                  <div className="flex justify-between">
                    <span>Today:</span>
                    <span className="font-bold">{stats.users.today}</span>
                  </div>
                  <div className="flex justify-between">
                    <span>This Week:</span>
                    <span className="font-bold">{stats.users.this_week}</span>
                  </div>
                </div>
              </div>
            </div>

            {/* Steering Docs */}
            <div className="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
              <h2 className="text-xl font-bold mb-4 dark:text-white">AI Steering Docs</h2>
              <div className="space-y-2 text-gray-700 dark:text-gray-300">
                <div className="flex justify-between">
                  <span>Collections:</span>
                  <span className="font-bold">{stats.steering_docs.collections}</span>
                </div>
                <div className="flex justify-between">
                  <span>Documents:</span>
                  <span className="font-bold">{stats.steering_docs.documents}</span>
                </div>
                <div className="flex justify-between">
                  <span>Public:</span>
                  <span>{stats.steering_docs.public}</span>
                </div>
                <div className="border-t border-gray-200 dark:border-gray-700 pt-2 mt-2">
                  <div className="text-sm font-semibold mb-1">By Type:</div>
                  {Object.entries(stats.steering_docs.by_type).map(([type, count]) => (
                    <div key={type} className="flex justify-between text-sm">
                      <span>.{type}/</span>
                      <span>{count}</span>
                    </div>
                  ))}
                </div>
              </div>
            </div>

            {/* Packages */}
            <div className="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
              <h2 className="text-xl font-bold mb-4 dark:text-white">Packages</h2>
              <div className="space-y-2 text-gray-700 dark:text-gray-300">
                <div className="flex justify-between">
                  <span>Total Tracked:</span>
                  <span className="font-bold">{stats.packages.total}</span>
                </div>
                <div className="flex justify-between">
                  <span>Unique:</span>
                  <span className="font-bold">{stats.packages.unique}</span>
                </div>
              </div>
            </div>

            {/* Queue */}
            <div className="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
              <h2 className="text-xl font-bold mb-4 dark:text-white">Queue</h2>
              <div className="space-y-2 text-gray-700 dark:text-gray-300">
                <div className="flex justify-between">
                  <span>Horizon:</span>
                  <span className={`font-bold ${statusColor(stats.queue.horizon_status)}`}>
                    {stats.queue.horizon_status}
                  </span>
                </div>
                <div className="flex justify-between">
                  <span>Failed Jobs:</span>
                  <span className={stats.queue.failed_jobs > 0 ? 'text-red-500 font-bold' : ''}>
                    {stats.queue.failed_jobs}
                  </span>
                </div>
              </div>
            </div>

            {/* System */}
            <div className="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
              <h2 className="text-xl font-bold mb-4 dark:text-white">System</h2>
              <div className="space-y-2 text-gray-700 dark:text-gray-300 text-sm">
                <div className="flex justify-between">
                  <span>Laravel:</span>
                  <span>{stats.system.laravel_version}</span>
                </div>
                <div className="flex justify-between">
                  <span>PHP:</span>
                  <span>{stats.system.php_version}</span>
                </div>
                <div className="flex justify-between">
                  <span>Environment:</span>
                  <span className="font-mono">{stats.system.environment}</span>
                </div>
                <div className="flex justify-between">
                  <span>Debug:</span>
                  <span className={stats.system.debug ? 'text-yellow-500' : 'text-green-500'}>
                    {stats.system.debug ? 'ON' : 'OFF'}
                  </span>
                </div>
              </div>
            </div>

            {/* Revenue (placeholder) */}
            <div className="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
              <h2 className="text-xl font-bold mb-4 dark:text-white">Revenue</h2>
              <div className="space-y-2 text-gray-700 dark:text-gray-300">
                <div className="flex justify-between">
                  <span>MRR:</span>
                  <span className="font-bold text-green-500">
                    £{stats.users.pro * 9}
                  </span>
                </div>
                <div className="flex justify-between">
                  <span>Lifetime Sales:</span>
                  <span className="font-bold text-orange-500">
                    £{stats.users.lifetime * 299}
                  </span>
                </div>
                <div className="border-t border-gray-200 dark:border-gray-700 pt-2 mt-2">
                  <div className="flex justify-between">
                    <span>Total:</span>
                    <span className="font-bold text-xl">
                      £{(stats.users.pro * 9) + (stats.users.lifetime * 299)}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div className="mt-8 text-center text-gray-500 dark:text-gray-400 text-sm">
            Last updated: {new Date().toLocaleString()}
          </div>
        </div>
      </div>
    </>
  )
}
