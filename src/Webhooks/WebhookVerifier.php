<?php

namespace Zaenalrfn\LaravelPayPal\Webhooks;

use Zaenalrfn\LaravelPayPal\Http\PayPalHttpClient;
use Zaenalrfn\LaravelPayPal\Exceptions\PayPalException;

class WebhookVerifier
{
    public function __construct(
        protected PayPalHttpClient $client
    ) {
    }

    public function verify(array $headers, string $rawBody): bool
    {
        foreach ($headers as $key => $value) {
            if (empty($value)) {
                throw new PayPalException("Missing PayPal header: {$key}");
            }
        }

        $payload = [
            'auth_algo' => $headers['paypal-auth-algo'],
            'cert_url' => $headers['paypal-cert-url'],
            'transmission_id' => $headers['paypal-transmission-id'],
            'transmission_sig' => $headers['paypal-transmission-sig'],
            'transmission_time' => $headers['paypal-transmission-time'],
            'webhook_id' => config('paypal.webhook_id'),
            'webhook_event' => json_decode($rawBody, true),
        ];

        \Zaenalrfn\LaravelPayPal\Support\PayPalLogger::info('Webhook verification payload', [
            'webhook_id' => $payload['webhook_id'],
            'transmission_id' => $payload['transmission_id'],
        ]);

        $response = $this->client->request(
            'POST',
            '/v1/notifications/verify-webhook-signature',
            $payload
        );

        \Zaenalrfn\LaravelPayPal\Support\PayPalLogger::info('Webhook verification response', [
            'full_response' => $response,
        ]);

        if (($response['verification_status'] ?? null) !== 'SUCCESS') {
            \Zaenalrfn\LaravelPayPal\Support\PayPalLogger::error('Webhook verification failed', [
                'verification_status' => $response['verification_status'] ?? 'NULL',
                'response' => $response,
            ]);
            throw new PayPalException('Invalid PayPal webhook signature');
        }

        return true;
    }
}
