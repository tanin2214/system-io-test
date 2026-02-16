<?php

declare(strict_types=1);

namespace App\Services;

use App\Enum\PaymentProcessorTypeEnum;
use App\Services\PaymentProcessors\PaymentProcessorProvider;

class PurchaseService
{
    public function __construct(
        private readonly PaymentProcessorProvider $paymentProcessorProvider,
        private readonly CalculatePriceService $calculatePriceService,
    ) {
    }

    public function purchase(
        int $productId,
        string $taxNumber,
        ?string $couponCode,
        PaymentProcessorTypeEnum $paymentProcessorType,
    ): bool {
        $calculatedPrice = $this->calculatePriceService->calculatePrice(
            productId: $productId,
            taxNumber: $taxNumber,
            couponCode: $couponCode,
        );

        $processor = $this->paymentProcessorProvider->getPaymentProcessorByType(
            paymentProcessorType: $paymentProcessorType,
        );

        return $processor->pay(price: $calculatedPrice);
    }
}
