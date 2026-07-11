<?php

use App\Models\User;

test('checkout.session.completed for a one-time payment grants lifetime tier', function () {
    $user = User::factory()->create([
        'stripe_id' => 'cus_lifetime_test',
        'subscription_tier' => 'free',
    ]);

    $this->postJson('stripe/webhook', [
        'id' => 'evt_lifetime',
        'type' => 'checkout.session.completed',
        'data' => [
            'object' => [
                'id' => 'cs_test_lifetime',
                'mode' => 'payment',
                'payment_status' => 'paid',
                'customer' => 'cus_lifetime_test',
                'customer_details' => ['email' => $user->email],
            ],
        ],
    ])->assertOk();

    $user->refresh();
    expect($user->subscription_tier)->toBe('lifetime');
    expect($user->upload_limit)->toBe(999);
    expect($user->doc_limit)->toBe(999);
});

test('checkout.session.completed falls back to matching by email for a guest checkout', function () {
    // Simulates the webhook racing ahead of CheckoutController@success —
    // the user exists (created moments ago by the success redirect) but
    // has no stripe_id recorded yet.
    $user = User::factory()->create([
        'stripe_id' => null,
        'subscription_tier' => 'free',
    ]);

    $this->postJson('stripe/webhook', [
        'id' => 'evt_lifetime_guest',
        'type' => 'checkout.session.completed',
        'data' => [
            'object' => [
                'id' => 'cs_test_guest',
                'mode' => 'payment',
                'payment_status' => 'paid',
                'customer' => 'cus_guest_new',
                'customer_details' => ['email' => $user->email],
            ],
        ],
    ])->assertOk();

    $user->refresh();
    expect($user->subscription_tier)->toBe('lifetime');
    expect($user->stripe_id)->toBe('cus_guest_new');
});

test('checkout.session.completed ignores subscription-mode sessions', function () {
    $user = User::factory()->create([
        'stripe_id' => 'cus_sub_mode',
        'subscription_tier' => 'free',
    ]);

    $this->postJson('stripe/webhook', [
        'id' => 'evt_sub_mode',
        'type' => 'checkout.session.completed',
        'data' => [
            'object' => [
                'id' => 'cs_test_sub',
                'mode' => 'subscription',
                'payment_status' => 'paid',
                'customer' => 'cus_sub_mode',
            ],
        ],
    ])->assertOk();

    // customer.subscription.created is the event that grants pro, not this one
    $user->refresh();
    expect($user->subscription_tier)->toBe('free');
});

test('customer.subscription.created grants pro tier', function () {
    $user = User::factory()->create([
        'stripe_id' => 'cus_pro_test',
        'subscription_tier' => 'free',
    ]);

    $this->postJson('stripe/webhook', [
        'id' => 'evt_pro',
        'type' => 'customer.subscription.created',
        'data' => [
            'object' => [
                'id' => 'sub_pro_test',
                'customer' => 'cus_pro_test',
                'cancel_at_period_end' => false,
                'quantity' => 1,
                'items' => [
                    'data' => [[
                        'id' => 'si_pro_test',
                        'price' => ['id' => 'price_pro_monthly', 'product' => 'prod_pro'],
                        'quantity' => 1,
                    ]],
                ],
                'status' => 'active',
            ],
        ],
    ])->assertOk();

    $user->refresh();
    expect($user->subscription_tier)->toBe('pro');
    expect($user->upload_limit)->toBe(999);
    expect($user->doc_limit)->toBe(100);

    // Cashier's own bookkeeping still happens — this wasn't skipped.
    $this->assertDatabaseHas('subscriptions', [
        'user_id' => $user->id,
        'stripe_id' => 'sub_pro_test',
        'stripe_status' => 'active',
    ]);
});

test('customer.subscription.created never downgrades an existing lifetime purchaser', function () {
    $user = User::factory()->create([
        'stripe_id' => 'cus_lifetime_then_sub',
        'subscription_tier' => 'lifetime',
        'upload_limit' => 999,
        'doc_limit' => 999,
    ]);

    $this->postJson('stripe/webhook', [
        'id' => 'evt_pro_after_lifetime',
        'type' => 'customer.subscription.created',
        'data' => [
            'object' => [
                'id' => 'sub_after_lifetime',
                'customer' => 'cus_lifetime_then_sub',
                'cancel_at_period_end' => false,
                'quantity' => 1,
                'items' => [
                    'data' => [[
                        'id' => 'si_after_lifetime',
                        'price' => ['id' => 'price_pro_monthly', 'product' => 'prod_pro'],
                        'quantity' => 1,
                    ]],
                ],
                'status' => 'active',
            ],
        ],
    ])->assertOk();

    $user->refresh();
    expect($user->subscription_tier)->toBe('lifetime');
    expect($user->doc_limit)->toBe(999);
});

test('customer.subscription.deleted downgrades a pro user back to free', function () {
    $user = User::factory()->create([
        'stripe_id' => 'cus_cancel_test',
        'subscription_tier' => 'pro',
        'upload_limit' => 999,
        'doc_limit' => 100,
    ]);

    $user->subscriptions()->create([
        'type' => 'default',
        'stripe_id' => 'sub_cancel_test',
        'stripe_status' => 'active',
        'stripe_price' => 'price_pro_monthly',
        'quantity' => 1,
    ]);

    $this->postJson('stripe/webhook', [
        'id' => 'evt_cancel',
        'type' => 'customer.subscription.deleted',
        'data' => [
            'object' => [
                'id' => 'sub_cancel_test',
                'customer' => 'cus_cancel_test',
                'status' => 'canceled',
            ],
        ],
    ])->assertOk();

    $user->refresh();
    expect($user->subscription_tier)->toBe('free');
    expect($user->upload_limit)->toBe(2);
    expect($user->doc_limit)->toBe(10);
});

test('customer.subscription.deleted never touches a lifetime purchaser', function () {
    $user = User::factory()->create([
        'stripe_id' => 'cus_lifetime_immune',
        'subscription_tier' => 'lifetime',
        'upload_limit' => 999,
        'doc_limit' => 999,
    ]);

    $this->postJson('stripe/webhook', [
        'id' => 'evt_cancel_lifetime',
        'type' => 'customer.subscription.deleted',
        'data' => [
            'object' => [
                'id' => 'sub_irrelevant',
                'customer' => 'cus_lifetime_immune',
                'status' => 'canceled',
            ],
        ],
    ])->assertOk();

    $user->refresh();
    expect($user->subscription_tier)->toBe('lifetime');
});
