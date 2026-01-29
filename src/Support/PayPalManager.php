<?php

namespace Zaenalrfn\LaravelPayPal\Support;

use Zaenalrfn\LaravelPayPal\Checkout\CheckoutService;
use Zaenalrfn\LaravelPayPal\Payments\PaymentService;

class PayPalManager
{
    public function __construct(
        protected CheckoutService $checkout,
        protected PaymentService $payments
    ) {
    }

    public function checkout(): CheckoutService
    {
        return $this->checkout;
    }

    public function payments(): PaymentService
    {
        return $this->payments;
    }
}
