<?php

declare(strict_types=1);

namespace App\Services;

use App\Enum\CouponTypeEnum;
use App\Exception\AppException;
use App\Repository\CouponRepository;
use Symfony\Component\HttpFoundation\Response;

class CouponService
{
    public const MIN_PRICE = 1;

    public function __construct(
        private readonly CouponRepository $couponRepository,
    ) {
    }

    public function applyCoupon(int $price, ?string $couponCode): int
    {
        if (null === $couponCode) {
            return $price;
        }

        $coupon = $this->couponRepository->findByCode($couponCode);

        if (null === $coupon) {
            throw new AppException(
                userInterfaceMessage: sprintf('Купон "%s" не существует', $couponCode),
                httpStatusCode: Response::HTTP_BAD_REQUEST,
            );
        }

        return match ($coupon->getType()) {
            CouponTypeEnum::FIX => $this->calculateFixDiscount(price: $price, couponAmount: $coupon->getAmount()),
            CouponTypeEnum::PERCENT => $this->calculatePercentDiscount(
                price: $price,
                couponAmount: $coupon->getAmount(),
            ),
        };
    }

    private function calculateFixDiscount(int $price, int $couponAmount): int
    {
        if ($price < $couponAmount) {
            return self::MIN_PRICE;
        }

        return $price - $couponAmount;
    }

    private function calculatePercentDiscount(int $price, int $couponAmount): int
    {
        return intdiv($price * (100 - $couponAmount), 100);
    }
}
