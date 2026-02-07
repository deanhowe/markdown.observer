import { Head, Link } from '@inertiajs/react'

export default function Privacy() {
  return (
    <>
      <Head title="Privacy Policy" />
      
      <div className="min-h-screen bg-white dark:bg-gray-900 py-12">
        <div className="container mx-auto px-4 sm:px-6 max-w-3xl">
          <Link href="/" className="text-blue-500 hover:underline mb-6 inline-block">← Back to Home</Link>
          
          <h1 className="text-3xl font-bold mb-8 dark:text-white">Privacy Policy</h1>
          
          <div className="prose dark:prose-invert max-w-none">
            <p className="text-gray-700 dark:text-gray-300 mb-4">Last updated: February 7, 2026</p>

            <h2 className="text-xl font-bold mt-6 mb-3 dark:text-white">1. Information We Collect</h2>
            <p className="text-gray-700 dark:text-gray-300 mb-4">
              We collect information you provide directly to us, including:
            </p>
            <ul className="list-disc pl-6 text-gray-700 dark:text-gray-300 mb-4">
              <li>Account information (name, email, password)</li>
              <li>Payment information (processed securely by Stripe)</li>
              <li>Package documentation files you upload</li>
              <li>Usage data and analytics</li>
            </ul>

            <h2 className="text-xl font-bold mt-6 mb-3 dark:text-white">2. How We Use Your Information</h2>
            <p className="text-gray-700 dark:text-gray-300 mb-4">
              We use the information we collect to:
            </p>
            <ul className="list-disc pl-6 text-gray-700 dark:text-gray-300 mb-4">
              <li>Provide, maintain, and improve our services</li>
              <li>Process transactions and send related information</li>
              <li>Send technical notices and support messages</li>
              <li>Respond to your comments and questions</li>
              <li>Monitor and analyze trends and usage</li>
            </ul>

            <h2 className="text-xl font-bold mt-6 mb-3 dark:text-white">3. Data Storage & Security</h2>
            <p className="text-gray-700 dark:text-gray-300 mb-4">
              Your data is stored securely on Laravel Cloud infrastructure with PostgreSQL databases. 
              All data is encrypted in transit (HTTPS) and at rest. We implement industry-standard security measures 
              to protect your information.
            </p>

            <h2 className="text-xl font-bold mt-6 mb-3 dark:text-white">4. Data Sharing</h2>
            <p className="text-gray-700 dark:text-gray-300 mb-4">
              We do not sell your personal information. We may share your information with:
            </p>
            <ul className="list-disc pl-6 text-gray-700 dark:text-gray-300 mb-4">
              <li>Service providers (Stripe for payments, Laravel Cloud for hosting)</li>
              <li>Law enforcement when required by law</li>
            </ul>

            <h2 className="text-xl font-bold mt-6 mb-3 dark:text-white">5. Your Rights</h2>
            <p className="text-gray-700 dark:text-gray-300 mb-4">
              You have the right to:
            </p>
            <ul className="list-disc pl-6 text-gray-700 dark:text-gray-300 mb-4">
              <li>Access your personal data</li>
              <li>Correct inaccurate data</li>
              <li>Request deletion of your data</li>
              <li>Export your data</li>
              <li>Opt-out of marketing communications</li>
            </ul>

            <h2 className="text-xl font-bold mt-6 mb-3 dark:text-white">6. Cookies</h2>
            <p className="text-gray-700 dark:text-gray-300 mb-4">
              We use cookies and similar technologies to maintain your session, remember your preferences, 
              and analyze site traffic. You can control cookies through your browser settings.
            </p>

            <h2 className="text-xl font-bold mt-6 mb-3 dark:text-white">7. Third-Party Services</h2>
            <p className="text-gray-700 dark:text-gray-300 mb-4">
              We use third-party services including Stripe (payments) and Laravel Cloud (hosting). 
              These services have their own privacy policies governing their use of your information.
            </p>

            <h2 className="text-xl font-bold mt-6 mb-3 dark:text-white">8. Children's Privacy</h2>
            <p className="text-gray-700 dark:text-gray-300 mb-4">
              Our service is not intended for children under 13. We do not knowingly collect information 
              from children under 13.
            </p>

            <h2 className="text-xl font-bold mt-6 mb-3 dark:text-white">9. Changes to Privacy Policy</h2>
            <p className="text-gray-700 dark:text-gray-300 mb-4">
              We may update this privacy policy from time to time. We will notify you of any changes by 
              posting the new policy on this page.
            </p>

            <h2 className="text-xl font-bold mt-6 mb-3 dark:text-white">10. Contact Us</h2>
            <p className="text-gray-700 dark:text-gray-300 mb-4">
              For privacy-related questions, contact us at: privacy@markdown.observer
            </p>
          </div>
        </div>
      </div>
    </>
  )
}
