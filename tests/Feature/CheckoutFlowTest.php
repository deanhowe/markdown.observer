<?php

use App\Models\User;
use Stripe\ApiRequestor;
use Stripe\HttpClient\ClientInterface;
use Stripe\Util\CaseInsensitiveArray;

/**
 * Stands in for Stripe's HTTP transport only, so the real Cashier and
 * stripe-php code paths run end to end without touching the network.
 */
class FakeStripeTransport implements ClientInterface
{
    /** @var list<array{method: string, path: string, params: array<string, mixed>}> */
    public array $requests = [];

    /** @var array<string, mixed> What GET /v1/checkout/sessions/{id} returns. */
    public array $session = [];

    public function request($method, $absUrl, $headers, $params, $hasFile, $apiMode = 'v1', $maxNetworkRetries = null)
    {
        $method = strtolower($method);
        $path = (string) parse_url($absUrl, PHP_URL_PATH);
        $this->requests[] = ['method' => $method, 'path' => $path, 'params' => (array) $params];

        $body = match (true) {
            $method === 'post' && $path === '/v1/customers' => ['id' => 'cus_fake', 'object' => 'customer'],
            $method === 'post' && $path === '/v1/checkout/sessions' => [
                'id' => 'cs_test_fake', 'object' => 'checkout.session', 'url' => 'https://checkout.stripe.com/c/pay/cs_test_fake',
            ],
            $method === 'get' && str_starts_with($path, '/v1/checkout/sessions/') => $this->session,
            $method === 'post' && $path === '/v1/billing_portal/sessions' => [
                'id' => 'bps_fake', 'object' => 'billing_portal.session', 'url' => 'https://billing.stripe.com/p/session/fake',
            ],
            $method === 'post' && str_starts_with($path, '/v1/subscriptions/') => [
                'id' => basename($path), 'object' => 'subscription', 'status' => 'active', 'cancel_at_period_end' => true,
            ],
            $method === 'get' && str_starts_with($path, '/v1/subscription_items/') => [
                'id' => basename($path), 'object' => 'subscription_item', 'current_period_end' => now()->addDays(20)->timestamp,
            ],
            default => throw new RuntimeException("Unexpected Stripe call: {$method} {$path}"),
        };

        return [json_encode($body), 200, new CaseInsensitiveArray([])];
    }

    /** @return list<array<string, mixed>> */
    public function paramsFor(string $method, string $path): array
    {
        return array_values(array_map(
            fn (array $r): array => $r['params'],
            array_filter($this->requests, fn (array $r): bool => $r['method'] === $method && $r['path'] === $path),
        ));
    }
}

function paidSession(string $mode, string $customerId, string $email): array
{
    return [
        'id' => 'cs_test_fake',
        'object' => 'checkout.session',
        'status' => 'complete',
        'payment_status' => 'paid',
        'mode' => $mode,
        'customer' => ['id' => $customerId, 'object' => 'customer', 'email' => $email, 'name' => 'Buyer'],
        'customer_details' => ['email' => $email],
    ];
}

beforeEach(function () {
    config([
        'cashier.secret' => 'sk_test_fake',
        'services.stripe.secret' => 'sk_test_fake',
        'services.stripe.price_pro_monthly' => 'price_pro_monthly',
        'services.stripe.price_pro_yearly' => 'price_pro_yearly',
        'services.stripe.price_lifetime' => 'price_lifetime',
    ]);

    $this->stripe = new FakeStripeTransport;
    ApiRequestor::setHttpClient($this->stripe);
});

afterEach(fn () => ApiRequestor::setHttpClient(null));

test('an Inertia checkout request hands the browser to Stripe with a full-page visit', function () {
    // The pricing buttons use Inertia's router (an XHR). A 303 would be
    // followed by the XHR itself and blocked by CORS, so no browser could
    // reach Stripe. 409 + X-Inertia-Location makes the client set
    // window.location instead.
    $this->post(route('checkout', 'pro-monthly'), [], ['X-Inertia' => 'true'])
        ->assertStatus(409)
        ->assertHeader('X-Inertia-Location', 'https://checkout.stripe.com/c/pay/cs_test_fake');
});

test('guest checkout opens a promo-code-enabled subscription that returns through the success page', function () {
    $this->post(route('checkout', 'pro-yearly'))
        ->assertRedirect('https://checkout.stripe.com/c/pay/cs_test_fake');

    $params = $this->stripe->paramsFor('post', '/v1/checkout/sessions')[0];
    expect($params['mode'])->toBe('subscription')
        ->and($params['line_items'][0]['price'])->toBe('price_pro_yearly')
        ->and($params['allow_promotion_codes'])->toBeIn([true, 'true'])
        ->and($params['success_url'])->toEndWith('/checkout/success?session_id={CHECKOUT_SESSION_ID}');
});

