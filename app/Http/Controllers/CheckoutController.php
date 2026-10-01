<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Laravel\Cashier\Checkout;

class CheckoutController extends Controller
{
    /**
     * Start Stripe Checkout for guests AND logged-in users.
     *
     * The pricing buttons post here through Inertia's router, which is an XHR.
     * A plain 303 to checkout.stripe.com gets followed *by the XHR*,
     * cross-origin, and the browser blocks it with a CORS error, so the buyer
     * never reaches Stripe. That was live in production until 2026-10-01: the
     * server created real sessions but no browser could get to them.
     * Inertia::location() answers an Inertia request with 409 +
     * X-Inertia-Location, which makes the client do a full-page visit; any
     * other request still gets an ordinary redirect.
     */
    public function checkout(Request $request, string $plan)
    {
        $priceId = [
            'pro-monthly' => config('services.stripe.price_pro_monthly'),
            'pro-yearly' => config('services.stripe.price_pro_yearly'),
            'lifetime' => config('services.stripe.price_lifetime'),
        ][$plan] ?? null;

        if (! $priceId) {
            abort(404, 'Unknown plan');
        }

        // Every buyer comes back through success(), which fulfils the order
        // itself rather than relying only on the webhook arriving in time.
        $urls = [
            'success_url' => route('checkout.success').'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('pricing'),
        ];

        if ($user = $request->user()) {
            if ($user->subscription_tier === 'lifetime') {
                return redirect()->route('dashboard')->with('status', 'You already have lifetime access.');
            }

            // A second Pro checkout would open a second subscription and bill
            // them twice, so existing subscribers go to manage their plan.
            if ($user->subscription_tier === 'pro' && $plan !== 'lifetime') {
                return Inertia::location($user->billingPortalUrl(route('dashboard')));
            }

            $checkout = $plan === 'lifetime'
                ? Checkout::customer($user)->allowPromotionCodes()->create([$priceId => 1], $urls)
                : $user->newSubscription('default', $priceId)->allowPromotionCodes()->checkout($urls);

            return Inertia::location($checkout->url);
        }

        // Guest: Stripe collects the email and success() creates the account.
        // Checkout::guest() is Cashier's entry point for a session with no
        // owner. CheckoutBuilder::create() maps a string-keyed item to
        // ['price' => key, 'quantity' => value], so the value is the raw int.
        $checkout = $plan === 'lifetime'
            // Only valid in payment mode; subscriptions always create a customer.
            ? Checkout::guest()->allowPromotionCodes()->create([$priceId => 1], $urls + ['customer_creation' => 'always'])
            : Checkout::guest()->allowPromotionCodes()->create([$priceId => 1], $urls + ['mode' => 'subscription']);

        return Inertia::location($checkout->url);
    }

    /**
     * Back from Stripe Checkout: fulfil the order here, not only in the webhook.
     *
     * Stripe often delivers the webhook before this redirect, and for a new
     * guest there is no account yet for it to upgrade, so the buyer stayed on
     * the free plan. Both paths grant idempotently.
     *
     * Auto-login is only granted for accounts created right here, from a
     * session Stripe confirms is complete AND paid, and each session id can
     * mint a login exactly once. Existing accounts are never auto-logged-in
     * from a checkout redirect: the buyer hasn't proven they own them.
     */
    public function success(Request $request)
    {
        $sessionId = $request->get('session_id');
        if (! $sessionId) {
            return redirect()->route('pricing');
        }

        $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));

        try {
            $session = $stripe->checkout->sessions->retrieve($sessionId, ['expand' => ['customer']]);
        } catch (\Stripe\Exception\ApiErrorException) {
            return redirect()->route('pricing');
        }

        if ($session->status !== 'complete'
            || ! in_array($session->payment_status, ['paid', 'no_payment_required'], true)) {
            return redirect()->route('pricing')->with('error', 'Payment was not completed.');
        }

        $customer = $session->customer;
        $customerId = is_object($customer) ? $customer->id : $customer;
        $email = (is_object($customer) ? $customer->email : null)
            ?? $session->customer_details->email
            ?? null;

        if (! $email) {
            return redirect()->route('pricing')->with('error', 'Could not retrieve email from Stripe.');
        }

        // Session ids appear in URLs, logs and browser history, so burn each
        // one after first use: a replayed link can't mint a session.
        if (! Cache::add('checkout.session-used.'.$sessionId, true, now()->addDay())) {
            return Auth::check()
                ? redirect()->route('dashboard')
                : redirect()->route('login')->with('status', 'Purchase confirmed — please log in to continue.');
        }

        // Match the Stripe customer first (a logged-in buyer's app email may
        // have changed since their customer was created), then the email.
        $user = ($customerId ? User::where('stripe_id', $customerId)->first() : null)
            ?? User::where('email', $email)->first();

        $isNewAccount = false;
        if (! $user) {
            $user = User::create([
                'name' => (is_object($customer) ? $customer->name : null) ?? explode('@', $email)[0],
                'email' => $email,
                'password' => bcrypt(Str::random(32)),
            ]);
            $isNewAccount = true;
        }

        // forceFill, not create()/update(): stripe_id isn't mass-assignable,
        // so it used to be silently dropped, and the subscription webhooks
        // (which look buyers up by stripe_id) could never find a guest.
        if (! $user->stripe_id && $customerId) {
            $user->forceFill(['stripe_id' => $customerId])->save();
        }

        // Same mapping the webhook uses: a one-time payment is Lifetime and a
        // subscription is Pro. Never downgrade a lifetime owner.
        if ($session->mode === 'payment') {
            $user->grantLifetime();
        } elseif ($user->subscription_tier !== 'lifetime') {
            $user->applyTier('pro');
        }

        if ($isNewAccount) {
            Auth::login($user, remember: true);
        }

        if (Auth::id() === $user->id) {
            return redirect()->route('dashboard')->with('welcome', true);
        }

        return redirect()->route('login')
            ->with('status', 'Purchase confirmed — log in to access your account.');
    }

    /**
     * Stripe's billing portal: change plan, update card, cancel. Inertia-safe
     * for the same reason as checkout(): it's an external URL.
     */
    public function portal(Request $request)
    {
        $user = $request->user();

        if (! $user->hasStripeId()) {
            return redirect()->route('pricing');
        }

        return Inertia::location($user->billingPortalUrl(route('dashboard')));
    }
}
