<?php

namespace Zaenalrfn\LaravelPayPal\DTO;

final class PurchaseUnitData
{
    public function __construct(
        public readonly AmountData $amount,
        public readonly ?string $referenceId = null,
        public readonly ?string $description = null,
    ) {
    }

    public function toArray(): array
    {
        return array_filter([
            'reference_id' => $this->referenceId,
            'description' => $this->description,
            'amount' => $this->amount->toArray(),
        ]);
    }
}
