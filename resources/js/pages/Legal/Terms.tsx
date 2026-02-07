import { Head, Link } from '@inertiajs/react'

export default function Terms() {
  return (
    <>
      <Head title="Terms & Conditions" />
      
      <div className="min-h-screen bg-white dark:bg-gray-900 py-12">
        <div className="container mx-auto px-4 sm:px-6 max-w-3xl">
          <Link href="/" className="text-blue-500 hover:underline mb-6 inline-block">← Back to Home</Link>
          
          <h1 className="text-3xl font-bold mb-8 dark:text-white">Terms & Conditions</h1>
          
          <div className="prose dark:prose-invert max-w-none">
            <p className="text-gray-700 dark:text-gray-300 mb-4">Last updated: February 7, 2026</p>

            <h2 className="text-xl font-bold mt-6 mb-3 dark:text-white">1. Acceptance of Terms</h2>
            <p className="text-gray-700 dark:text-gray-300 mb-4">
              By accessing and using Markdown Observer, you accept and agree to be bound by these Terms and Conditions.
            </p>

            <h2 className="text-xl font-bold mt-6 mb-3 dark:text-white">2. Service Description</h2>
            <p className="text-gray-700 dark:text-gray-300 mb-4">
              Markdown Observer provides package documentation management and AI steering docs search services. 
              We reserve the right to modify, suspend, or discontinue any part of the service at any time.
            </p>

            <h2 className="text-xl font-bold mt-6 mb-3 dark:text-white">3. Lifetime Access</h2>
            <p className="text-gray-700 dark:text-gray-300 mb-4">
              "Lifetime" access means access for the duration that Markdown Observer remains commercially available 
              and operational. This is not a guarantee of perpetual service, but rather access for the product's 
              commercial lifespan.
            </p>

            <h2 className="text-xl font-bold mt-6 mb-3 dark:text-white">4. User Accounts</h2>
            <p className="text-gray-700 dark:text-gray-300 mb-4">
              You are responsible for maintaining the confidentiality of your account credentials and for all 
              activities that occur under your account.
            </p>

            <h2 className="text-xl font-bold mt-6 mb-3 dark:text-white">5. Payment & Refunds</h2>
            <p className="text-gray-700 dark:text-gray-300 mb-4">
              All payments are processed securely through Stripe. We offer a 14-day money-back guarantee on all plans. 
              Subscriptions renew automatically unless cancelled.
            </p>

            <h2 className="text-xl font-bold mt-6 mb-3 dark:text-white">6. Acceptable Use</h2>
            <p className="text-gray-700 dark:text-gray-300 mb-4">
              You agree not to misuse the service, including but not limited to: attempting to gain unauthorized access, 
              interfering with service operation, or using the service for illegal purposes.
            </p>

            <h2 className="text-xl font-bold mt-6 mb-3 dark:text-white">7. Intellectual Property</h2>
            <p className="text-gray-700 dark:text-gray-300 mb-4">
              You retain ownership of your content. We retain ownership of the service and its original content. 
              Documentation fetched from third-party sources remains subject to their respective licenses.
            </p>

            <h2 className="text-xl font-bold mt-6 mb-3 dark:text-white">8. Limitation of Liability</h2>
            <p className="text-gray-700 dark:text-gray-300 mb-4">
              Markdown Observer is provided "as is" without warranties of any kind. We are not liable for any 
              indirect, incidental, or consequential damages arising from your use of the service.
            </p>

            <h2 className="text-xl font-bold mt-6 mb-3 dark:text-white">9. Changes to Terms</h2>
            <p className="text-gray-700 dark:text-gray-300 mb-4">
              We reserve the right to modify these terms at any time. Continued use of the service after changes 
              constitutes acceptance of the new terms.
            </p>

            <h2 className="text-xl font-bold mt-6 mb-3 dark:text-white">10. Contact</h2>
            <p className="text-gray-700 dark:text-gray-300 mb-4">
              For questions about these terms, contact us at: support@markdown.observer
            </p>
          </div>
        </div>
      </div>
    </>
  )
}
