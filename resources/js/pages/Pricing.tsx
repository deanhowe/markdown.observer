import { Head, router } from '@inertiajs/react'

export default function Pricing({ auth }: { auth?: { user: any } }) {
  const handleCheckout = (plan: 'pro-monthly' | 'lifetime') => {
    if (!auth?.user) {
      router.visit(route('register'))
      return
    }
    
    router.post(route(`checkout.${plan}`))
  }

  return (
    <>
      <Head title="Pricing" />
      
      <div className="max-w-6xl mx-auto p-6">
        <h1 className="text-3xl font-bold text-center mb-4">Simple, Transparent Pricing</h1>
        <p className="text-center text-xl text-gray-600 dark:text-gray-400 mb-12">
          🎉 <span className="font-bold text-blue-600">50% OFF Launch Discount</span> - Limited Time!
        </p>
        
        <div className="grid md:grid-cols-3 gap-8">
          {/* Free */}
          <div className="border rounded-lg p-6 dark:border-gray-700">
            <h2 className="text-2xl font-bold mb-2 dark:text-white">Free</h2>
            <p className="text-4xl font-bold mb-6 dark:text-white">£0</p>
            <ul className="space-y-3 mb-6 dark:text-gray-300">
              <li>✓ 2 file uploads (PHP & JS)</li>
              <li>✓ 10 packages</li>
              <li>✓ Edit & sync docs</li>
              <li>✓ 1 project</li>
            </ul>
            <button className="w-full py-2 border rounded hover:bg-gray-50 dark:border-gray-600 dark:hover:bg-gray-800 dark:text-white">
              Current Plan
            </button>
          </div>

          {/* Pro */}
          <div className="border-2 border-blue-500 rounded-lg p-6 relative dark:bg-gray-800/50">
            <div className="absolute -top-3 left-1/2 -translate-x-1/2 bg-blue-500 text-white px-3 py-1 rounded text-sm">
              Popular
            </div>
            <h2 className="text-2xl font-bold mb-2 dark:text-white">Pro</h2>
            <p className="text-4xl font-bold mb-2 dark:text-white">
              £9<span className="text-lg">/mo</span>
            </p>
            <p className="text-sm text-gray-500 line-through mb-4">£18/mo</p>
            <ul className="space-y-3 mb-6 dark:text-gray-300">
              <li>✓ Unlimited uploads</li>
              <li>✓ 100 packages</li>
              <li>✓ Edit & sync docs</li>
              <li>✓ 3 projects</li>
              <li>✓ Priority support</li>
            </ul>
            <button 
              onClick={() => handleCheckout('pro-monthly')}
              className="w-full py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
            >
              Upgrade to Pro
            </button>
          </div>

          {/* Lifetime */}
          <div className="border rounded-lg p-6 dark:border-gray-700">
            <h2 className="text-2xl font-bold mb-2 dark:text-white">Lifetime</h2>
            <p className="text-4xl font-bold mb-2 dark:text-white">£299</p>
            <p className="text-sm text-gray-500 line-through mb-4">£598</p>
            <ul className="space-y-3 mb-6 dark:text-gray-300">
              <li>✓ Unlimited everything</li>
              <li>✓ Unlimited packages</li>
              <li>✓ Unlimited projects</li>
              <li>✓ Priority support</li>
              <li>✓ Pay once, use forever</li>
            </ul>
            <button 
              onClick={() => handleCheckout('lifetime')}
              className="w-full py-2 bg-green-500 text-white rounded hover:bg-green-600"
            >
              Get Lifetime Access
            </button>
          </div>
        </div>

        <p className="text-center text-gray-500 dark:text-gray-400 mt-12">
          Secure checkout powered by Stripe • Cancel anytime
        </p>
      </div>
    </>
  )
}
