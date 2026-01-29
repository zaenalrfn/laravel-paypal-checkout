<?php

use Illuminate\Support\Facades\Http;
use Zaenalrfn\LaravelPayPal\Payments\PaymentService;
use Zaenalrfn\LaravelPayPal\Exceptions\PayPalException;

describe('PaymentService', function () {
    it('captures order successfully', function () {
        Http::fake([
            '*/v1/oauth2/token' => Http::response([
                'access_token' => 'test-access-token',
                'expires_in' => 3600,
            ], 200),
            '*/v2/checkout/orders/ORDER-123/capture' => Http::response([
                'id' => 'ORDER-123',
                'status' => 'COMPLETED',
                'purchase_units' => [
                    [
                        'payments' => [
                            'captures' => [
                                [
                                    'id' => 'CAPTURE-123',
                                    'status' => 'COMPLETED',
                                    'amount' => [
                                        'currency_code' => 'USD',
                                        'value' => '10.00',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $service = app(PaymentService::class);

        $response = $service->captureOrder('ORDER-123');

        expect($response)->toHaveKey('id');
        expect($response['id'])->toBe('ORDER-123');
        expect($response['status'])->toBe('COMPLETED');
    });

    it('throws exception when capture fails', function () {
        Http::fake([
            '*/v1/oauth2/token' => Http::response([
                'access_token' => 'test-access-token',
                'expires_in' => 3600,
            ], 200),
            '*/v2/checkout/orders/INVALID-ORDER/capture' => Http::response([
                'error' => 'RESOURCE_NOT_FOUND',
                'error_description' => 'Order not found',
            ], 404),
        ]);

        $service = app(PaymentService::class);

        expect(fn() => $service->captureOrder('INVALID-ORDER'))
            ->toThrow(PayPalException::class);
    });

    it('sends correct authorization header', function () {
        Http::fake([
            '*/v1/oauth2/token' => Http::response([
                'access_token' => 'test-access-token',
            ], 200),
            '*/v2/checkout/orders/*/capture' => Http::response([
                'id' => 'ORDER-123',
                'status' => 'COMPLETED',
            ], 200),
        ]);

        $service = app(PaymentService::class);
        $service->captureOrder('ORDER-123');

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/capture')
                && $request->hasHeader('Authorization')
                && str_contains($request->header('Authorization')[0], 'Bearer');
        });
    });
});
