<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function proMonthly(Request $request)
    {
        return $request->user()
            ->newSubscription('default', config('services.stripe.price_pro_monthly'))
            ->checkout([
                'success_url' => route('dashboard') . '?success=true',
                'cancel_url' => route('welcome'),
            ]);
    }

    public function lifetime(Request $request)
    {
        return $request->user()->checkout([config('services.stripe.price_lifetime')], [
            'success_url' => route('dashboard') . '?success=true',
            'cancel_url' => route('welcome'),
        ]);
    }

    public function portal(Request $request)
    {
        return $request->user()->redirectToBillingPortal(route('dashboard'));
    }
}
