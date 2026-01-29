<?php

namespace Zaenalrfn\LaravelPayPal\DTO;

use InvalidArgumentException;

final class CheckoutOrderData
{
    private string $intent = 'CAPTURE';
    private array $purchaseUnits = [];
    private ?string $returnUrl = null;
    private ?string $cancelUrl = null;

    private function __construct()
    {
    }

    public static function capture(): self
    {
        return new self();
    }

    public static function authorize(): self
    {
        $self = new self();
        $self->intent = 'AUTHORIZE';
        return $self;
    }

    public function addPurchaseUnit(PurchaseUnitData $unit): self
    {
        $this->purchaseUnits[] = $unit;
        return $this;
    }

    public function returnUrl(string $url): self
    {
        $this->returnUrl = $url;
        return $this;
    }

    public function withReturnUrl(string $url): self
    {
        return $this->returnUrl($url);
    }

    public function cancelUrl(string $url): self
    {
        $this->cancelUrl = $url;
        return $this;
    }

    public function withCancelUrl(string $url): self
    {
        return $this->cancelUrl($url);
    }

    public function __get(string $name)
    {
        return $this->$name ?? null;
    }

    public function toArray(): array
    {
        if (empty($this->purchaseUnits)) {
            throw new InvalidArgumentException('At least one purchase unit is required');
        }

        return [
            'intent' => $this->intent,
            'purchase_units' => array_map(
                fn(PurchaseUnitData $unit) => $unit->toArray(),
                $this->purchaseUnits
            ),
            'application_context' => $this->returnUrl && $this->cancelUrl ? [
                'return_url' => $this->returnUrl,
                'cancel_url' => $this->cancelUrl,
            ] : null,
        ];
    }
}
