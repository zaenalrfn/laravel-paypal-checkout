<?php

namespace Zaenalrfn\LaravelPayPal\Checkout;

use Zaenalrfn\LaravelPayPal\Contracts\CheckoutContract;
use Zaenalrfn\LaravelPayPal\Http\PayPalHttpClient;
use Zaenalrfn\LaravelPayPal\DTO\CheckoutOrderData;

class CheckoutService implements CheckoutContract
{
    public function __construct(
        protected PayPalHttpClient $client
    ) {
    }

    /**
     * Create PayPal Order
     */
    public function create(array|CheckoutOrderData $payload): array
    {
        $data = $payload instanceof CheckoutOrderData
            ? $payload->toArray()
            : $payload;

        return $this->client->request(
            'POST',
            '/v2/checkout/orders',
            $data
        );
    }

    /**
     * Capture PayPal Order
     */
    public function capture(string $orderId): array
    {
        return $this->client->request(
            'POST',
            "/v2/checkout/orders/{$orderId}/capture"
        );
    }
}
