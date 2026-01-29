<?php

use Zaenalrfn\LaravelPayPal\Webhooks\WebhookHandler;

describe('WebhookHandler', function () {
    it('handles payment capture completed event', function () {
        $handler = app(WebhookHandler::class);

        $event = [
            'id' => 'WH-123',
            'event_type' => 'PAYMENT.CAPTURE.COMPLETED',
            'resource' => [
                'id' => 'CAPTURE-123',
                'status' => 'COMPLETED',
            ],
        ];

        // Should not throw exception
        $handler->handle($event);

        expect(true)->toBeTrue();
    });

    it('handles payment capture denied event', function () {
        $handler = app(WebhookHandler::class);

        $event = [
            'id' => 'WH-456',
            'event_type' => 'PAYMENT.CAPTURE.DENIED',
            'resource' => [
                'id' => 'CAPTURE-456',
                'status' => 'DENIED',
            ],
        ];

        $handler->handle($event);

        expect(true)->toBeTrue();
    });

    it('handles payment capture refunded event', function () {
        $handler = app(WebhookHandler::class);

        $event = [
            'id' => 'WH-789',
            'event_type' => 'PAYMENT.CAPTURE.REFUNDED',
            'resource' => [
                'id' => 'CAPTURE-789',
                'status' => 'REFUNDED',
            ],
        ];

        $handler->handle($event);

        expect(true)->toBeTrue();
    });

    it('handles unknown event type gracefully', function () {
        $handler = app(WebhookHandler::class);

        $event = [
            'id' => 'WH-999',
            'event_type' => 'UNKNOWN.EVENT.TYPE',
            'resource' => [],
        ];

        // Should not throw exception
        $handler->handle($event);

        expect(true)->toBeTrue();
    });

    it('handles event without event_type', function () {
        $handler = app(WebhookHandler::class);

        $event = [
            'id' => 'WH-000',
            'resource' => [],
        ];

        $handler->handle($event);

        expect(true)->toBeTrue();
    });
});
