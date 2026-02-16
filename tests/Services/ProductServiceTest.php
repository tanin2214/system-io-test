<?php

declare(strict_types=1);

namespace App\Tests\Services;

use App\Entity\Product;
use App\Exception\AppException;
use App\Repository\ProductRepository;
use App\Services\ProductService;
use PHPUnit\Framework\TestCase;

class ProductServiceTest extends TestCase
{
    public function testGetProductReturnsProduct(): void
    {
        $product = new Product();

        $repository = $this->createMock(ProductRepository::class);
        $repository->expects(self::once())
            ->method('find')
            ->with(1)
            ->willReturn($product)
        ;

        $service = new ProductService($repository);

        self::assertSame($product, $service->getProduct(1));
    }

    public function testGetProductThrowsWhenNotFound(): void
    {
        $repository = $this->createMock(ProductRepository::class);
        $repository->expects(self::once())
            ->method('find')
            ->with(42)
            ->willReturn(null)
        ;

        $service = new ProductService($repository);

        $this->expectException(AppException::class);
        $this->expectExceptionMessage('Продукт id = "42" не существует');

        $service->getProduct(42);
    }
}
