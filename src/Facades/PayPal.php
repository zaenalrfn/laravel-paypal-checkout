<?php

namespace Zaenalrfn\LaravelPayPal\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Zaenalrfn\LaravelPayPal\Checkout\CheckoutService checkout()
 * @method static \Zaenalrfn\LaravelPayPal\Payments\PaymentService payments()
 */
class PayPal extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'paypal';
    }
}
