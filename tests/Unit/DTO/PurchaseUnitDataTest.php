<?php

use Zaenalrfn\LaravelPayPal\DTO\PurchaseUnitData;
use Zaenalrfn\LaravelPayPal\DTO\AmountData;

describe('PurchaseUnitData', function () {
    it('can be created with amount only', function () {
        $amount = new AmountData('USD', '10.00');
        $purchaseUnit = new PurchaseUnitData($amount);

        expect($purchaseUnit->amount)->toBe($amount);
        expect($purchaseUnit->referenceId)->toBeNull();
    });

    it('can be created with reference id', function () {
        $amount = new AmountData('USD', '10.00');
        $purchaseUnit = new PurchaseUnitData($amount, 'ORDER-123');

        expect($purchaseUnit->amount)->toBe($amount);
        expect($purchaseUnit->referenceId)->toBe('ORDER-123');
    });

    it('converts to array correctly without reference id', function () {
        $amount = new AmountData('USD', '10.00');
        $purchaseUnit = new PurchaseUnitData($amount);

        $array = $purchaseUnit->toArray();

        expect($array)->toHaveKey('amount');
        expect($array)->not->toHaveKey('reference_id');
        expect($array['amount'])->toBeArray();
    });

    it('converts to array correctly with reference id', function () {
        $amount = new AmountData('USD', '10.00');
        $purchaseUnit = new PurchaseUnitData($amount, 'ORDER-123');

        $array = $purchaseUnit->toArray();

        expect($array)->toHaveKey('amount');
        expect($array)->toHaveKey('reference_id');
        expect($array['reference_id'])->toBe('ORDER-123');
    });

    it('includes nested amount data in array', function () {
        $amount = new AmountData('USD', '10.00');
        $purchaseUnit = new PurchaseUnitData($amount);

        $array = $purchaseUnit->toArray();

        expect($array['amount'])->toHaveKey('currency_code');
        expect($array['amount'])->toHaveKey('value');
        expect($array['amount']['currency_code'])->toBe('USD');
        expect($array['amount']['value'])->toBe('10.00');
    });
});
