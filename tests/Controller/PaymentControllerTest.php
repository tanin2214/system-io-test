<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Controller\PaymentController;
use App\Dto\Request\CalculatePriceRequest;
use App\Dto\Request\PurchaseRequest;
use App\Enum\PaymentProcessorTypeEnum;
use App\Services\CalculatePriceService;
use App\Services\PurchaseService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class PaymentControllerTest extends TestCase
{
    public function testCalculatePriceReturnsJsonWithCalculatedPrice(): void
    {
        $service = $this->createMock(CalculatePriceService::class);
        $service->expects(self::once())
            ->method('calculatePrice')
            ->with(productId: 1, taxNumber: 'DE123456789', couponCode: 'COUPON')
            ->willReturn(123.45)
        ;

        $requestDto = new CalculatePriceRequest(product: 1, taxNumber: 'DE123456789', couponCode: 'COUPON');

        $controller = new class() extends PaymentController {
            /**
             * @param array<string, mixed> $headers
             * @param array<string, mixed> $context
             */
            public function json(
                mixed $data = null,
                int $status = 200,
                array $headers = [],
                array $context = [],
            ): JsonResponse {
                return new JsonResponse($data, $status, $headers);
            }
        };

        $response = $controller->calculatePrice($requestDto, $service);

        self::assertSame(200, $response->getStatusCode());
        /** @var string $content */
        $content = $response->getContent();
        self::assertJson($content);
        /** @var array<string, mixed> $data */
        $data = json_decode($content, true, 512, JSON_THROW_ON_ERROR);

        self::assertSame(123.45, $data['calculatedPrice']);
    }

    public function testPurchaseReturnsJsonWithStatus(): void
    {
        $purchaseService = $this->createMock(PurchaseService::class);
        $purchaseService->expects(self::once())
            ->method('purchase')
            ->with(
                productId: 1,
                taxNumber: 'DE123456789',
                couponCode: 'COUPON',
                paymentProcessorType: PaymentProcessorTypeEnum::PAYPAL,
            )
            ->willReturn(true)
        ;

        $requestDto = new PurchaseRequest(
            product: 1,
            taxNumber: 'DE123456789',
            paymentProcessor: PaymentProcessorTypeEnum::PAYPAL,
            couponCode: 'COUPON',
        );
        $controller = new class() extends PaymentController {
            /**
             * @param array<string, mixed> $headers
             * @param array<string, mixed> $context
             */
            public function json(
                mixed $data = null,
                int $status = 200,
                array $headers = [],
                array $context = [],
            ): JsonResponse {
                return new JsonResponse($data, $status, $headers);
            }
        };

        $response = $controller->purchase($requestDto, $purchaseService);

        self::assertSame(200, $response->getStatusCode());
        /** @var string $content */
        $content = $response->getContent();
        self::assertJson($content);
        /** @var array<string, mixed> $data */
        $data = json_decode($content, true, 512, JSON_THROW_ON_ERROR);

        self::assertArrayHasKey('status', $data);
        self::assertTrue($data['status']);
    }
}
