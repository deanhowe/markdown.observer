import { Head, Link } from '@inertiajs/react'

export default function FAQ() {
  return (
    <>
      <Head title="FAQ" />
      
      <div className="min-h-screen bg-white dark:bg-gray-900 py-12">
        <div className="container mx-auto px-4 sm:px-6 max-w-3xl">
          <Link href="/" className="text-blue-500 hover:underline mb-6 inline-block">← Back to Home</Link>
          
          <h1 className="text-3xl font-bold mb-8 dark:text-white">Frequently Asked Questions</h1>
          
          <div className="space-y-6">
            <div>
              <h2 className="text-xl font-bold mb-2 dark:text-white">How long is "Lifetime" access?</h2>
              <p className="text-gray-700 dark:text-gray-300">
                Lifetime access is valid for as long as Markdown Observer remains commercially available. 
                This means you'll have access to the service for its entire operational lifespan. 
                There's no fixed end date, but it's tied to the product being actively maintained and available on the market.
              </p>
            </div>

            <div>
              <h2 className="text-xl font-bold mb-2 dark:text-white">Can I cancel my subscription?</h2>
              <p className="text-gray-700 dark:text-gray-300">
                Yes! Pro subscriptions can be cancelled at any time from your billing portal. 
                You'll retain access until the end of your current billing period.
              </p>
            </div>

            <div>
              <h2 className="text-xl font-bold mb-2 dark:text-white">What payment methods do you accept?</h2>
              <p className="text-gray-700 dark:text-gray-300">
                We accept all major credit cards (Visa, Mastercard, American Express) via Stripe. 
                All payments are secure and encrypted.
              </p>
            </div>

            <div>
              <h2 className="text-xl font-bold mb-2 dark:text-white">Do you offer refunds?</h2>
              <p className="text-gray-700 dark:text-gray-300">
                We offer a 14-day money-back guarantee on all plans. If you're not satisfied, 
                contact us within 14 days of purchase for a full refund.
              </p>
            </div>

            <div>
              <h2 className="text-xl font-bold mb-2 dark:text-white">Can I upgrade or downgrade my plan?</h2>
              <p className="text-gray-700 dark:text-gray-300">
                Yes! You can upgrade from Free to Pro, or from Pro to Lifetime at any time. 
                Downgrades take effect at the end of your current billing period.
              </p>
            </div>

            <div>
              <h2 className="text-xl font-bold mb-2 dark:text-white">Is my data secure?</h2>
              <p className="text-gray-700 dark:text-gray-300">
                Yes. All data is encrypted in transit and at rest. We use industry-standard security practices 
                and host on Laravel Cloud with PostgreSQL databases.
              </p>
            </div>

            <div>
              <h2 className="text-xl font-bold mb-2 dark:text-white">Can I export my data?</h2>
              <p className="text-gray-700 dark:text-gray-300">
                Yes! You can export all your documentation at any time in markdown format. 
                Your data is yours.
              </p>
            </div>
          </div>
        </div>
      </div>
    </>
  )
}
