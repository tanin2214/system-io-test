<?php

declare(strict_types=1);

namespace App\Tests\Services;

use App\Entity\CountryTax;
use App\Exception\AppException;
use App\Repository\CountryTaxRepository;
use App\Services\TaxService;
use PHPUnit\Framework\TestCase;

class TaxServiceTest extends TestCase
{
    public function testApplyTaxCalculatesTaxForKnownCountry(): void
    {
        $repository = $this->createMock(CountryTaxRepository::class);
        $countryTax = $this->getMockBuilder(CountryTax::class)
            ->onlyMethods(['getAmount'])
            ->getMock()
        ;

        $countryTax->method('getAmount')
            ->willReturn(24)
        ;

        $repository->expects(self::once())
            ->method('findByCode')
            ->with('GR')
            ->willReturn($countryTax)
        ;

        $service = new TaxService($repository);

        $result = $service->applyTax(100.0, 'GR123456789');

        self::assertSame(124.0, $result);
    }

    public function testApplyTaxThrowsExceptionWhenCountryNotFound(): void
    {
        $repository = $this->createMock(CountryTaxRepository::class);

        $repository->expects(self::once())
            ->method('findByCode')
            ->with('XX')
            ->willReturn(null)
        ;

        $service = new TaxService($repository);

        $this->expectException(AppException::class);
        $this->expectExceptionMessage('Налог с кодом "XX" не существует');

        $service->applyTax(100.0, 'XX123456789');
    }
}
