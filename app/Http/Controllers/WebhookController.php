<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Http\Controllers\WebhookController as CashierWebhookController;

/**
 * Extends Cashier's webhook handling to keep `subscription_tier` (the field
 * every feature-gate actually reads — see SteeringDocController, HealthController)
 * in sync with what Stripe says happened. Cashier's own tables (subscriptions,
 * subscription_items) are still updated by the parent methods; this class only
 * adds the tier bookkeeping Cashier has no opinion on.
 *
 * Registered at POST /stripe/webhook via routes/web.php, with Cashier's own
 * auto-registration disabled (Cashier::ignoreRoutes() in AppServiceProvider)
 * so this controller — not vendor's — receives the request.
 */
class WebhookController extends CashierWebhookController
{
    /**
     * One-time payment (lifetime plan) completing checkout. Subscriptions
     * (pro-monthly/yearly) don't need this — Stripe fires
     * customer.subscription.created for those, handled below.
     */
    protected function handleCheckoutSessionCompleted(array $payload)
    {
        $session = $payload['data']['object'];

        if (($session['mode'] ?? null) !== 'payment') {
            return $this->successMethod();
        }

        if (($session['payment_status'] ?? null) !== 'paid') {
            return $this->successMethod();
        }

        $customerId = $session['customer'] ?? null;
        if (! $customerId) {
            return $this->successMethod();
        }

        $user = $this->getUserByStripeId($customerId);

        if (! $user) {
            // Guest checkout: CheckoutController@success creates the user
            // from this same session, but webhook delivery can race ahead of
            // that redirect. Fall back to matching on the Stripe customer
            // email so a fast webhook doesn't get silently dropped.
            $email = $session['customer_details']['email'] ?? null;
            $user = $email ? User::where('email', $email)->first() : null;
        }

        if ($user) {
            $user->forceFill([
                'subscription_tier' => 'lifetime',
                'upload_limit' => 999,
                'doc_limit' => 999,
                'stripe_id' => $user->stripe_id ?? $customerId,
            ])->save();

            Log::info('markdown.observer: lifetime tier granted', ['user_id' => $user->id]);
        } else {
            Log::warning('markdown.observer: checkout.session.completed (payment) with no matching user', [
                'stripe_customer' => $customerId,
            ]);
        }

        return $this->successMethod();
    }

    /**
     * New subscription (pro-monthly or pro-yearly). Let Cashier create its
     * subscription/subscription_items rows first, then set the tier that the
     * rest of the app actually checks.
     */
    protected function handleCustomerSubscriptionCreated(array $payload)
    {
        $response = parent::handleCustomerSubscriptionCreated($payload);

        $this->syncTierFromSubscription($payload);

        return $response;
    }

    /**
     * Plan changes (e.g. monthly <-> yearly) or a resumed subscription still
     * belong on the pro tier — re-sync in case this arrives before `created`.
     */
    protected function handleCustomerSubscriptionUpdated(array $payload)
    {
        $response = parent::handleCustomerSubscriptionUpdated($payload);

        $status = $payload['data']['object']['status'] ?? null;
        if (in_array($status, ['active', 'trialing'], true)) {
            $this->syncTierFromSubscription($payload);
        }

        return $response;
    }

    /**
     * Subscription canceled/expired: drop back to free. Lifetime purchasers
     * never have a Cashier subscription record, so this never touches them.
     */
    protected function handleCustomerSubscriptionDeleted(array $payload)
    {
        $response = parent::handleCustomerSubscriptionDeleted($payload);

        if ($user = $this->getUserByStripeId($payload['data']['object']['customer'])) {
            if ($user->subscription_tier === 'pro') {
                $user->forceFill([
                    'subscription_tier' => 'free',
                    'upload_limit' => 2,
                    'doc_limit' => 10,
                ])->save();

                Log::info('markdown.observer: downgraded to free tier', ['user_id' => $user->id]);
            }
        }

        return $response;
    }

    private function syncTierFromSubscription(array $payload): void
    {
        $data = $payload['data']['object'];
        $customerId = $data['customer'] ?? null;
        if (! $customerId) {
            return;
        }

        $user = $this->getUserByStripeId($customerId);
        if (! $user) {
            return;
        }

        // Lifetime is a one-time payment, never a subscription — anything
        // reaching here is pro-monthly or pro-yearly by definition.
        if ($user->subscription_tier === 'lifetime') {
            return;
        }

        $user->forceFill([
            'subscription_tier' => 'pro',
            // Pricing page advertises "Unlimited uploads" + "100 packages"
            // for Pro — 999 is the same unlimited-sentinel lifetime uses.
            'upload_limit' => 999,
            'doc_limit' => 100,
        ])->save();

        Log::info('markdown.observer: pro tier granted', ['user_id' => $user->id]);
    }
}
