<?php

namespace Zaenalrfn\LaravelPayPal\Payments;

use Zaenalrfn\LaravelPayPal\Http\PayPalHttpClient;

class PaymentService
{
    public function __construct(
        protected PayPalHttpClient $client
    ) {
    }

    public function captureOrder(string $orderId): array
    {
        return $this->client->request(
            'POST',
            "/v2/checkout/orders/{$orderId}/capture"
        );
    }

    public function refund(string $captureId, array $payload = []): array
    {
        return $this->client->request(
            'POST',
            "/v2/payments/captures/{$captureId}/refund",
            $payload
        );
    }
}
