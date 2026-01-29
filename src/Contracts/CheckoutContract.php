<?php

namespace Zaenalrfn\LaravelPayPal\Contracts;

interface CheckoutContract
{
    public function create(array $payload): array;

    public function capture(string $orderId): array;
}
