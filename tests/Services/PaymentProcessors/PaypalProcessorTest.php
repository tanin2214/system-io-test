<?php

declare(strict_types=1);

namespace App\Tests\Services\PaymentProcessors;

use App\Enum\PaymentProcessorTypeEnum;
use App\Services\PaymentProcessors\PaypalProcessor;
use PHPUnit\Framework\TestCase;
use Systemeio\TestForCandidates\PaymentProcessor\PaypalPaymentProcessor;

class PaypalProcessorTest extends TestCase
{
    public function testSupportOnlyPaypal(): void
    {
        $inner = $this->createMock(PaypalPaymentProcessor::class);
        $processor = new PaypalProcessor($inner);

        self::assertTrue($processor->support(PaymentProcessorTypeEnum::PAYPAL));
        self::assertFalse($processor->support(PaymentProcessorTypeEnum::STRIPE));
    }

    public function testPayReturnsTrueOnSuccess(): void
    {
        $inner = $this->createMock(PaypalPaymentProcessor::class);
        $inner->expects(self::once())
            ->method('pay')
            ->with(100)
        ;

        $processor = new PaypalProcessor($inner);

        self::assertTrue($processor->pay(100.0));
    }

    public function testPayReturnsFalseOnException(): void
    {
        $inner = $this->createMock(PaypalPaymentProcessor::class);
        $inner->method('pay')
            ->willThrowException(new \Exception('fail'))
        ;

        $processor = new PaypalProcessor($inner);

        self::assertFalse($processor->pay(100.0));
    }
}
