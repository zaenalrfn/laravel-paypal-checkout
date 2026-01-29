<?php

namespace Zaenalrfn\LaravelPayPal;

use Illuminate\Support\ServiceProvider;
use Zaenalrfn\LaravelPayPal\Http\PayPalHttpClient;
use Zaenalrfn\LaravelPayPal\Checkout\CheckoutService;
use Zaenalrfn\LaravelPayPal\Contracts\CheckoutContract;
use Zaenalrfn\LaravelPayPal\Payments\PaymentService;
use Zaenalrfn\LaravelPayPal\Support\PayPalManager;

class LaravelPayPalServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Merge default config
        $this->mergeConfigFrom(
            __DIR__ . '/../config/paypal.php',
            'paypal'
        );

        $this->app->singleton(PayPalHttpClient::class);
        $this->app->singleton(
            CheckoutContract::class,
            CheckoutService::class
        );

        $this->app->singleton(CheckoutService::class);
        $this->app->singleton(PaymentService::class);

        $this->app->singleton('paypal', function ($app) {
            return new PayPalManager(
                $app->make(CheckoutService::class),
                $app->make(PaymentService::class)
            );
        });
    }

    public function boot(): void
    {
        // Publish config
        $this->publishes([
            __DIR__ . '/../config/paypal.php' => config_path('paypal.php'),
        ], 'paypal-config');
    }
}
