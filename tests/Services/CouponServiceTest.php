<?php

declare(strict_types=1);

namespace App\Tests\Services;

use App\Entity\Coupon;
use App\Enum\CouponTypeEnum;
use App\Exception\AppException;
use App\Repository\CouponRepository;
use App\Services\CouponService;
use PHPUnit\Framework\TestCase;

class CouponServiceTest extends TestCase
{
    public function testApplyCouponReturnsPriceWhenCouponIsNull(): void
    {
        $repository = $this->createMock(CouponRepository::class);
        $repository->expects(self::never())
            ->method('findByCode')
        ;

        $service = new CouponService($repository);

        self::assertSame(100, $service->applyCoupon(100, null));
    }

    public function testApplyCouponThrowsWhenCouponNotFound(): void
    {
        $repository = $this->createMock(CouponRepository::class);
        $repository->expects(self::once())
            ->method('findByCode')
            ->with('UNKNOWN')
            ->willReturn(null)
        ;

        $service = new CouponService($repository);

        $this->expectException(AppException::class);
        $this->expectExceptionMessage('Купон "UNKNOWN" не существует');

        $service->applyCoupon(100, 'UNKNOWN');
    }

    public function testApplyCouponFixTypeNormalDiscount(): void
    {
        $coupon = $this->createConfiguredMock(
            Coupon::class,
            [
                'getType' => CouponTypeEnum::FIX,
                'getAmount' => 10,
            ],
        );

        $repository = $this->createMock(CouponRepository::class);
        $repository->expects(self::once())
            ->method('findByCode')
            ->with('FIX10')
            ->willReturn($coupon)
        ;

        $service = new CouponService($repository);

        self::assertSame(90, $service->applyCoupon(100, 'FIX10'));
    }

    public function testApplyCouponFixTypeCannotGoBelowMinPrice(): void
    {
        $coupon = $this->createConfiguredMock(
            Coupon::class,
            [
                'getType' => CouponTypeEnum::FIX,
                'getAmount' => 50,
            ],
        );

        $repository = $this->createMock(CouponRepository::class);
        $repository->method('findByCode')
            ->willReturn($coupon)
        ;

        $service = new CouponService($repository);

        self::assertSame(CouponService::MIN_PRICE, $service->applyCoupon(10, 'FIX50'));
    }

    public function testApplyCouponPercentType(): void
    {
        $coupon = $this->createConfiguredMock(
            Coupon::class,
            [
                'getType' => CouponTypeEnum::PERCENT,
                'getAmount' => 25,
            ],
        );

        $repository = $this->createMock(CouponRepository::class);
        $repository->method('findByCode')
            ->willReturn($coupon)
        ;

        $service = new CouponService($repository);

        // 100 * (100 - 25) / 100 = 75
        self::assertSame(75, $service->applyCoupon(100, 'P25'));
    }
}
