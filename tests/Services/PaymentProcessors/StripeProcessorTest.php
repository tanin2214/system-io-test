<?php

declare(strict_types=1);

namespace App\Tests\Services\PaymentProcessors;

use App\Enum\PaymentProcessorTypeEnum;
use App\Services\PaymentProcessors\StripeProcessor;
use PHPUnit\Framework\TestCase;
use Systemeio\TestForCandidates\PaymentProcessor\StripePaymentProcessor;

class StripeProcessorTest extends TestCase
{
    public function testSupportOnlyStripe(): void
    {
        $inner = $this->createMock(StripePaymentProcessor::class);
        $processor = new StripeProcessor($inner);

        self::assertTrue($processor->support(PaymentProcessorTypeEnum::STRIPE));
        self::assertFalse($processor->support(PaymentProcessorTypeEnum::PAYPAL));
    }

    public function testPayDelegatesToStripeProcessor(): void
    {
        $inner = $this->createMock(StripePaymentProcessor::class);
        $inner->expects(self::once())
            ->method('processPayment')
            ->with(150.0)
            ->willReturn(true)
        ;

        $processor = new StripeProcessor($inner);

        self::assertTrue($processor->pay(150.0));
    }
}
