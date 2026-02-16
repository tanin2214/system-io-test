<?php

declare(strict_types=1);

namespace App\Services\PaymentProcessors;

use App\Enum\PaymentProcessorTypeEnum;

interface PaymentProcessorInterface
{
    public function support(PaymentProcessorTypeEnum $type): bool;

    public function pay(float $price): bool;
}
