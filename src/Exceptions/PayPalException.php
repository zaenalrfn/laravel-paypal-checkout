<?php

namespace Zaenalrfn\LaravelPayPal\Exceptions;

use RuntimeException;

class PayPalException extends RuntimeException
{
    protected array $context = [];

    public function withContext(array $context): self
    {
        $this->context = $context;
        return $this;
    }

    public function context(): array
    {
        return $this->context;
    }

    public function getContext(): array
    {
        return $this->context();
    }
}
