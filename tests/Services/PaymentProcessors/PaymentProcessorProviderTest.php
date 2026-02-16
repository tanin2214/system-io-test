<?php

declare(strict_types=1);

namespace App\Tests\Services\PaymentProcessors;

use App\Enum\PaymentProcessorTypeEnum;
use App\Exception\AppException;
use App\Services\PaymentProcessors\PaymentProcessorInterface;
use App\Services\PaymentProcessors\PaymentProcessorProvider;
use PHPUnit\Framework\TestCase;

class PaymentProcessorProviderTest extends TestCase
{
    public function testGetPaymentProcessorByTypeReturnsMatchingProcessor(): void
    {
        $paypalProcessor = $this->createMock(PaymentProcessorInterface::class);
        $paypalProcessor->method('support')
            ->willReturnCallback(
                static fn (PaymentProcessorTypeEnum $type): bool => $type === PaymentProcessorTypeEnum::PAYPAL,
            )
        ;

        $stripeProcessor = $this->createMock(PaymentProcessorInterface::class);
        $stripeProcessor->method('support')
            ->willReturnCallback(
                static fn (PaymentProcessorTypeEnum $type): bool => $type === PaymentProcessorTypeEnum::STRIPE,
            )
        ;

        $provider = new PaymentProcessorProvider([$paypalProcessor, $stripeProcessor]);

        self::assertSame(
            $paypalProcessor,
            $provider->getPaymentProcessorByType(PaymentProcessorTypeEnum::PAYPAL),
        );
    }

    public function testGetPaymentProcessorByTypeThrowsWhenNotFound(): void
    {
        $processor = $this->createMock(PaymentProcessorInterface::class);
        $processor->method('support')
            ->willReturn(false)
        ;

        $provider = new PaymentProcessorProvider([$processor]);

        $this->expectException(AppException::class);
        $this->expectExceptionMessage('Ошибка проведения платежа');

        $provider->getPaymentProcessorByType(PaymentProcessorTypeEnum::PAYPAL);
    }
}
