<?php

declare(strict_types=1);

namespace App\Tests\ArgumentValueResolver;

use App\ArgumentValueResolver\CalculatePriceRequestResolver;
use App\ArgumentValueResolver\Tools\RequestValidator;
use App\Dto\Request\CalculatePriceRequest;
use App\Exception\RequestFormatException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\SerializerInterface;

class CalculatePriceRequestResolverTest extends TestCase
{
    public function testResolveReturnsEmptyIterableForOtherArgumentType(): void
    {
        $serializer = $this->createMock(SerializerInterface::class);
        $validator = $this->createMock(RequestValidator::class);

        $resolver = new CalculatePriceRequestResolver($serializer, $validator);

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

        $resolver = new CalculatePriceRequestResolver($serializer, $validator);

        $request = new Request(content: '{"invalid": "json"}');
        $argument = new ArgumentMetadata('arg', CalculatePriceRequest::class, false, false, null);

        $this->expectException(RequestFormatException::class);

        iterator_to_array($resolver->resolve($request, $argument));
    }

    public function testResolveThrowsRequestFormatExceptionWhenValidationFails(): void
    {
        $serializer = $this->createMock(SerializerInterface::class);
        $validator = $this->createMock(RequestValidator::class);

        $dto = new CalculatePriceRequest(product: 1, taxNumber: 'DE123456789', couponCode: 'ABC');

        $serializer->expects(self::once())
            ->method('deserialize')
            ->with('{"ok":true}', CalculatePriceRequest::class, 'json')
            ->willReturn($dto)
        ;

        $validator->expects(self::once())
            ->method('getErrorMsg')
            ->with($dto)
            ->willReturn('some error')
        ;

        $resolver = new CalculatePriceRequestResolver($serializer, $validator);

        $request = new Request(content: '{"ok":true}');
        $argument = new ArgumentMetadata('arg', CalculatePriceRequest::class, false, false, null);

        $this->expectException(RequestFormatException::class);
        $this->expectExceptionMessage('some error');

        iterator_to_array($resolver->resolve($request, $argument));
    }

    public function testResolveReturnsDtoWhenValid(): void
    {
        $serializer = $this->createMock(SerializerInterface::class);
        $validator = $this->createMock(RequestValidator::class);

        $dto = new CalculatePriceRequest(product: 1, taxNumber: 'DE123456789', couponCode: 'ABC');

        $serializer->expects(self::once())
            ->method('deserialize')
            ->with('{"ok":true}', CalculatePriceRequest::class, 'json')
            ->willReturn($dto)
        ;

        $validator->expects(self::once())
            ->method('getErrorMsg')
            ->with($dto)
            ->willReturn(null)
        ;

        $resolver = new CalculatePriceRequestResolver($serializer, $validator);

        $request = new Request(content: '{"ok":true}');
        $argument = new ArgumentMetadata('arg', CalculatePriceRequest::class, false, false, null);

        $result = iterator_to_array($resolver->resolve($request, $argument));

        self::assertCount(1, $result);
        self::assertSame($dto, $result[0]);
    }
}
