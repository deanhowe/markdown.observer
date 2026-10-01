import { Head, Link, router } from '@inertiajs/react'
import { useState } from 'react'

type Plan = 'pro-monthly' | 'pro-yearly' | 'lifetime'
type Limits = { upload_limit: number; doc_limit: number; steering_collections: number }

interface PricingProps {
  // Same table the feature gates enforce (App\Models\User::TIERS).
  tiers: Record<'free' | 'pro' | 'lifetime', Limits>
  unlimited: number
  auth?: { user?: { subscription_tier?: string } | null }
}

export default function Pricing({ tiers, unlimited, auth }: PricingProps) {
  const [isYearly, setIsYearly] = useState(false)
  const [busy, setBusy] = useState<Plan | null>(null)
  const current = auth?.user?.subscription_tier ?? null

  const count = (n: number, noun: string) =>
    n >= unlimited ? `Unlimited ${noun}s` : `${n} ${noun}${n === 1 ? '' : 's'}`

  const checkout = (plan: Plan) =>
    router.post(route('checkout', { plan }), {}, {
      onStart: () => setBusy(plan),
      onFinish: () => setBusy(null),
    })

  const feature = (text: string, tone = 'text-green-500') => (
    <li key={text} className="flex items-start">
      <span className={`${tone} mr-2`} aria-hidden="true">✓</span>
      <span>{text}</span>
    </li>
  )

  const disabledButton = (label: string) => (
    <button disabled className="w-full py-3 rounded-lg bg-gray-200 text-gray-500 dark:bg-gray-700 dark:text-gray-400 font-semibold cursor-default">
      {label}
    </button>
  )

  return (
    <>
      <Head title="Pricing" />

      <div className="max-w-6xl mx-auto p-6">
        <h1 className="text-3xl font-bold text-center mb-3 dark:text-white">Simple, honest pricing</h1>
        <p className="text-center text-gray-600 dark:text-gray-300 max-w-2xl mx-auto mb-10">
          Your package docs and AI steering docs as Markdown you own: edit them, keep their history, and hand them to Claude, Cursor or Kiro.
        </p>

        {/* Billing toggle */}
        <div className="flex justify-center items-center gap-4 mb-12">
          <span className={`text-lg ${!isYearly ? 'font-bold dark:text-white' : 'text-gray-500 dark:text-gray-400'}`}>Monthly</span>
          <button
            type="button"
            role="switch"
            aria-checked={isYearly}
            aria-label="Bill yearly"
            onClick={() => setIsYearly(!isYearly)}
            className="relative w-14 h-7 bg-gray-300 dark:bg-gray-600 rounded-full transition-colors"
          >
            <div className={`absolute top-1 left-1 w-5 h-5 bg-white rounded-full transition-transform ${isYearly ? 'translate-x-7' : ''}`} />
          </button>
          <span className={`text-lg ${isYearly ? 'font-bold dark:text-white' : 'text-gray-500 dark:text-gray-400'}`}>
            Yearly
            <span className="ml-2 text-sm text-green-600 dark:text-green-400">(save 17%)</span>
          </span>
        </div>

        <div className="grid md:grid-cols-3 gap-8">
          {/* Free */}
          <div className="border-2 border-green-500 rounded-lg p-6 relative bg-green-50 dark:bg-green-900/20">
            <h2 className="text-2xl font-bold mb-2 dark:text-white">Free</h2>
            <p className="text-5xl font-bold mb-6 text-green-600 dark:text-green-400">£0</p>
            <ul className="space-y-3 mb-6 dark:text-gray-300">
              {feature(`${count(tiers.free.doc_limit, 'package')} tracked`)}
              {feature('composer.json & package.json uploads')}
              {feature(count(tiers.free.steering_collections, 'AI steering-doc collection'))}
              {feature('TipTap editor with version history')}
              {feature('No credit card needed')}
            </ul>
            {current
              ? disabledButton(current === 'free' ? 'Your current plan' : 'Included')
              : (
                <Link href={route('register')} className="block w-full py-3 text-center bg-green-500 text-white rounded-lg hover:bg-green-600 font-bold">
                  Start free
                </Link>
              )}
          </div>

          {/* Pro */}
          <div className="border border-gray-300 dark:border-gray-700 rounded-lg p-6">
            <h2 className="text-2xl font-bold mb-2 dark:text-white">Pro</h2>
            {isYearly ? (
              <>
                <p className="text-4xl font-bold mb-1 dark:text-white">£90<span className="text-lg">/year</span></p>
                <p className="text-sm text-green-600 dark:text-green-400 mb-5">£7.50 a month, billed yearly</p>
              </>
            ) : (
              <>
                <p className="text-4xl font-bold mb-1 dark:text-white">£9<span className="text-lg">/month</span></p>
                <p className="text-sm text-gray-500 dark:text-gray-400 mb-5">Billed monthly</p>
              </>
            )}
            <ul className="space-y-3 mb-6 dark:text-gray-300">
              {feature('Everything in Free', 'text-blue-500')}
              {feature(`${count(tiers.pro.doc_limit, 'package')} tracked`, 'text-blue-500')}
              {feature(count(tiers.pro.upload_limit, 'upload'), 'text-blue-500')}
              {feature(count(tiers.pro.steering_collections, 'AI steering-doc collection'), 'text-blue-500')}
              {feature('Priority support', 'text-blue-500')}
              {feature('Cancel any time from your billing page', 'text-blue-500')}
            </ul>
            {current === 'lifetime' ? disabledButton('Included in Lifetime')
              : current === 'pro' ? (
                <Link href={route('billing.portal')} className="block w-full py-3 text-center border-2 border-blue-500 text-blue-600 dark:text-blue-400 rounded-lg font-semibold hover:bg-blue-50 dark:hover:bg-blue-900/20">
                  Manage subscription
                </Link>
              ) : (
                <button
                  type="button"
                  disabled={busy !== null}
                  onClick={() => checkout(isYearly ? 'pro-yearly' : 'pro-monthly')}
                  className="w-full py-3 bg-blue-500 hover:bg-blue-600 disabled:opacity-60 text-white rounded-lg font-bold"
                >
                  {busy?.startsWith('pro') ? 'Opening secure checkout…' : 'Upgrade to Pro'}
                </button>
              )}
          </div>

          {/* Lifetime */}
          <div className="border-2 border-orange-400 dark:border-orange-500 rounded-lg p-6">
            <h2 className="text-2xl font-bold mb-2 dark:text-white">Lifetime</h2>
            <p className="text-4xl font-bold mb-1 dark:text-white">£299</p>
            <p className="text-sm text-orange-600 dark:text-orange-400 mb-5">Pay once, no subscription*</p>
            <ul className="space-y-3 mb-6 dark:text-gray-300">
              {feature('Everything in Pro', 'text-orange-500')}
              {feature(`${count(tiers.lifetime.doc_limit, 'package')} tracked`, 'text-orange-500')}
              {feature(count(tiers.lifetime.steering_collections, 'AI steering-doc collection'), 'text-orange-500')}
              {feature('All future updates', 'text-orange-500')}
            </ul>
            {current === 'lifetime' ? disabledButton('You own Lifetime') : (
              <>
                <button
                  type="button"
                  disabled={busy !== null}
                  onClick={() => checkout('lifetime')}
                  className="w-full py-3 bg-orange-500 hover:bg-orange-600 disabled:opacity-60 text-white rounded-lg font-bold"
                >
                  {busy === 'lifetime' ? 'Opening secure checkout…' : 'Get Lifetime access'}
                </button>
                {current === 'pro' && (
                  <p className="text-xs text-gray-500 dark:text-gray-400 mt-3">
                    Upgrading from Pro? Your subscription stops at the end of the period you've already paid for.
                  </p>
                )}
              </>
            )}
          </div>
        </div>

        <p className="text-center text-gray-500 dark:text-gray-400 mt-12">
          Secure checkout by Stripe • 14-day money-back guarantee • Got a promo code? Enter it at checkout.
        </p>
        <p className="text-center text-xs text-gray-400 dark:text-gray-500 mt-2">
          *Lifetime access lasts for as long as Markdown Observer is commercially available.
        </p>
      </div>
    </>
  )
}
