<?php

return [

    /*
    |--------------------------------------------------------------------------
    | PayPal Mode
    |--------------------------------------------------------------------------
    | Supported: "sandbox", "live"
    */
    'mode' => env('PAYPAL_MODE', 'sandbox'),

    /*
    |--------------------------------------------------------------------------
    | PayPal Credentials
    |--------------------------------------------------------------------------
    */
    'sandbox' => [
        'client_id' => env('PAYPAL_SANDBOX_CLIENT_ID'),
        'client_secret' => env('PAYPAL_SANDBOX_CLIENT_SECRET'),
        'base_uri' => 'https://api-m.sandbox.paypal.com',
    ],

    'live' => [
        'client_id' => env('PAYPAL_LIVE_CLIENT_ID'),
        'client_secret' => env('PAYPAL_LIVE_CLIENT_SECRET'),
        'base_uri' => 'https://api-m.paypal.com',
    ],

    /*
    |--------------------------------------------------------------------------
    | Webhook
    |--------------------------------------------------------------------------
    */
    'webhook_id' => env('PAYPAL_WEBHOOK_ID'),

    /*
    |--------------------------------------------------------------------------
    | Webhook Verification (Development)
    |--------------------------------------------------------------------------
    | Set to false to skip webhook signature verification in development.
    | WARNING: Never disable this in production!
    */
    'verify_webhook' => env('PAYPAL_VERIFY_WEBHOOK', true),

    /*
    |--------------------------------------------------------------------------
    | HTTP Options (Future-proof)
    |--------------------------------------------------------------------------
    */
    'http' => [
        'timeout' => 30,
        'retry' => 1,
    ],
];
