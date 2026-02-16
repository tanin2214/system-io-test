<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\Product;
use App\Exception\AppException;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;

class ProductService
{
    public function __construct(
        private readonly ProductRepository $productRepository,
    ) {
    }

    public function getProduct(int $id): Product
    {
        /** @var Product|null $product */
        $product = $this->productRepository->find($id);

        if (null === $product) {
            throw new AppException(
                userInterfaceMessage: sprintf('Продукт id = "%s" не существует', $id),
                httpStatusCode: Response::HTTP_BAD_REQUEST,
            );
        }

        return $product;
    }
}