test('logged-in checkout creates the Stripe customer and also returns through the success page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->post(route('checkout', 'lifetime'))
        ->assertRedirect('https://checkout.stripe.com/c/pay/cs_test_fake');

    $params = $this->stripe->paramsFor('post', '/v1/checkout/sessions')[0];
    expect($params['customer'])->toBe('cus_fake')
        ->and($params['mode'])->toBe('payment')
        ->and($params['success_url'])->toEndWith('/checkout/success?session_id={CHECKOUT_SESSION_ID}');
    expect($user->fresh()->stripe_id)->toBe('cus_fake');
});

test('an existing Pro subscriber is sent to manage billing instead of being billed twice', function () {
    $user = User::factory()->create(['subscription_tier' => 'pro', 'stripe_id' => 'cus_existing']);

    $this->actingAs($user)->post(route('checkout', 'pro-monthly'))
        ->assertRedirect('https://billing.stripe.com/p/session/fake');

    expect($this->stripe->paramsFor('post', '/v1/checkout/sessions'))->toBeEmpty();
});

test('a lifetime owner is never sent to checkout again', function () {
    $user = User::factory()->create(['subscription_tier' => 'lifetime', 'stripe_id' => 'cus_owner']);

    $this->actingAs($user)->post(route('checkout', 'pro-monthly'))->assertRedirect(route('dashboard'));

    expect($this->stripe->requests)->toBeEmpty();
});

test('the success page fulfils a new guest\'s Pro subscription without waiting for the webhook', function () {
    $this->stripe->session = paidSession('subscription', 'cus_newguest', 'new@example.com');

    $this->get(route('checkout.success', ['session_id' => 'cs_test_fake']))->assertRedirect(route('dashboard'));

    $user = User::where('email', 'new@example.com')->firstOrFail();
    expect($user->subscription_tier)->toBe('pro')
        ->and($user->doc_limit)->toBe(User::TIERS['pro']['doc_limit'])
        // Used to be silently dropped (not mass-assignable), so later
        // subscription webhooks (renewal, cancel) could never find the buyer.
        ->and($user->stripe_id)->toBe('cus_newguest');
    $this->assertAuthenticatedAs($user);
});

test('the success page grants lifetime and never auto-logs-in an existing account', function () {
    $user = User::factory()->create(['email' => 'owner@example.com']);
    $this->stripe->session = paidSession('payment', 'cus_owner', 'owner@example.com');

    $this->get(route('checkout.success', ['session_id' => 'cs_test_fake']))->assertRedirect(route('login'));

    expect($user->fresh())
        ->subscription_tier->toBe('lifetime')
        ->doc_limit->toBe(User::UNLIMITED)
        ->stripe_id->toBe('cus_owner');
    $this->assertGuest();
});

test('a success link can only be redeemed once', function () {
    $this->stripe->session = paidSession('subscription', 'cus_replay', 'replay@example.com');

    $this->get(route('checkout.success', ['session_id' => 'cs_test_fake']))->assertRedirect(route('dashboard'));
    auth()->logout();

    $this->get(route('checkout.success', ['session_id' => 'cs_test_fake']))->assertRedirect(route('login'));
    $this->assertGuest();
});

test('an unpaid session grants nothing', function () {
    $this->stripe->session = ['status' => 'open', 'payment_status' => 'unpaid'] + paidSession('subscription', 'cus_x', 'unpaid@example.com');

    $this->get(route('checkout.success', ['session_id' => 'cs_test_fake']))->assertRedirect(route('pricing'));

    expect(User::where('email', 'unpaid@example.com')->exists())->toBeFalse();
});

test('upgrading from Pro to Lifetime stops Pro billing at the end of the paid period', function () {
    $user = User::factory()->create(['subscription_tier' => 'pro', 'stripe_id' => 'cus_upgrader']);
    $subscription = $user->subscriptions()->create([
        'type' => 'default', 'stripe_id' => 'sub_pro', 'stripe_status' => 'active',
        'stripe_price' => 'price_pro_monthly', 'quantity' => 1,
    ]);
    $subscription->items()->create([
        'stripe_id' => 'si_pro', 'stripe_product' => 'prod_pro', 'stripe_price' => 'price_pro_monthly', 'quantity' => 1,
    ]);
    $this->stripe->session = paidSession('payment', 'cus_upgrader', $user->email);

    $this->actingAs($user)->get(route('checkout.success', ['session_id' => 'cs_test_fake']))
        ->assertRedirect(route('dashboard'));

    expect($user->fresh()->subscription_tier)->toBe('lifetime');
    expect($this->stripe->paramsFor('post', '/v1/subscriptions/sub_pro')[0]['cancel_at_period_end'])->toBeIn([true, 'true']);
    expect($subscription->fresh()->onGracePeriod())->toBeTrue();
});
