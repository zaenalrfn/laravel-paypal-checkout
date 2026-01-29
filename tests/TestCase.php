<?php

namespace Zaenalrfn\LaravelPayPal\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Zaenalrfn\LaravelPayPal\LaravelPayPalServiceProvider;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            LaravelPayPalServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('paypal.mode', 'sandbox');
        $app['config']->set('paypal.sandbox.client_id', 'test-client-id');
        $app['config']->set('paypal.sandbox.client_secret', 'test-client-secret');
        $app['config']->set('paypal.sandbox.base_uri', 'https://api-m.sandbox.paypal.com');
        $app['config']->set('paypal.webhook_id', 'test-webhook-id');
        $app['config']->set('paypal.verify_webhook', true);
        $app['config']->set('paypal.http.timeout', 30);
        $app['config']->set('paypal.http.retry', 1);
    }

    protected function getEnvironmentSetUp($app): void
    {
        // Setup test environment if needed
    }
}
