<?php

declare(strict_types=1);

namespace App\Tests\ArgumentValueResolver;

use App\ArgumentValueResolver\PurchaseRequestResolverResolver;
use App\ArgumentValueResolver\Tools\RequestValidator;
use App\Dto\Request\PurchaseRequest;
use App\Enum\PaymentProcessorTypeEnum;
use App\Exception\RequestFormatException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\SerializerInterface;

class PurchaseRequestResolverResolverTest extends TestCase
{
    public function testResolveReturnsEmptyIterableForOtherArgumentType(): void
    {
        $serializer = $this->createMock(SerializerInterface::class);
        $validator = $this->createMock(RequestValidator::class);

        $resolver = new PurchaseRequestResolverResolver($serializer, $validator);

        $request = new Request();
        $argument = new ArgumentMetadata('arg', 'string', false, false, null);

        self::assertSame([], iterator_to_array($resolver->resolve($request, $argument)));
    }

    public function testResolveThrowsRequestFormatExceptionOnDeserializeError(): void
    {
        $serializer = $this->createMock(SerializerInterface::class);
        $validator = $this->createMock(RequestValidator::class);

        $serializer->expects(self::once())
            ->method('deserialize')
            ->willThrowException(new \RuntimeException('bad json'))
        ;

        $resolver = new PurchaseRequestResolverResolver($serializer, $validator);

        $request = new Request(content: '{"invalid": "json"}');
        $argument = new ArgumentMetadata('arg', PurchaseRequest::class, false, false, null);

        $this->expectException(RequestFormatException::class);

        iterator_to_array($resolver->resolve($request, $argument));
    }

    public function testResolveThrowsRequestFormatExceptionWhenValidationFails(): void
    {
        $serializer = $this->createMock(SerializerInterface::class);
        $validator = $this->createMock(RequestValidator::class);

        $dto = new PurchaseRequest(
            product: 1,
            taxNumber: 'DE123456789',
            paymentProcessor: PaymentProcessorTypeEnum::PAYPAL,
            couponCode: 'ABC',
        );

        $serializer->expects(self::once())
            ->method('deserialize')
            ->with('{"ok":true}', PurchaseRequest::class, 'json')
            ->willReturn($dto)
        ;

        $validator->expects(self::once())
            ->method('getErrorMsg')
            ->with($dto)
            ->willReturn('some error')
        ;

        $resolver = new PurchaseRequestResolverResolver($serializer, $validator);

        $request = new Request(content: '{"ok":true}');
        $argument = new ArgumentMetadata('arg', PurchaseRequest::class, false, false, null);

        $this->expectException(RequestFormatException::class);
        $this->expectExceptionMessage('some error');

        iterator_to_array($resolver->resolve($request, $argument));
    }

    public function testResolveReturnsDtoWhenValid(): void
    {
        $serializer = $this->createMock(SerializerInterface::class);
        $validator = $this->createMock(RequestValidator::class);

        $dto = new PurchaseRequest(
            product: 1,
            taxNumber: 'DE123456789',
            paymentProcessor: PaymentProcessorTypeEnum::PAYPAL,
            couponCode: 'ABC',
        );

        $serializer->expects(self::once())
            ->method('deserialize')
            ->with('{"ok":true}', PurchaseRequest::class, 'json')
            ->willReturn($dto)
        ;

        $validator->expects(self::once())
            ->method('getErrorMsg')
            ->with($dto)
            ->willReturn(null)
        ;

        $resolver = new PurchaseRequestResolverResolver($serializer, $validator);

        $request = new Request(content: '{"ok":true}');
        $argument = new ArgumentMetadata('arg', PurchaseRequest::class, false, false, null);

        $result = iterator_to_array($resolver->resolve($request, $argument));

        self::assertCount(1, $result);
        self::assertSame($dto, $result[0]);
    }
}
