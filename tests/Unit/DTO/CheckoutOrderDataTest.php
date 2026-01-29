<?php

use Zaenalrfn\LaravelPayPal\DTO\CheckoutOrderData;
use Zaenalrfn\LaravelPayPal\DTO\PurchaseUnitData;
use Zaenalrfn\LaravelPayPal\DTO\AmountData;

describe('CheckoutOrderData', function () {
    it('can create capture order', function () {
        $order = CheckoutOrderData::capture();

        expect($order->intent)->toBe('CAPTURE');
    });

    it('can create authorize order', function () {
        $order = CheckoutOrderData::authorize();

        expect($order->intent)->toBe('AUTHORIZE');
    });

    it('can add purchase unit', function () {
        $amount = new AmountData('USD', '10.00');
        $purchaseUnit = new PurchaseUnitData($amount);

        $order = CheckoutOrderData::capture()
            ->addPurchaseUnit($purchaseUnit);

        expect($order->purchaseUnits)->toHaveCount(1);
        expect($order->purchaseUnits[0])->toBe($purchaseUnit);
    });

    it('can add multiple purchase units', function () {
        $amount1 = new AmountData('USD', '10.00');
        $amount2 = new AmountData('USD', '20.00');

        $order = CheckoutOrderData::capture()
            ->addPurchaseUnit(new PurchaseUnitData($amount1))
            ->addPurchaseUnit(new PurchaseUnitData($amount2));

        expect($order->purchaseUnits)->toHaveCount(2);
    });

    it('converts to array correctly', function () {
        $amount = new AmountData('USD', '10.00');
        $purchaseUnit = new PurchaseUnitData($amount);

        $order = CheckoutOrderData::capture()
            ->addPurchaseUnit($purchaseUnit);

        $array = $order->toArray();

        expect($array)->toHaveKey('intent');
        expect($array)->toHaveKey('purchase_units');
        expect($array['intent'])->toBe('CAPTURE');
        expect($array['purchase_units'])->toBeArray();
        expect($array['purchase_units'])->toHaveCount(1);
    });

    it('includes application context when set', function () {
        $amount = new AmountData('USD', '10.00');
        $purchaseUnit = new PurchaseUnitData($amount);

        $order = CheckoutOrderData::capture()
            ->addPurchaseUnit($purchaseUnit)
            ->withReturnUrl('https://example.com/return')
            ->withCancelUrl('https://example.com/cancel');

        $array = $order->toArray();

        expect($array)->toHaveKey('application_context');
        expect($array['application_context']['return_url'])->toBe('https://example.com/return');
        expect($array['application_context']['cancel_url'])->toBe('https://example.com/cancel');
    });
});
