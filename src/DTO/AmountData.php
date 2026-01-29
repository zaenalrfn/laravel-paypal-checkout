<?php

namespace Zaenalrfn\LaravelPayPal\DTO;

use InvalidArgumentException;

final class AmountData
{
    public function __construct(
        public readonly string $currency,
        public readonly string $value,
    ) {
        if (!is_numeric($value) || (float) $value <= 0) {
            throw new InvalidArgumentException('Amount must be greater than zero');
        }
    }

    public function toArray(): array
    {
        return [
            'currency_code' => strtoupper($this->currency),
            'value' => number_format((float) $this->value, 2, '.', ''),
        ];
    }

    public function __get(string $name)
    {
        if ($name === 'currencyCode') {
            return $this->currency;
        }
        return null;
    }
}
