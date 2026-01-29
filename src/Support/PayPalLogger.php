<?php

namespace Zaenalrfn\LaravelPayPal\Support;

use Illuminate\Support\Facades\Log;

class PayPalLogger
{
    public static function info(string $message, array $context = []): void
    {
        Log::channel('paypal')->info($message, $context);
    }

    public static function error(string $message, array $context = []): void
    {
        Log::channel('paypal')->error($message, $context);
    }
}
