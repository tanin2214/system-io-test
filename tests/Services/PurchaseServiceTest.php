<?php

declare(strict_types=1);

namespace App\Tests\Services;

use App\Enum\PaymentProcessorTypeEnum;
use App\Services\CalculatePriceService;
use App\Services\PaymentProcessors\PaymentProcessorInterface;
use App\Services\PaymentProcessors\PaymentProcessorProvider;
use App\Services\PurchaseService;
use PHPUnit\Framework\TestCase;

class PurchaseServiceTest extends TestCase
{
    public function testPurchaseDelegatesToPriceServiceAndProcessor(): void
    {
        $calculatePriceService = $this->createMock(CalculatePriceService::class);
        $calculatePriceService->expects(self::once())
            ->method('calculatePrice')
            ->with(1, 'DE123456789', 'COUPON')
            ->willReturn(120.0)
        ;

        $processor = $this->createMock(PaymentProcessorInterface::class);
        $processor->expects(self::once())
            ->method('pay')
            ->with(120.0)
            ->willReturn(true)
        ;

        $provider = $this->createMock(PaymentProcessorProvider::class);
        $provider->expects(self::once())
            ->method('getPaymentProcessorByType')
            ->with(PaymentProcessorTypeEnum::PAYPAL)
            ->willReturn($processor)
        ;

        $service = new PurchaseService($provider, $calculatePriceService);

        self::assertTrue(
            $service->purchase(
                productId: 1,
                taxNumber: 'DE123456789',
                couponCode: 'COUPON',
                paymentProcessorType: PaymentProcessorTypeEnum::PAYPAL,
            ),
        );
    }
}
