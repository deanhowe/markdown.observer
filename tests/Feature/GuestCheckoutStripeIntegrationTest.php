<?php

/**
 * Regression guard for a real bug found and fixed 2026-07-11: guest checkout
 * called `User::checkout(...)` statically. checkout() is an INSTANCE method
 * on Cashier's Billable trait (used correctly elsewhere in this file for
 * logged-in users) — calling it statically threw
 * `Error: Non-static method App\Models\User::checkout() cannot be called
 * statically` on every single guest checkout attempt, for every plan.
 *
 * The fix uses `Checkout::guest()->create(...)`, Cashier's actual documented
 * entry point for a checkout session with no owner attached. Verified live
 * against Stripe's test-mode API (not just this static check) on 2026-07-11:
 * all three plans (pro-monthly, pro-yearly, lifetime) returned a genuine
 * 303 redirect to a real https://checkout.stripe.com/... session.
 *
 * This is a static-analysis guard rather than a live HTTP test because the
 * .env.testing environment doesn't carry real Stripe price IDs (correctly —
 * hitting Stripe's live API from the automated suite would be flaky and
 * slow), so a request-based test here would only prove config plumbing, not
 * the actual bug. The live-API verification lives in session notes /
 * giga-brain, not in CI.
 */
test('guest checkout never uses the deprecated static User::checkout call', function () {
    $source = file_get_contents(app_path('Http/Controllers/CheckoutController.php'));

    expect($source)->not->toContain('User::checkout(');
    expect($source)->toContain('Checkout::guest()');
});

test('guest checkout only sends customer_creation for the payment-mode (lifetime) branch', function () {
    // Stripe rejects `customer_creation` outside `payment` mode with
    // "customer_creation can only be used in payment mode." — verified live
    // 2026-07-11. Guard against reintroducing it on the subscription branch.
    $source = file_get_contents(app_path('Http/Controllers/CheckoutController.php'));

    expect(substr_count($source, "'customer_creation'"))->toBe(1);
});
