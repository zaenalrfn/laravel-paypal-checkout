<?php

use Illuminate\Support\Facades\Http;
use Zaenalrfn\LaravelPayPal\Checkout\CheckoutService;
use Zaenalrfn\LaravelPayPal\DTO\CheckoutOrderData;
use Zaenalrfn\LaravelPayPal\DTO\PurchaseUnitData;
use Zaenalrfn\LaravelPayPal\DTO\AmountData;
use Zaenalrfn\LaravelPayPal\Exceptions\PayPalException;

describe('CheckoutService', function () {
    it('creates paypal order successfully', function () {
        Http::fake([
            '*/v1/oauth2/token' => Http::response([
                'access_token' => 'test-access-token',
                'expires_in' => 3600,
            ], 200),
            '*/v2/checkout/orders' => Http::response([
                'id' => 'ORDER-123',
                'status' => 'CREATED',
                'links' => [
                    [
                        'rel' => 'approve',
                        'href' => 'https://www.sandbox.paypal.com/checkoutnow?token=ORDER-123',
                    ],
                ],
            ], 201),
        ]);

        $service = app(CheckoutService::class);

        $order = CheckoutOrderData::capture()
            ->addPurchaseUnit(
                new PurchaseUnitData(
                    new AmountData('USD', '10.00')
                )
            );

        $response = $service->create($order);

        expect($response)->toHaveKey('id');
        expect($response['id'])->toBe('ORDER-123');
        expect($response['status'])->toBe('CREATED');
    });

    it('captures paypal order successfully', function () {
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
                                ],
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $service = app(CheckoutService::class);

        $response = $service->capture('ORDER-123');

        expect($response)->toHaveKey('id');
        expect($response['id'])->toBe('ORDER-123');
        expect($response['status'])->toBe('COMPLETED');
    });

    it('throws exception when order creation fails', function () {
        Http::fake([
            '*/v1/oauth2/token' => Http::response([
                'access_token' => 'test-access-token',
                'expires_in' => 3600,
            ], 200),
            '*/v2/checkout/orders' => Http::response([
                'error' => 'INVALID_REQUEST',
                'error_description' => 'Invalid request',
            ], 400),
        ]);

        $service = app(CheckoutService::class);

        $order = CheckoutOrderData::capture()
            ->addPurchaseUnit(
                new PurchaseUnitData(
                    new AmountData('USD', '10.00')
                )
            );

        expect(fn() => $service->create($order))
            ->toThrow(PayPalException::class);
    });

    it('sends correct payload to paypal api', function () {
        Http::fake([
            '*/v1/oauth2/token' => Http::response([
                'access_token' => 'test-access-token',
            ], 200),
            '*/v2/checkout/orders' => Http::response([
                'id' => 'ORDER-123',
                'status' => 'CREATED',
            ], 201),
        ]);

        $service = app(CheckoutService::class);

        $order = CheckoutOrderData::capture()
            ->addPurchaseUnit(
                new PurchaseUnitData(
                    new AmountData('USD', '25.50'),
                    'REF-123'
                )
            );

        $service->create($order);

        Http::assertSent(function ($request) {
            $body = json_decode($request->body(), true);

            return $request->url() === 'https://api-m.sandbox.paypal.com/v2/checkout/orders'
                && $body['intent'] === 'CAPTURE'
                && $body['purchase_units'][0]['amount']['value'] === '25.50'
                && $body['purchase_units'][0]['reference_id'] === 'REF-123';
        });
    });
});
