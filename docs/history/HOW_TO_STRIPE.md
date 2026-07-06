# How to Add Stripe (5-Year-Old Edition) 🍭

## Step 1: Get Stripe Account

1. Go to https://stripe.com
2. Click "Sign up"
3. Enter your email
4. Make a password
5. Done! You have a Stripe account

## Step 2: Get Your Secret Keys

1. Log into Stripe dashboard
2. Click "Developers" (top right)
3. Click "API keys"
4. You'll see two keys:
   - **Publishable key** (starts with `pk_test_`)
   - **Secret key** (starts with `sk_test_`)
5. Click "Reveal test key" on the secret key
6. Copy both keys somewhere safe

## Step 3: Install Stripe in Laravel

```bash
cd ~/PLANNR/VALET/VHOSTS/markdown.observer
composer require laravel/cashier
php artisan vendor:publish --tag="cashier-migrations"
php artisan migrate
```

## Step 4: Add Keys to .env

Open `.env` file and add:

```env
STRIPE_KEY=pk_test_YOUR_KEY_HERE
STRIPE_SECRET=sk_test_YOUR_SECRET_HERE
STRIPE_WEBHOOK_SECRET=
```

## Step 5: Update User Model

Open `app/Models/User.php` and add:

```php
use Laravel\Cashier\Billable;

class User extends Authenticatable
{
    use Billable; // Add this line
    
    // ... rest of code
}
```

## Step 6: Create Stripe Products

1. Go to Stripe dashboard
2. Click "Products" (left menu)
3. Click "Add product"

### Create Pro Plan
- Name: "Pro"
- Price: $9.00
- Billing: Monthly
- Click "Save"
- Copy the **Price ID** (starts with `price_`)

### Create Lifetime Plan
- Name: "Lifetime"
- Price: $299.00
- Billing: One time
- Click "Save"
- Copy the **Price ID**

## Step 7: Add Price IDs to .env

```env
STRIPE_PRO_PRICE_ID=price_YOUR_PRO_ID
STRIPE_LIFETIME_PRICE_ID=price_YOUR_LIFETIME_ID
```

## Step 8: Create Checkout Controller

```bash
php artisan make:controller CheckoutController
```

Open `app/Http/Controllers/CheckoutController.php`:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function pro()
    {
        return auth()->user()
            ->newSubscription('default', config('services.stripe.pro_price_id'))
            ->checkout([
                'success_url' => route('dashboard'),
                'cancel_url' => route('pricing'),
            ]);
    }

    public function lifetime()
    {
        return auth()->user()
            ->checkout([config('services.stripe.lifetime_price_id')], [
                'success_url' => route('dashboard'),
                'cancel_url' => route('pricing'),
            ]);
    }
}
```

## Step 9: Add Routes

Open `routes/web.php` and add:

```php
Route::middleware(['auth'])->group(function () {
    Route::post('/checkout/pro', [CheckoutController::class, 'pro'])->name('checkout.pro');
    Route::post('/checkout/lifetime', [CheckoutController::class, 'lifetime'])->name('checkout.lifetime');
});
```

## Step 10: Update Pricing Page

Open `resources/js/pages/Pricing.tsx` and change buttons:

```tsx
// Pro button
<form method="POST" action={route('checkout.pro')}>
  <button type="submit">Upgrade to Pro</button>
</form>

// Lifetime button
<form method="POST" action={route('checkout.lifetime')}>
  <button type="submit">Get Lifetime Access</button>
</form>
```

## Step 11: Handle Webhooks

```bash
php artisan make:controller WebhookController
```

Open `app/Http/Controllers/WebhookController.php`:

```php
<?php

namespace App\Http\Controllers;

use Laravel\Cashier\Http\Controllers\WebhookController as CashierController;

class WebhookController extends CashierController
{
    public function handleCheckoutSessionCompleted($payload)
    {
        $user = User::find($payload['data']['object']['client_reference_id']);
        
        // Update user subscription tier
        if ($payload['data']['object']['mode'] === 'subscription') {
            $user->update(['subscription_tier' => 'pro']);
        } else {
            $user->update(['subscription_tier' => 'lifetime']);
        }
    }
}
```

Add route in `routes/web.php`:

```php
Route::post('/stripe/webhook', [WebhookController::class, 'handleWebhook']);
```

## Step 12: Set Up Webhook in Stripe

1. Go to Stripe dashboard
2. Click "Developers" → "Webhooks"
3. Click "Add endpoint"
4. URL: `https://markdown.observer/stripe/webhook`
5. Events to send:
   - `checkout.session.completed`
   - `customer.subscription.deleted`
6. Click "Add endpoint"
7. Copy the **Signing secret** (starts with `whsec_`)
8. Add to `.env`:
   ```env
   STRIPE_WEBHOOK_SECRET=whsec_YOUR_SECRET
   ```

## Step 13: Test It!

1. Visit `/pricing`
2. Click "Upgrade to Pro"
3. Enter test card: `4242 4242 4242 4242`
4. Any future date
5. Any CVC
6. Complete checkout
7. You should be redirected to dashboard
8. Check your subscription tier updated!

## For Laravel Cloud

Add these to environment variables:

```env
STRIPE_KEY=pk_live_YOUR_LIVE_KEY
STRIPE_SECRET=sk_live_YOUR_LIVE_SECRET
STRIPE_WEBHOOK_SECRET=whsec_YOUR_WEBHOOK_SECRET
STRIPE_PRO_PRICE_ID=price_YOUR_PRO_PRICE_ID
STRIPE_LIFETIME_PRICE_ID=price_YOUR_LIFETIME_PRICE_ID
```

## Test Cards

- Success: `4242 4242 4242 4242`
- Decline: `4000 0000 0000 0002`
- 3D Secure: `4000 0025 0000 3155`

## Done! 🎉

Your users can now pay you money!

## Troubleshooting

**"No such price"**
- Check your price IDs in .env
- Make sure they start with `price_`

**"Webhook failed"**
- Check webhook secret in .env
- Make sure URL is correct in Stripe

**"Payment not working"**
- Check you're using test keys (pk_test_, sk_test_)
- Use test card 4242 4242 4242 4242

## Need Help?

- Stripe docs: https://stripe.com/docs
- Laravel Cashier: https://laravel.com/docs/billing
- Discord: Ask in #laravel channel
