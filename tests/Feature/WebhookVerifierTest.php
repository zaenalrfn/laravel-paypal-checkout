<?php

use Illuminate\Support\Facades\Http;
use Zaenalrfn\LaravelPayPal\Webhooks\WebhookVerifier;
use Zaenalrfn\LaravelPayPal\Exceptions\PayPalException;

describe('WebhookVerifier', function () {
    it('verifies webhook signature successfully', function () {
        Http::fake([
            '*/v1/oauth2/token' => Http::response([
                'access_token' => 'test-token',
                'expires_in' => 3600,
            ], 200),
            '*/v1/notifications/verify-webhook-signature' => Http::response([
                'verification_status' => 'SUCCESS',
            ], 200),
        ]);

        $verifier = app(WebhookVerifier::class);

        $headers = [
            'paypal-auth-algo' => 'SHA256withRSA',
            'paypal-cert-url' => 'https://api.sandbox.paypal.com/v1/notifications/certs/CERT-123',
            'paypal-transmission-id' => 'transmission-123',
            'paypal-transmission-sig' => 'signature-123',
            'paypal-transmission-time' => now()->toIso8601String(),
        ];

        $rawBody = json_encode(['event_type' => 'PAYMENT.CAPTURE.COMPLETED']);

        $result = $verifier->verify($headers, $rawBody);

        expect($result)->toBeTrue();
    });

    it('throws exception when verification fails', function () {
        Http::fake([
            '*/v1/oauth2/token' => Http::response([
                'access_token' => 'test-token',
                'expires_in' => 3600,
            ], 200),
            '*/v1/notifications/verify-webhook-signature' => Http::response([
                'verification_status' => 'FAILURE',
            ], 200),
        ]);

        $verifier = app(WebhookVerifier::class);

        $headers = [
            'paypal-auth-algo' => 'SHA256withRSA',
            'paypal-cert-url' => 'https://api.sandbox.paypal.com/v1/notifications/certs/CERT-123',
            'paypal-transmission-id' => 'transmission-123',
            'paypal-transmission-sig' => 'invalid-signature',
            'paypal-transmission-time' => now()->toIso8601String(),
        ];

        $rawBody = json_encode(['event_type' => 'PAYMENT.CAPTURE.COMPLETED']);

        expect(fn() => $verifier->verify($headers, $rawBody))
            ->toThrow(PayPalException::class, 'Invalid PayPal webhook signature');
    });

    it('throws exception when header is missing', function () {
        $verifier = app(WebhookVerifier::class);

        $headers = [
            'paypal-auth-algo' => 'SHA256withRSA',
            'paypal-cert-url' => '',  // Missing
            'paypal-transmission-id' => 'transmission-123',
            'paypal-transmission-sig' => 'signature-123',
            'paypal-transmission-time' => now()->toIso8601String(),
        ];

        $rawBody = json_encode(['event_type' => 'PAYMENT.CAPTURE.COMPLETED']);

        expect(fn() => $verifier->verify($headers, $rawBody))
            ->toThrow(PayPalException::class);
    });
});
