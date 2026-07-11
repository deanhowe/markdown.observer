<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    /**
     * Stripe Checkout for guests AND logged-in users.
     * For guests: Stripe collects email, success() creates the user
     * after verifying the session is complete and paid.
     * For users: normal Cashier checkout.
     */
    public function checkout(Request $request, string $plan)
    {
        $priceMap = [
            'pro-monthly' => config('services.stripe.price_pro_monthly'),
            'pro-yearly' => config('services.stripe.price_pro_yearly'),
            'lifetime' => config('services.stripe.price_lifetime'),
        ];

        $priceId = $priceMap[$plan] ?? null;
        if (! $priceId) {
            abort(404, 'Unknown plan');
        }

        // Logged-in user: use Cashier
        if ($user = $request->user()) {
            if ($plan === 'lifetime') {
                return $user->checkout([$priceId], [
                    'success_url' => route('dashboard').'?welcome=1',
                    'cancel_url' => route('pricing'),
                ]);
            }

            return $user->newSubscription('default', $priceId)
                ->checkout([
                    'success_url' => route('dashboard').'?welcome=1',
                    'cancel_url' => route('pricing'),
                ]);
        }

        // Guest: Stripe Checkout with email collection.
        // Checkout::guest() (not User::checkout — that's an INSTANCE method
        // on Billable for authenticated owners and cannot be called
        // statically) is Cashier's documented entry point for a checkout
        // session with no owner attached.
        $params = [
            'success_url' => route('checkout.success').'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('pricing'),
        ];

        if ($plan === 'lifetime') {
            // customer_creation is only valid in `payment` mode (Stripe
            // rejects it in `subscription` mode — subscriptions always
            // create a customer implicitly).
            $params['customer_creation'] = 'always';

            // Price comes from the configured Stripe Price, same as the
            // logged-in flow — an ad-hoc amount here would silently diverge
            // when the price changes in the Stripe dashboard.
            return \Laravel\Cashier\Checkout::guest()->create([$priceId => 1], $params);
        }

        // CheckoutBuilder::create() maps a string-keyed item as
        // ['price' => key, 'quantity' => value] — the value must be the raw
        // quantity int, not a nested ['quantity' => 1] array (Stripe's API
        // rejected that with "Invalid integer").
        return \Laravel\Cashier\Checkout::guest()->create([$priceId => 1], array_merge($params, [
            'mode' => 'subscription',
        ]));
    }

    /**
     * After Stripe Checkout: create user from Stripe customer.
     *
     * Auto-login is only granted for accounts created right here, from a
     * session Stripe confirms is complete AND paid, and each session id can
     * mint a login exactly once. Existing accounts are never auto-logged-in
     * from a checkout redirect — the buyer hasn't proven they own them.
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

        // Session ids appear in URLs, logs and browser history — burn each
        // one after first use so a replayed link can't mint a session.
        if (! Cache::add('checkout.session-used.'.$sessionId, true, now()->addDay())) {
            return redirect()->route('login')
                ->with('status', 'Purchase confirmed — please log in to continue.');
        }

        $user = User::where('email', $email)->first();

        if (! $user) {
            $user = User::create([
                'name' => (is_object($customer) ? $customer->name : null) ?? explode('@', $email)[0],
                'email' => $email,
                'password' => bcrypt(Str::random(32)),
                'stripe_id' => $customerId,
            ]);

            Auth::login($user, remember: true);

            return redirect()->route('dashboard')->with('welcome', true);
        }

        if (! $user->stripe_id) {
            $user->update(['stripe_id' => $customerId]);
        }

        if (Auth::id() === $user->id) {
            return redirect()->route('dashboard')->with('welcome', true);
        }

        return redirect()->route('login')
            ->with('status', 'Purchase confirmed — log in to access your account.');
    }

    public function portal(Request $request)
    {
        return $request->user()->redirectToBillingPortal(route('dashboard'));
    }
}
