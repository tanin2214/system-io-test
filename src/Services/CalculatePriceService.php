<?php

declare(strict_types=1);

namespace App\Services;

class CalculatePriceService
{
    public function __construct(
        private readonly CouponService $couponService,
        private readonly ProductService $productService,
        private readonly TaxService $taxService,
    ) {
    }

    public function calculatePrice(int $productId, string $taxNumber, ?string $couponCode): float
    {
        $product = $this->productService->getProduct(id: $productId);
        $priceAfterCoupon = $this->couponService->applyCoupon(price: $product->getPrice(), couponCode: $couponCode);

        $priceWithTax = $this->taxService->applyTax(price: $priceAfterCoupon, taxNumber: $taxNumber);

        return $priceWithTax;
    }
}
