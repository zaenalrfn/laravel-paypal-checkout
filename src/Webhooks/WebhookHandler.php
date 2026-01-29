<?php

namespace Zaenalrfn\LaravelPayPal\Webhooks;

use Zaenalrfn\LaravelPayPal\Support\PayPalLogger;

class WebhookHandler
{
    public function handle(array $event): void
    {
        $type = $event['event_type'] ?? null;

        PayPalLogger::info('Webhook received', [
            'event' => $type,
            'id' => $event['id'] ?? null,
        ]);

        match ($type) {
            'PAYMENT.CAPTURE.COMPLETED' => $this->paymentCompleted($event),
            'PAYMENT.CAPTURE.DENIED' => $this->paymentDenied($event),
            'PAYMENT.CAPTURE.REFUNDED' => $this->paymentRefunded($event),
            default => null,
        };
    }

    protected function paymentCompleted(array $event): void
    {
        $captureId = $event['resource']['id'] ?? null;

        // TODO: update database (idempotent)
        PayPalLogger::info('Payment completed', [
            'capture_id' => $captureId,
        ]);
    }

    protected function paymentDenied(array $event): void
    {
        PayPalLogger::error('Payment denied', [
            'event' => $event,
        ]);
    }

    protected function paymentRefunded(array $event): void
    {
        PayPalLogger::info('Payment refunded', [
            'event' => $event,
        ]);
    }
}
