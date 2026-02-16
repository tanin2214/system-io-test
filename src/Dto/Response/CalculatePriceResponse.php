<?php

declare(strict_types=1);

namespace App\Dto\Response;

class CalculatePriceResponse
{
    public function __construct(
        public readonly float $calculatedPrice,
    ) {
    }
}
