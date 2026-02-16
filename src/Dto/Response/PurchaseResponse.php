<?php

declare(strict_types=1);

namespace App\Dto\Response;

class PurchaseResponse
{
    public function __construct(
        public readonly int $calculatedPrice,
    ) {
    }
}
