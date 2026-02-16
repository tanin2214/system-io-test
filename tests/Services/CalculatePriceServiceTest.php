<?php

declare(strict_types=1);

namespace App\Tests\Services;

use App\Entity\Product;
use App\Services\CalculatePriceService;
use App\Services\CouponService;
use App\Services\ProductService;
use App\Services\TaxService;
use PHPUnit\Framework\TestCase;

class CalculatePriceServiceTest extends TestCase
{
    public function testCalculatePriceUsesServicesInCorrectOrder(): void
    {
        $product = $this->createConfiguredMock(Product::class, [
            'getPrice' => 100,
        ],);

        $productService = $this->createMock(ProductService::class);
        $productService->expects(self::once())
            ->method('getProduct')
            ->with(1)
            ->willReturn($product)
        ;

        $couponService = $this->createMock(CouponService::class);
        $couponService->expects(self::once())
            ->method('applyCoupon')
            ->with(100, 'COUPON')
            ->willReturn(80)
        ;

        $taxService = $this->createMock(TaxService::class);
        $taxService->expects(self::once())
            ->method('applyTax')
            ->with(80.0, 'DE123456789')
            ->willReturn(95.2)
        ;

        $service = new CalculatePriceService($couponService, $productService, $taxService);

        self::assertSame(95.2, $service->calculatePrice(1, 'DE123456789', 'COUPON'));
    }
}
