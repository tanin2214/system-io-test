<?php

declare(strict_types=1);

namespace App\Services\PaymentProcessors;

use App\Enum\PaymentProcessorTypeEnum;
use Systemeio\TestForCandidates\PaymentProcessor\PaypalPaymentProcessor;

class PaypalProcessor implements PaymentProcessorInterface
{
    public function __construct(
        private readonly PaypalPaymentProcessor $paypalPaymentProcessor,
    ) {
    }

    public function support(PaymentProcessorTypeEnum $type): bool
    {
        return $type === PaymentProcessorTypeEnum::PAYPAL;
    }

    public function pay(float $price): bool
    {
        $success = false;

        try {
            $this->paypalPaymentProcessor->pay(price: (int) $price);

            $success = true;
        } catch (\Throwable $e) {
            //логируем ошибку
        }

        return $success;
    }
}
