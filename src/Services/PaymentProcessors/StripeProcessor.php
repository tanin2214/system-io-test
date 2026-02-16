<?php

declare(strict_types=1);

namespace App\Services\PaymentProcessors;

use App\Enum\PaymentProcessorTypeEnum;
use Systemeio\TestForCandidates\PaymentProcessor\StripePaymentProcessor;

class StripeProcessor implements PaymentProcessorInterface
{
    public function __construct(
        private readonly StripePaymentProcessor $stripePaymentProcessor,
    ) {
    }

    public function support(PaymentProcessorTypeEnum $type): bool
    {
        return $type === PaymentProcessorTypeEnum::STRIPE;
    }

    public function pay(float $price): bool
    {
        return $this->stripePaymentProcessor->processPayment(price: $price);
    }
}
