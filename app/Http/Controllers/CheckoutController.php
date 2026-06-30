<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    /**
     * Stripe Checkout for guests AND logged-in users.
     * For guests: Stripe collects email, webhook creates user.
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

        // Guest: Stripe Checkout with email collection
        $params = [
            'success_url' => route('checkout.success').'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('pricing'),
            'customer_creation' => 'always',
        ];

        if ($plan === 'lifetime') {
            return User::checkoutCharge(amount: 29900, name: 'Markdown Observer Pro — Lifetime', quantity: 1, sessionOptions: $params);
        }

        return User::checkout([$priceId => ['quantity' => 1]], array_merge($params, [
            'mode' => 'subscription',
        ]));
    }

    /**
     * After Stripe Checkout: create user from Stripe customer, log them in.
     */
    public function success(Request $request)
    {
        $sessionId = $request->get('session_id');
        if (! $sessionId) {
            return redirect()->route('pricing');
        }

        $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
        $session = $stripe->checkout->sessions->retrieve($sessionId, ['expand' => ['customer']]);

        $customer = $session->customer;
        $email = $customer->email ?? $session->customer_details->email ?? null;

        if (! $email) {
            return redirect()->route('pricing')->with('error', 'Could not retrieve email from Stripe.');
        }

        // Find or create user
        $user = User::where('email', $email)->first();
        if (! $user) {
            $user = User::create([
                'name' => $customer->name ?? explode('@', $email)[0],
                'email' => $email,
                'password' => bcrypt(Str::random(32)),
                'stripe_id' => $customer->id,
            ]);
        } elseif (! $user->stripe_id) {
            $user->update(['stripe_id' => $customer->id]);
        }

        Auth::login($user, remember: true);

        return redirect()->route('dashboard')->with('welcome', true);
    }

    public function portal(Request $request)
    {
        return $request->user()->redirectToBillingPortal(route('dashboard'));
    }
}
