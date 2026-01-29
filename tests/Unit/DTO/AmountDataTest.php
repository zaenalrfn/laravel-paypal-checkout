<?php

use Zaenalrfn\LaravelPayPal\DTO\AmountData;

describe('AmountData', function () {
    it('can be created with currency and value', function () {
        $amount = new AmountData('USD', '10.00');

        expect($amount->currencyCode)->toBe('USD');
        expect($amount->value)->toBe('10.00');
    });

    it('converts to array correctly', function () {
        $amount = new AmountData('USD', '10.00');

        $array = $amount->toArray();

        expect($array)->toHaveKey('currency_code');
        expect($array)->toHaveKey('value');
        expect($array['currency_code'])->toBe('USD');
        expect($array['value'])->toBe('10.00');
    });

    it('supports different currencies', function () {
        $currencies = ['USD', 'EUR', 'GBP', 'JPY', 'IDR'];

        foreach ($currencies as $currency) {
            $amount = new AmountData($currency, '100.00');
            expect($amount->currencyCode)->toBe($currency);
        }
    });

    it('preserves decimal precision', function () {
        $amount = new AmountData('USD', '10.99');

        expect($amount->value)->toBe('10.99');
        expect($amount->toArray()['value'])->toBe('10.99');
    });
});
