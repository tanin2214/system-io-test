<?php

declare(strict_types=1);

namespace App\Services;

use App\Exception\AppException;
use App\Repository\CountryTaxRepository;
use Symfony\Component\HttpFoundation\Response;

class TaxService
{
    public function __construct(
        private readonly CountryTaxRepository $countryTaxRepository,
    ) {
    }

    public function applyTax(float $price, string $taxNumber): float
    {
        $countryCode = substr($taxNumber, 0, 2);
        $countryTax = $this->countryTaxRepository->findByCode(countryCode: $countryCode);

        if (null === $countryTax) {
            throw new AppException(
                userInterfaceMessage: sprintf('Налог с кодом "%s" не существует', $countryCode),
                httpStatusCode: Response::HTTP_BAD_REQUEST,
            );
        }

        return $price + ($price * $countryTax->getAmount() / 100);
    }
}
