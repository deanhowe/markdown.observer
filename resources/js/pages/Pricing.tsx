import { Head, router } from '@inertiajs/react'
import { useState } from 'react'

export default function Pricing({ auth }: { auth?: { user: any } }) {
  const [isYearly, setIsYearly] = useState(false)
  
  const handleCheckout = (plan: 'pro-monthly' | 'pro-yearly' | 'lifetime') => {
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
        <h1 className="text-3xl font-bold text-center mb-4 dark:text-white">Simple, Transparent Pricing</h1>
        
        {/* Big Discount Banner */}
        <div className="max-w-2xl mx-auto mb-8 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-lg p-6 text-center">
          <div className="text-4xl font-bold mb-2">🎉 50% OFF</div>
          <div className="text-xl">Launch Discount - Limited Time Only!</div>
          <div className="text-sm mt-2 opacity-90">All prices shown are already discounted</div>
        </div>
        
        {/* Billing Toggle */}
        <div className="flex justify-center items-center gap-4 mb-12">
          <span className={`text-lg ${!isYearly ? 'font-bold dark:text-white' : 'text-gray-500 dark:text-gray-400'}`}>
            Monthly
          </span>
          <button
            onClick={() => setIsYearly(!isYearly)}
            className="relative w-14 h-7 bg-gray-300 dark:bg-gray-600 rounded-full transition-colors"
          >
            <div className={`absolute top-1 left-1 w-5 h-5 bg-white rounded-full transition-transform ${isYearly ? 'translate-x-7' : ''}`} />
          </button>
          <span className={`text-lg ${isYearly ? 'font-bold dark:text-white' : 'text-gray-500 dark:text-gray-400'}`}>
            Yearly
            <span className="ml-2 text-sm text-green-600 dark:text-green-400">(Save 17%)</span>
          </span>
        </div>
        
        <div className="grid md:grid-cols-3 gap-8">
          {/* Free - Make it shine! */}
          <div className="border-2 border-green-500 rounded-lg p-6 dark:border-green-500 relative bg-green-50 dark:bg-green-900/20">
            <div className="absolute -top-3 left-1/2 -translate-x-1/2 bg-green-500 text-white px-3 py-1 rounded text-sm">
              Start Here!
            </div>
            <h2 className="text-2xl font-bold mb-2 dark:text-white">Free Forever</h2>
            <p className="text-5xl font-bold mb-6 dark:text-white text-green-600 dark:text-green-400">£0</p>
            <ul className="space-y-3 mb-6 dark:text-gray-300">
              <li className="flex items-start">
                <span className="text-green-500 mr-2">✓</span>
                <span><strong>2 file uploads</strong> (PHP & JS)</span>
              </li>
              <li className="flex items-start">
                <span className="text-green-500 mr-2">✓</span>
                <span><strong>10 packages</strong> tracked</span>
              </li>
              <li className="flex items-start">
                <span className="text-green-500 mr-2">✓</span>
                <span><strong>Edit & sync</strong> docs</span>
              </li>
              <li className="flex items-start">
                <span className="text-green-500 mr-2">✓</span>
                <span><strong>No credit card</strong> required</span>
              </li>
            </ul>
            <button 
              onClick={() => router.visit(route('register'))}
              className="w-full py-3 bg-green-500 text-white rounded-lg hover:bg-green-600 font-bold"
            >
              Start Free Now
            </button>
          </div>

          {/* Pro */}
          <div className="border border-gray-300 dark:border-gray-700 rounded-lg p-6">
            <h2 className="text-2xl font-bold mb-2 dark:text-white">Pro</h2>
            {isYearly ? (
              <>
                <p className="text-4xl font-bold mb-2 dark:text-white">
                  £90<span className="text-lg">/year</span>
                </p>
                <p className="text-sm text-gray-500 line-through mb-4">£180/year</p>
                <p className="text-xs text-green-600 dark:text-green-400 mb-4">£7.50/mo - Save £18/year!</p>
              </>
            ) : (
              <>
                <p className="text-4xl font-bold mb-2 dark:text-white">
                  £9<span className="text-lg">/mo</span>
                </p>
                <p className="text-sm text-gray-500 line-through mb-4">£18/mo</p>
              </>
            )}
            <ul className="space-y-3 mb-6 dark:text-gray-300">
              <li>✓ <strong>Unlimited uploads</strong></li>
              <li>✓ <strong>100 packages</strong></li>
              <li>✓ <strong>3 projects</strong></li>
              <li>✓ Priority support</li>
              <li>✓ Cancel anytime</li>
            </ul>
            <button 
              onClick={() => handleCheckout(isYearly ? 'pro-yearly' : 'pro-monthly')}
              className="w-full py-2 bg-blue-500/90 hover:bg-blue-500 text-white rounded backdrop-blur-sm"
            >
              Upgrade to Pro
            </button>
          </div>

          {/* Lifetime */}
          <div className="border border-gray-300 dark:border-gray-700 rounded-lg p-6">
            <h2 className="text-2xl font-bold mb-2 dark:text-white">Lifetime</h2>
            <p className="text-4xl font-bold mb-2 dark:text-white">£299</p>
            <p className="text-sm text-gray-500 line-through mb-4">£598</p>
            <p className="text-xs text-orange-600 dark:text-orange-400 mb-4">Pay once, use forever*</p>
            <ul className="space-y-3 mb-6 dark:text-gray-300">
              <li>✓ <strong>Unlimited everything</strong></li>
              <li>✓ <strong>Unlimited packages</strong></li>
              <li>✓ <strong>Unlimited projects</strong></li>
              <li>✓ Priority support</li>
              <li>✓ All future updates</li>
            </ul>
            <button 
              onClick={() => handleCheckout('lifetime')}
              className="w-full py-2 bg-orange-400/80 hover:bg-orange-400 text-white rounded backdrop-blur-sm"
            >
              Get Lifetime Access
            </button>
          </div>
        </div>

        <p className="text-center text-gray-500 dark:text-gray-400 mt-12">
          Secure checkout powered by Stripe • 14-day money-back guarantee
        </p>
        <p className="text-center text-xs text-gray-400 dark:text-gray-500 mt-2">
          *Lifetime access valid while product is commercially available
        </p>
      </div>
    </>
  )
}
