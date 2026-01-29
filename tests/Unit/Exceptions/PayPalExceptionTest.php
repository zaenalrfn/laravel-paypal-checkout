<?php

use Zaenalrfn\LaravelPayPal\Exceptions\PayPalException;

describe('PayPalException', function () {
    it('can be created with message', function () {
        $exception = new PayPalException('Test error message');

        expect($exception->getMessage())->toBe('Test error message');
    });

    it('can store context data', function () {
        $exception = new PayPalException('Test error');
        $exception->withContext(['key' => 'value']);

        expect($exception->getContext())->toBe(['key' => 'value']);
    });

    it('can chain context data', function () {
        $exception = (new PayPalException('Test error'))
            ->withContext(['stage' => 'oauth']);

        expect($exception->getContext())->toHaveKey('stage');
        expect($exception->getContext()['stage'])->toBe('oauth');
    });

    it('returns empty array when no context set', function () {
        $exception = new PayPalException('Test error');

        expect($exception->getContext())->toBe([]);
    });

    it('can be thrown and caught', function () {
        expect(function () {
            throw new PayPalException('Test error');
        })->toThrow(PayPalException::class, 'Test error');
    });
});
