# Laravel PayPal Package

[![Latest Version](https://img.shields.io/packagist/v/zaenalrfn/laravel-paypal.svg)](https://packagist.org/packages/zaenalrfn/laravel-paypal)
[![Total Downloads](https://img.shields.io/packagist/dt/zaenalrfn/laravel-paypal.svg)](https://packagist.org/packages/zaenalrfn/laravel-paypal)
[![License](https://img.shields.io/packagist/l/zaenalrfn/laravel-paypal.svg)](https://packagist.org/packages/zaenalrfn/laravel-paypal)

A modern, clean, and well-tested PayPal payment gateway integration for Laravel 11 & 12.

## Features

✅ **Simple & Intuitive API** - Fluent interface for creating orders and processing payments  
✅ **Webhook Support** - Secure webhook verification and event handling  
✅ **Type-Safe DTOs** - Immutable data transfer objects for better code quality  
✅ **Comprehensive Logging** - Dedicated PayPal log channel for debugging  
✅ **Test Coverage** - Well-tested codebase with Pest PHP  
✅ **Laravel 12 Ready** - Compatible with Laravel 11 and 12

## Installation

Install via Composer:

```bash
composer require zaenalrfn/laravel-paypal-checkout
```

## Configuration

### 1. Publish Configuration

```bash
php artisan vendor:publish --tag=paypal-config
```

This creates `config/paypal.php` with all available options.

### 2. Environment Variables

Add your PayPal credentials to `.env`:

```env
# PayPal Mode (sandbox or live)
PAYPAL_MODE=sandbox

# Sandbox Credentials
PAYPAL_SANDBOX_CLIENT_ID=your-sandbox-client-id
PAYPAL_SANDBOX_CLIENT_SECRET=your-sandbox-client-secret

# Production Credentials (when ready)
PAYPAL_LIVE_CLIENT_ID=your-live-client-id
PAYPAL_LIVE_CLIENT_SECRET=your-live-client-secret

# Webhook ID (get from PayPal Dashboard)
PAYPAL_WEBHOOK_ID=your-webhook-id

# Optional: Disable webhook verification in development
PAYPAL_VERIFY_WEBHOOK=true
```

### 3. Get PayPal Credentials

1. Go to [PayPal Developer Dashboard](https://developer.paypal.com/dashboard/)
2. Create an app or select existing one
3. Copy **Client ID** and **Secret**
4. For webhooks: Go to **Webhooks** → Create webhook → Copy **Webhook ID**

## Usage

### Creating a PayPal Order

```php
use Zaenalrfn\LaravelPayPal\Facades\PayPal;
use Zaenalrfn\LaravelPayPal\DTO\{CheckoutOrderData, PurchaseUnitData, AmountData};

// Build order data
$order = CheckoutOrderData::capture()
    ->addPurchaseUnit(
        new PurchaseUnitData(
            new AmountData('USD', '10.00'),
            'ORDER-123'  // Optional reference ID
        )
    )
    ->withReturnUrl('https://yoursite.com/payment/success')
    ->withCancelUrl('https://yoursite.com/payment/cancel');

// Create order with PayPal
$response = PayPal::checkout()->create($order);

// Get approval URL
$approvalUrl = collect($response['links'])
    ->firstWhere('rel', 'approve')['href'];

// Redirect user to PayPal
return redirect($approvalUrl);
```

### Capturing a Payment

After user approves payment on PayPal:

```php
use Zaenalrfn\LaravelPayPal\Facades\PayPal;

// Get order ID from PayPal callback
$orderId = $request->query('token');

// Capture the payment
$response = PayPal::checkout()->capture($orderId);

if ($response['status'] === 'COMPLETED') {
    // Payment successful!
    $captureId = $response['purchase_units'][0]['payments']['captures'][0]['id'];

    // Store in database, fulfill order, etc.
}
```

### Processing Refunds

```php
use Zaenalrfn\LaravelPayPal\Facades\PayPal;

$response = PayPal::payment()->refund($captureId, [
    'amount' => [
        'currency_code' => 'USD',
        'value' => '10.00'
    ],
    'note_to_payer' => 'Refund for order #123'
]);
```

## Webhook Setup

### 1. Create Webhook in PayPal Dashboard

1. Go to [PayPal Developer Dashboard](https://developer.paypal.com/dashboard/)
2. Select your app
3. Click **Webhooks** → **Add Webhook**
4. Webhook URL: `https://yoursite.com/api/paypal/webhook`
5. Select events:
    - `PAYMENT.CAPTURE.COMPLETED`
    - `PAYMENT.CAPTURE.DENIED`
    - `PAYMENT.CAPTURE.REFUNDED`
6. Save and copy the **Webhook ID**
7. Add to `.env`: `PAYPAL_WEBHOOK_ID=your-webhook-id`

### 2. Webhook Route

The package automatically registers the webhook route:

```
POST /api/paypal/webhook
```

### 3. Handle Webhook Events

Extend the `WebhookHandler` to customize event handling:

```php
namespace App\PayPal;

use Zaenalrfn\LaravelPayPal\Webhooks\WebhookHandler as BaseHandler;
use Zaenalrfn\LaravelPayPal\Support\PayPalLogger;

class CustomWebhookHandler extends BaseHandler
{
    protected function paymentCompleted(array $event): void
    {
        $captureId = $event['resource']['id'] ?? null;

        // Update your database
        \App\Models\Payment::where('capture_id', $captureId)
            ->update(['status' => 'completed']);

        PayPalLogger::info('Payment completed', ['capture_id' => $captureId]);
    }
}
```

Then bind it in your `AppServiceProvider`:

```php
$this->app->bind(
    \Zaenalrfn\LaravelPayPal\Webhooks\WebhookHandler::class,
    \App\PayPal\CustomWebhookHandler::class
);
```

## Testing

### Running Tests

```bash
cd packages/laravel-paypal
./vendor/bin/pest
```

### Development Mode

For local testing without valid webhook signatures:

```env
PAYPAL_VERIFY_WEBHOOK=false
```

**⚠️ WARNING:** Never disable webhook verification in production!

## Logging

All PayPal interactions are logged to a dedicated channel:

```
storage/logs/paypal-{date}.log
```

View logs:

```bash
tail -f storage/logs/paypal-$(date +%Y-%m-%d).log
```

## Troubleshooting

### Webhook 500 Error

**Problem:** Webhook returns 500 Internal Server Error

**Solutions:**

1. **Wrong Webhook ID** - Verify `PAYPAL_WEBHOOK_ID` matches PayPal Dashboard
2. **Environment Mismatch** - Sandbox webhook ID only works with sandbox credentials
3. **Development Testing** - Set `PAYPAL_VERIFY_WEBHOOK=false` for local testing

### Authentication Failed

**Problem:** "PayPal authentication failed" error

**Solutions:**

1. Verify `PAYPAL_SANDBOX_CLIENT_ID` and `PAYPAL_SANDBOX_CLIENT_SECRET` are correct
2. Check `PAYPAL_MODE` matches your credentials (sandbox vs live)
3. Ensure credentials are from the correct PayPal app

### Order Creation Failed

**Problem:** Order creation returns 400 error

**Solutions:**

1. Verify amount format: must be string with 2 decimals (e.g., "10.00")
2. Check currency code is valid (USD, EUR, GBP, etc.)
3. Ensure at least one purchase unit is added

## Security

- ✅ Webhook signature verification enabled by default
- ✅ No credentials in code (environment variables only)
- ✅ Dedicated exception handling with context
- ✅ Comprehensive logging for audit trails

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

## Credits

- [Zaenal Arifin](https://github.com/zaenalrfn)
- [All Contributors](../../contributors)

## Support

- **Issues:** [GitHub Issues](https://github.com/zaenalrfn/laravel-paypal/issues)
- **Documentation:** [PayPal API Docs](https://developer.paypal.com/docs/api/overview/)
