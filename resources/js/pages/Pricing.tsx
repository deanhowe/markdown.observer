import { Head } from '@inertiajs/react'

export default function Pricing() {
  return (
    <>
      <Head title="Pricing" />
      
      <div className="max-w-6xl mx-auto p-6">
        <h1 className="text-3xl font-bold text-center mb-12">Simple, Transparent Pricing</h1>
        
        <div className="grid md:grid-cols-3 gap-8">
          {/* Free */}
          <div className="border rounded-lg p-6">
            <h2 className="text-2xl font-bold mb-2">Free</h2>
            <p className="text-4xl font-bold mb-6">$0</p>
            <ul className="space-y-3 mb-6">
              <li>✓ 2 file uploads (PHP & JS)</li>
              <li>✓ 10 packages</li>
              <li>✓ Edit & sync docs</li>
              <li>✓ 1 project</li>
            </ul>
            <button className="w-full py-2 border rounded hover:bg-gray-50">
              Current Plan
            </button>
          </div>

          {/* Pro */}
          <div className="border-2 border-blue-500 rounded-lg p-6 relative">
            <div className="absolute -top-3 left-1/2 -translate-x-1/2 bg-blue-500 text-white px-3 py-1 rounded text-sm">
              Popular
            </div>
            <h2 className="text-2xl font-bold mb-2">Pro</h2>
            <p className="text-4xl font-bold mb-6">
              $9<span className="text-lg">/mo</span>
            </p>
            <ul className="space-y-3 mb-6">
              <li>✓ Unlimited uploads</li>
              <li>✓ 100 packages</li>
              <li>✓ Edit & sync docs</li>
              <li>✓ 3 projects</li>
              <li>✓ Priority support</li>
            </ul>
            <button className="w-full py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
              Upgrade to Pro
            </button>
          </div>

          {/* Lifetime */}
          <div className="border rounded-lg p-6">
            <h2 className="text-2xl font-bold mb-2">Lifetime</h2>
            <p className="text-4xl font-bold mb-6">$299</p>
            <ul className="space-y-3 mb-6">
              <li>✓ Unlimited everything</li>
              <li>✓ Unlimited packages</li>
              <li>✓ Unlimited projects</li>
              <li>✓ Priority support</li>
              <li>✓ Pay once, use forever</li>
            </ul>
            <button className="w-full py-2 bg-green-500 text-white rounded hover:bg-green-600">
              Get Lifetime Access
            </button>
          </div>
        </div>

        <p className="text-center text-gray-500 mt-12">
          Stripe integration coming soon. Email for early access!
        </p>
      </div>
    </>
  )
}
